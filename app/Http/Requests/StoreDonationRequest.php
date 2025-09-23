<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'user_id' => 'required|exists:users,user_id',
        'pickup_address_id' => 'required|exists:addresses,address_id',
        'item_category' => 'required|in:clothing,electronics,furniture,books,toys,household,other',
        'condition' => 'required|in:excellent,good,fair,needs-repair',
        'description' => 'required|string|max:2000',
        'pickup_date' => [
            'required',
            'date',
            'after_or_equal:today',
        ],
        'additional_notes' => 'nullable|string|max:1000',
        'photos' => 'required|array|min:1|max:5',
        'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
     ];
    }
}
