<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function showForm()
    {
        return view('pages.support');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|min:5',
        ]);

        SupportRequest::create($validated);

        return back()->with('success', 'Talebiniz başarıyla alındı. En kısa sürede dönüş yapacağız.');
    }
}


