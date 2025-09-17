<?php

namespace App\Jobs;

use App\Models\Request as RecyclingRequest;
use App\Models\Invoice;
use App\Models\User;
use App\Mail\CompletionInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class SendCompletionInvoiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;
    protected $invoice;

    /**
     * Create a new job instance.
     *
     * @param RecyclingRequest $request
     * @param Invoice $invoice
     * @return void
     */
    public function __construct(RecyclingRequest $request, Invoice $invoice)
    {
        $this->request = $request;
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Generate PDF invoice
        $pdf = PDF::loadView('emails.invoice', [
            'request' => $this->request,
            'invoice' => $this->invoice
        ]);

        // Send email with PDF attachment
        Mail::to($this->request->customer->email)
            ->send(new CompletionInvoice($this->request, $this->invoice, $pdf));
    }
}