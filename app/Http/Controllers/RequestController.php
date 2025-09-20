<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Request as RecyclingRequest;
use App\Models\RequestItem;
use App\Models\Material;
use App\Models\Invoice;
use App\Models\User;
use App\Jobs\SendRequestConfirmationEmail;
use App\Jobs\SendCollectorAssignedEmail;
use App\Jobs\SendCompletionInvoiceEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Http\Requests\StoreRequestRequest;

class RequestController extends Controller
{
    /**
     * Display a listing of the requests.
     */
    public function index(Request $request)
    {
        $query = RecyclingRequest::with(['customer', 'collector', 'pickupAddress', 'requestItems.material']);

        // Apply filters if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('request_type', $request->type);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $recyclingRequests = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $recyclingRequests
        ]);
    }

    /**
     * Store a newly created request in storage.
     */
    public function store(StoreRequestRequest $request)
    {
        try {
            DB::beginTransaction();

            $recyclingRequest = new RecyclingRequest();
            $recyclingRequest->request_type = $request->request_type;
            $recyclingRequest->pickup_date = $request->pickup_date;
            $recyclingRequest->pickup_address_id = $request->pickup_address_id;
            $recyclingRequest->customer_id = Auth::id();
            $recyclingRequest->status = 'Pending';
            // $recyclingRequest->phone = $request->contact_phone;
            $recyclingRequest->save();

            $totalValue = 0;
            foreach ($request->materials as $item) {
                $material = Material::findOrFail($item['material_id']);
                $calculatedPrice = $material->price_per_unit * $item['quantity'];
                $totalValue += $calculatedPrice;

                RequestItem::create([
                    'request_id' => $recyclingRequest->request_id,
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'calculated_price' => $calculatedPrice
                ]);
            }

            DB::commit();

            // Email job
            $recyclingRequest->load(['customer', 'pickupAddress', 'requestItems.material']);
            SendRequestConfirmationEmail::dispatch($recyclingRequest);

            return response()->json([
                'success' => true,
                'message' => 'Recycling request created successfully',
                'data' => $recyclingRequest
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create recycling request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified request.
     */
    public function show($id)
    {
        $recyclingRequest = RecyclingRequest::with([
            'customer',
            'collector',
            'pickupAddress',
            'requestItems.material',
            'invoice'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $recyclingRequest
        ]);
    }

    /**
     * Update the specified request status.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pending,Assigned,Completed,Canceled',
            'collector_id' => 'required_if:status,Assigned|exists:users,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $recyclingRequest = RecyclingRequest::findOrFail($id);
            $oldStatus = $recyclingRequest->status;
            $recyclingRequest->status = $request->status;

            // If status is changing to Assigned, set the collector
            if ($request->status === 'Assigned' && $request->has('collector_id')) {
                $recyclingRequest->collector_id = $request->collector_id;

                // Send email notification about collector assignment
                SendCollectorAssignedEmail::dispatch($recyclingRequest);
            }

            // If status is changing to Completed, generate invoice
            if ($request->status === 'Completed' && $oldStatus !== 'Completed') {
                // Calculate total value
                $totalValue = $recyclingRequest->requestItems->sum('calculated_price');

                // Create invoice
                $invoice = new Invoice();
                $invoice->request_id = $recyclingRequest->request_id;
                $invoice->invoice_date = now();
                $invoice->total_amount = $totalValue;
                $invoice->save();

                // Generate and send invoice PDF
                SendCompletionInvoiceEmail::dispatch($recyclingRequest, $invoice);
            }

            $recyclingRequest->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Request status updated successfully',
                'data' => $recyclingRequest->load(['customer', 'collector', 'pickupAddress', 'requestItems.material', 'invoice'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update request status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all requests for the authenticated user.
     */
    public function getUserRequests()
    {
        $userId = Auth::id();
        $requests = RecyclingRequest::with(['pickupAddress', 'requestItems.material', 'invoice'])
            ->where('customer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * Get all requests assigned to the authenticated collector.
     */
    public function getCollectorAssignments()
    {
        $userId = Auth::id();
        $assignments = RecyclingRequest::with(['customer', 'pickupAddress', 'requestItems.material'])
            ->where('collector_id', $userId)
            ->orderBy('pickup_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }

    /**
     * Public: return materials for frontend
     */
    public function getMaterials(Request $request)
    {
        $materials = Material::with('category')->get()->map(function ($m) {
            return [
                'material_id'    => $m->material_id,
                'material_name'  => $m->material_name,
                'description'    => $m->description,
                'price_per_unit' => (float) $m->price_per_unit,
                'default_unit'   => $m->default_unit,
                'units'          => $m->units ?? [],
                'image_url'      => $m->image_url,
                'category_id'    => $m->category_id,
                'category'       => $m->category ? [
                    'category_id'   => $m->category->category_id,
                    'category_name' => $m->category->category_name,
                    'slug'          => $m->category->slug,
                    'image_url'     => $m->category->image_url,
                ] : null,
            ];
        });

        return response()->json(['data' => $materials]);
    }

    /**
     * Public: return categories with nested materials (UI-friendly shape)
     */
    public function getCategories(Request $request)
    {
        $categories = Category::with('materials')->get()->map(function ($c) {
            return [
                'category_id'   => $c->category_id,
                'category_name' => $c->category_name,
                'slug'          => $c->slug,
                'image_url'     => $c->image_url,
                'materials'     => $c->materials->map(function ($m) {
                    return [
                        'material_id'    => $m->material_id,
                        'material_name'  => $m->material_name,
                        'description'    => $m->description,
                        'price_per_unit' => (float) $m->price_per_unit,
                        'default_unit'   => $m->default_unit,
                        'units'          => $m->units ?? [],
                        'image_url'      => $m->image_url,
                        'category_id'    => $m->category_id,
                    ];
                }),
            ];
        });

        return response()->json(['data' => $categories]);
    }
}
