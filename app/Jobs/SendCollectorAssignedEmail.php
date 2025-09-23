<?php

namespace App\Jobs;

use App\Models\Request as RecyclingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailLog;

class SendCollectorAssignedEmail implements ShouldQueue
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
            $mailable = new \App\Mail\CollectorAssigned($this->request);
            Mail::to($this->request->customer->email)->send($mailable);
            EmailLog::create([
                'request_id' => $this->request->request_id,
                'email_type' => 'collector_assigned',
                'to_email' => $this->request->customer->email,
                'subject' => method_exists($mailable, 'subject') ? $mailable->subject : 'Collector Assigned',
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            EmailLog::create([
                'request_id' => $this->request->request_id,
                'email_type' => 'collector_assigned',
                'to_email' => $this->request->customer->email,
                'subject' => 'Collector Assigned',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);
            throw $e;
        }
    }
}
