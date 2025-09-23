<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendRequestConfirmationEmail;
use App\Jobs\SendCollectorAssignedEmail;
use App\Jobs\SendCompletionInvoiceEmail;
use App\Models\EmailLog;
use App\Models\Invoice;
use App\Models\Request as RecyclingRequest;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function index($requestId)
    {
        try {
            $logs = EmailLog::where('request_id', $requestId)
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $logs,
            ]);
        } catch (\Throwable $e) {
            // If table missing or other error, return empty list to avoid 500s
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }
    }

    public function resend($requestId, Request $request)
    {
        $type = $request->input('email_type');
        $recyclingRequest = RecyclingRequest::with(['customer', 'requestItems.material', 'invoice'])
            ->findOrFail($requestId);

        switch ($type) {
            case 'request_confirmation':
                SendRequestConfirmationEmail::dispatch($recyclingRequest);
                break;
            case 'collector_assigned':
                SendCollectorAssignedEmail::dispatch($recyclingRequest);
                break;
            case 'completion_invoice':
                $invoice = $recyclingRequest->invoice;
                if (!$invoice) {
                    return response()->json(['success' => false, 'message' => 'No invoice for this request'], 400);
                }
                SendCompletionInvoiceEmail::dispatch($recyclingRequest, $invoice);
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Unknown email_type'], 422);
        }

        return response()->json(['success' => true, 'message' => 'Email queued for resend']);
    }
}
