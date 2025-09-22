<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use App\Models\Material;
use App\Models\Address;

class StoreRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'request_type' => 'required|in:Donation,Recycling',
            'pickup_address_id' => 'required|integer|exists:addresses,address_id',
            'pickup_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
                'before_or_equal:' . now()->addMonths(3)->format('Y-m-d'),
                function ($attribute, $value, $fail) {
                    // Parse as date-only in app timezone and compare from start of day
                    $date = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();

                    // Check if pickup date is not on Fridays (weekend rule)
                    if ($date->isFriday()) {
                        $fail('Pickup cannot be scheduled on Fridays.');
                    }

                    // Check if pickup date is not in the past
                    if ($date->lessThan(now()->startOfDay())) {
                        $fail('Pickup date cannot be in the past.');
                    }

                    // Ensure at least 48 hours (2 days) advance from now
                    if ($date->lessThan(now()->addDays(2)->startOfDay())) {
                        $fail('Pickup must be scheduled at least 48 hours in advance.');
                    }
                }
            ],
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|integer|exists:materials,material_id',
            'materials.*.quantity' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $user = auth()->user();
                    $materialIndex = explode('.', $attribute)[1];
                    $materialId = $this->input("materials.{$materialIndex}.material_id");
                    $material = Material::find($materialId);

                    if (!$material) {
                        return;
                    }

                    // Normalize numeric value
                    if (!is_numeric($value)) {
                        $fail('Quantity must be a valid number.');
                        return;
                    }

                    $qty = (float) $value;
                    $unit = $material->default_unit ?? null; // schema uses default_unit

                    // Validate against material stock (schema changed to include stock)
                    if (isset($material->stock) && $material->stock < $qty) {
                        $fail("Only {$material->stock} {$unit} available for '{$material->material_name}'.");
                        return;
                    }

                    // Role-based rules: use role field on user
                    $role = $user->role ?? null;

                    // Rules for 'item' unit - must be whole numbers
                    if ($unit === 'item') {
                        if ($qty < 1) {
                            $fail('Minimum quantity per item is 1.');
                        }
                        if (floor($qty) != $qty) {
                            $fail('Quantity for items must be a whole number.');
                        }
                    }

                    // kilogram rules
                    if ($unit === 'kg') {
                        if ($role === 'user' && $qty < 1) {
                            $fail('For users, minimum quantity is 1 kg.');
                        }
                        if ($role === 'factory' && $qty < 100) {
                            $fail('For factories, minimum quantity is 100 kg (0.1 ton).');
                        }
                    }
                }
            ],
            'notes' => 'nullable|string|max:1000',
            'contact_phone' => 'required|string|regex:/^[0-9+\-\s()]+$/|max:20'
        ];
    }

    public function messages(): array
    {
        return [
            // Request type validation messages
            'request_type.required' => 'Please select a request type (Donation or Recycling).',
            'request_type.in' => 'Request type must be either Donation or Recycling.',

            // Address validation messages
            'pickup_address_id.required' => 'Please select a pickup address.',
            'pickup_address_id.integer' => 'Invalid pickup address selected.',
            'pickup_address_id.exists' => 'The selected pickup address does not exist.',

            // Pickup date validation messages
            'pickup_date.required' => 'Please select a pickup date.',
            'pickup_date.date' => 'Please enter a valid date.',
            'pickup_date.after_or_equal' => 'Pickup date cannot be in the past.',
            'pickup_date.before_or_equal' => 'Pickup date cannot be more than 3 months in advance.',

            // Materials validation messages
            'materials.required' => 'You must include at least one material in your request.',
            'materials.array' => 'Materials must be provided as a list.',
            'materials.min' => 'You must include at least one material.',
            'materials.*.material_id.required' => 'Each material must have a valid material ID.',
            'materials.*.material_id.integer' => 'Material ID must be a valid number.',
            'materials.*.material_id.exists' => 'One or more selected materials do not exist.',
            'materials.*.quantity.required' => 'Please specify the quantity for each material.',
            'materials.*.quantity.numeric' => 'Quantity must be a valid number.',

            // Optional fields validation messages
            'notes.string' => 'Notes must be text.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'contact_phone.required' => 'Contact phone must be included.',
            'contact_phone.string' => 'Contact phone must be text.',
            'contact_phone.regex' => 'Please enter a valid phone number.',
            'contact_phone.max' => 'Contact phone cannot exceed 20 characters.'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateAddressOwnership($validator);
            $this->validateMaterialAvailability($validator);
            $this->validateDuplicateMaterials($validator);
        });
    }

    /**
     * Validate that the pickup address belongs to the authenticated user
     */
    protected function validateAddressOwnership($validator)
    {
        $pickupAddressId = $this->input('pickup_address_id');

        if ($pickupAddressId) {
            $address = Address::find($pickupAddressId);

            if ($address && $address->user_id !== auth()->id()) {
                $validator->errors()->add('pickup_address_id', 'You can only use your own addresses for pickup.');
            }
        }
    }

    /**
     * Validate that materials are available and active
     */
    protected function validateMaterialAvailability($validator)
    {
        $materials = $this->input('materials', []);

        foreach ($materials as $index => $material) {
            if (isset($material['material_id'])) {
                $materialModel = Material::find($material['material_id']);

                if (!$materialModel) {
                    $validator->errors()->add("materials.{$index}.material_id", "Material with ID {$material['material_id']} does not exist.");
                    continue;
                }

                // ensure material is available and has a positive price
                if ($materialModel->price_per_unit <= 0) {
                    $validator->errors()->add("materials.{$index}.material_id", "Material '{$materialModel->material_name}' is currently not available for requests.");
                    continue;
                }

                // if stock exists check requested quantity against stock
                $reqQty = isset($material['quantity']) && is_numeric($material['quantity']) ? (float)$material['quantity'] : null;
                if ($reqQty !== null && isset($materialModel->stock) && $materialModel->stock < $reqQty) {
                    $validator->errors()->add("materials.{$index}.quantity", "Only {$materialModel->stock} {$materialModel->default_unit} available for '{$materialModel->material_name}'.");
                }
            }
        }
    }

    /**
     * Validate that no duplicate materials are included in the request
     */
    protected function validateDuplicateMaterials($validator)
    {
        $materials = $this->input('materials', []);
        $materialIds = array_column($materials, 'material_id');

        if (count($materialIds) !== count(array_unique($materialIds))) {
            $validator->errors()->add('materials', 'You cannot include the same material multiple times in one request.');
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'request_type' => 'request type',
            'pickup_address_id' => 'pickup address',
            'pickup_date' => 'pickup date',
            'materials.*.material_id' => 'material',
            'materials.*.quantity' => 'quantity',
            'contact_phone' => 'contact phone',
        ];
    }
}
