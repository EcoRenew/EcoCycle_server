<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use App\Http\Requests\StoreDonationRequest;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;



class DonationController extends Controller
{
    public function store(StoreDonationRequest $request)
    {
        try {

            $pickupDate = $request->pickup_date;
            $date = Carbon::createFromFormat('Y-m-d', $pickupDate)->startOfDay();
            if ($date->isFriday()) {
                return response()->json(['error' => 'Pickup cannot be scheduled on Fridays.'], 422);
            }


            if (!$request->hasFile('photos')) {
                return response()->json(['error' => 'No photos uploaded'], 422);
            }

            $imagePaths = [];
            foreach ($request->file('photos') as $file) {
                $path = $file->store('donations', 'public');
                $imagePaths[] = env('APP_URL') . Storage::url($path);
            }

            $validatedData = $request->validated();
            $validatedData['user_id'] = $request->user()->user_id; // Assuming user is authenticated
            $validatedData['photos'] = $imagePaths;

            $donation = Donation::create($validatedData);
            $donation->load('user', 'pickupAddress', 'invoice');



        return response()->json([
            'message' => 'Donation created successfully',
            'donation' => $donation
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create donation', 'details' => $e->getMessage()], 500);
    }
    }
}
