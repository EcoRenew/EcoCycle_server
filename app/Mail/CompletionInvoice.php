<?php

namespace App\Mail;

use App\Models\Request as RecyclingRequest;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class CompletionInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $invoice;
    public $pdf;

    /**
     * Create a new message instance.
     */
    public function __construct(RecyclingRequest $request, Invoice $invoice, $pdf)
    {
        $this->request = $request;
        $this->invoice = $invoice;
        $this->pdf = $pdf;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'EcoCycle - Recycling Request Completion Invoice',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.completion-invoice',
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.completion-invoice')
                    ->with([
                        'request' => $this->request,
                        'invoice' => $this->invoice
                    ])
                    ->attachData($this->pdf->output(), 'invoice.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}