<?php

namespace App\Jobs;

use App\Models\Request as RecyclingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestConfirmation;
use App\Models\EmailLog;

class SendRequestConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @param RecyclingRequest $request
     * @return void
     */
    public function __construct(RecyclingRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $mailable = new RequestConfirmation($this->request);
            Mail::to($this->request->customer->email)->send($mailable);
            EmailLog::create([
                'request_id' => $this->request->request_id,
                'email_type' => 'request_confirmation',
                'to_email' => $this->request->customer->email,
                'subject' => method_exists($mailable, 'subject') ? $mailable->subject : 'Request Confirmation',
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            EmailLog::create([
                'request_id' => $this->request->request_id,
                'email_type' => 'request_confirmation',
                'to_email' => $this->request->customer->email,
                'subject' => 'Request Confirmation',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);
            throw $e;
        }
    }
}
