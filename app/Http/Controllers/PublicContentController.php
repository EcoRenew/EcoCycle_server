<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class PublicContentController extends Controller
{
    public function faqs()
    {
        $faqs = Faq::where('is_active', true)->orderBy('display_order')->get();
        return response()->json(['success' => true, 'data' => $faqs]);
    }

    public function events()
    {
        $events = Event::where('is_active', true)->orderBy('date')->get();
        return response()->json(['success' => true, 'data' => $events]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'name' => 'required|string',
            'email' => 'required|email',
        ]);
        $reg = EventRegistration::create($data);
        return response()->json(['success' => true, 'data' => $reg], 201);
    }
}
