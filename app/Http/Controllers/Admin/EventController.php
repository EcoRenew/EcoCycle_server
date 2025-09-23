<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => Event::orderByDesc('date')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'time' => 'nullable|string',
            'location' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);
        $event = Event::create($data);
        return response()->json(['success' => true, 'data' => $event], 201);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'time' => 'nullable|string',
            'location' => 'nullable|string',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);
        $event->fill($data)->save();
        return response()->json(['success' => true, 'data' => $event]);
    }

    public function destroy($id)
    {
        Event::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
