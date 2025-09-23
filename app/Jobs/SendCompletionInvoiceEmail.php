<?php

namespace App\Jobs;

use App\Models\Request as RecyclingRequest;
use App\Models\Invoice;
use App\Mail\CompletionInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailLog;

class SendCompletionInvoiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;
    protected $invoice;

    /**
     * Create a new job instance.
     */
    public function __construct(RecyclingRequest $request, Invoice $invoice)
    {
        $this->request = $request;
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Ensure relations are loaded
            $this->request->loadMissing(['customer', 'pickupAddress', 'requestItems.material']);

            // Send email and log outcome
            $mailable = new CompletionInvoice($this->request, $this->invoice);
            Mail::to($this->request->customer->email)->send($mailable);
            EmailLog::create([
                'request_id' => $this->request->request_id,
                'email_type' => 'completion_invoice',
                'to_email' => $this->request->customer->email,
                'subject' => method_exists($mailable, 'subject') ? $mailable->subject : 'Completion Invoice',
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            EmailLog::create([
                'request_id' => $this->request->request_id,
                'email_type' => 'completion_invoice',
                'to_email' => $this->request->customer->email,
                'subject' => 'Completion Invoice',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);
            Log::error('SendCompletionInvoiceEmail failed: ' . $e->getMessage(), [
                'request_id' => $this->request->request_id ?? null,
                'invoice_id' => $this->invoice->invoice_id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
