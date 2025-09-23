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

        // Search by request_id, customer name/email, collector name, or address street/city
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('request_id', $search)
                    ->orWhereHas('customer', function ($qq) use ($search) {
                        $qq->where('name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    })
                    ->orWhereHas('collector', function ($qq) use ($search) {
                        $qq->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('pickupAddress', function ($qq) use ($search) {
                        $qq->where('street', 'like', "%$search%")
                            ->orWhere('city', 'like', "%$search%");
                    });
            });
        }

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

        // Sorting
        $sortable = ['created_at', 'pickup_date', 'status', 'request_type'];
        $sortBy = in_array($request->input('sort_by'), $sortable) ? $request->input('sort_by') : 'created_at';
        $sortDir = $request->input('sort_dir') === 'asc' ? 'asc' : 'desc';

        // Pagination
        $perPage = (int) ($request->input('per_page') ?? 10);
        $perPage = max(1, min($perPage, 100));

        $recyclingRequests = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

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
            $totalPoints = 0;
            foreach ($request->materials as $item) {
                $material = Material::findOrFail($item['material_id']);
                $calculatedPrice = $material->price_per_unit * $item['quantity'];
                $totalValue += $calculatedPrice;

                // Calculate points: assume quantity is in kilograms-equivalent
                $pointsPerKg = (float) ($material->points ?? 0);
                $totalPoints += $pointsPerKg * (float) $item['quantity'];

                RequestItem::create([
                    'request_id' => $recyclingRequest->request_id,
                    'material_id' => $item['material_id'],
                    'quantity' => $item['quantity'],
                    'calculated_price' => $calculatedPrice
                ]);
            }

            // Award points to user for recycling requests only
            if ($recyclingRequest->request_type === 'Recycling' && $totalPoints > 0) {
                $user = User::findOrFail(Auth::id());
                $current = (int) ($user->recycling_points ?? 0);
                $user->recycling_points = $current + (int) round($totalPoints);
                $user->save();
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
                'points'  => $m->points,
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
                        'points'  => $m->points,
                    ];
                }),
            ];
        });

        return response()->json(['data' => $categories]);
    }

    /**
     * Get dashboard stats for the authenticated user.
     *
     * Returns:
     *  - total_recycled_items (sum of quantities for completed recycling requests)
     *  - total_donation_requests (count)
     *  - total_items_donated (sum of quantities for donation requests)
     *  - total_price_donated (sum of calculated_price for donation requests)
     *  - upcoming_pickups (next 5 pending/assigned pickups with items)
     *  - recycling-points
     */
    public function getUserDashboard()
    {
        $userId = Auth::id();

        // read recycling points 
        $user = Auth::user();
        $recyclingPoints = $user->recycling_points; // accessor is applied

        // Total recycled items (only completed recycling requests)
        $totalRecycledItems = DB::table('request_items')
            ->join('requests', 'request_items.request_id', '=', 'requests.request_id')
            ->where('requests.customer_id', $userId)
            ->where('requests.request_type', 'Recycling')
            ->where('requests.status', 'Completed')
            ->sum('request_items.quantity');

        // Total donation requests count
        $totalDonationRequests = RecyclingRequest::where('customer_id', $userId)
            ->where('request_type', 'Donation')
            ->count();

        // Total items donated (all donation requests)
        $totalItemsDonated = DB::table('request_items')
            ->join('requests', 'request_items.request_id', '=', 'requests.request_id')
            ->where('requests.customer_id', $userId)
            ->where('requests.request_type', 'Donation')
            ->sum('request_items.quantity');

        // Total money value for donated items (sum of calculated_price for donation requests)
        $totalPriceDonated = DB::table('request_items')
            ->join('requests', 'request_items.request_id', '=', 'requests.request_id')
            ->where('requests.customer_id', $userId)
            ->where('requests.request_type', 'Donation')
            ->sum('request_items.calculated_price');

        // Upcoming pickups (next 5 pending or assigned)
        $upcomingPickups = RecyclingRequest::with(['pickupAddress', 'requestItems.material'])
            ->where('customer_id', $userId)
            ->whereIn('status', ['Pending', 'Assigned'])
            ->where('pickup_date', '>=', now())
            ->orderBy('pickup_date', 'asc')
            ->take(5)
            ->get()
            ->map(function ($r) {
                return [
                    'request_id' => $r->request_id,
                    'request_type' => $r->request_type,
                    'status' => $r->status,
                    'pickup_date' => $r->pickup_date,
                    'pickup_address' => $r->pickupAddress ? ($r->pickupAddress->full_address ?? ($r->pickupAddress->street ?? null)) : null,
                    'items' => $r->requestItems->map(function ($it) {
                        return [
                            'material_name' => $it->material->material_name ?? ($it->material->name ?? null),
                            'quantity' => $it->quantity,
                            'calculated_price' => $it->calculated_price,
                        ];
                    })->values(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'recycling_points' => $recyclingPoints,
                'total_recycled_items' => (int) $totalRecycledItems,
                'total_donation_requests' => (int) $totalDonationRequests,
                'total_items_donated' => (int) $totalItemsDonated,
                'total_price_donated' => (float) $totalPriceDonated,
                'upcoming_pickups' => $upcomingPickups,
            ],
        ]);
    }

    /**
     * Admin: Show request by id with relations
     */
    public function adminShow($id)
    {
        $recyclingRequest = RecyclingRequest::with(['customer', 'collector', 'pickupAddress', 'requestItems.material', 'invoice'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $recyclingRequest
        ]);
    }

    /**
     * Admin: Update request fields (pickup_date, pickup_address_id, request_type)
     */
    public function adminUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'pickup_date' => 'sometimes|date',
            'pickup_address_id' => 'sometimes|exists:addresses,address_id',
            'request_type' => 'sometimes|in:Donation,Recycling',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $recyclingRequest = RecyclingRequest::findOrFail($id);
        $recyclingRequest->fill($validator->validated());
        $recyclingRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Request updated',
            'data' => $recyclingRequest->load(['customer', 'collector', 'pickupAddress', 'requestItems.material', 'invoice'])
        ]);
    }

    /**
     * Admin: Delete request (cascades request_items and invoice)
     */
    public function adminDestroy($id)
    {
        $recyclingRequest = RecyclingRequest::findOrFail($id);
        $recyclingRequest->delete();

        return response()->json(['success' => true, 'message' => 'Request deleted']);
    }
}
