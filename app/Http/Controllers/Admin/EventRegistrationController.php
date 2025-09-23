<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EventRegistrationController extends Controller
{
    public function index()
    {
        $regs = EventRegistration::with('event')->orderByDesc('created_at')->get();
        return response()->json(['success' => true, 'data' => $regs]);
    }

    public function updateStatus($id, Request $request)
    {
        $data = $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $reg = EventRegistration::with('event')->findOrFail($id);
        $reg->status = $data['status'];
        $reg->save();

        if ($reg->status === 'approved') {
            // Send simple approval email
            Mail::raw("You're approved for {$reg->event->title} at {$reg->event->location} on {$reg->event->date} {$reg->event->time}", function ($m) use ($reg) {
                $m->to($reg->email)->subject('Event Registration Approved');
            });
        }

        return response()->json(['success' => true, 'data' => $reg]);
    }
}
