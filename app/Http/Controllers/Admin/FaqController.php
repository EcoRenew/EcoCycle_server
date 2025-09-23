<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => Faq::orderBy('display_order')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ]);
        $faq = Faq::create($data);
        return response()->json(['success' => true, 'data' => $faq], 201);
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);
        $data = $request->validate([
            'question' => 'sometimes|string',
            'answer' => 'sometimes|string',
            'category' => 'nullable|string',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ]);
        $faq->fill($data)->save();
        return response()->json(['success' => true, 'data' => $faq]);
    }

    public function destroy($id)
    {
        Faq::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
