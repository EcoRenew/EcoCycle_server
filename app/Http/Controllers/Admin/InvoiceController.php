<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['request.customer'])
            ->orderByDesc('invoice_date');

        if ($request->has('search')) {
            $search = $request->string('search');
            $query->whereHas('request.customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = (int) ($request->get('per_page', 10));
        $invoices = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    public function show($id)
    {
        $invoice = Invoice::with(['request.customer', 'request.requestItems.material'])
            ->where('invoice_id', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $invoice,
        ]);
    }
}
