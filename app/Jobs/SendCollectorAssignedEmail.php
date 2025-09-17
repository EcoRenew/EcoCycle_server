<?php

namespace App\Jobs;

use App\Models\Request as RecyclingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

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
        // Send email to customer about collector assignment
        Mail::to($this->request->customer->email)
            ->send(new \App\Mail\CollectorAssigned($this->request));
    }
}