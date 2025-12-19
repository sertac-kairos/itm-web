<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use Illuminate\Http\Request;

class SupportRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'device_id' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $supportRequest = SupportRequest::create($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $supportRequest->id,
                'status' => $supportRequest->status,
            ],
            'message' => 'Destek talebiniz alınmıştır.'
        ], 201);
    }
}


