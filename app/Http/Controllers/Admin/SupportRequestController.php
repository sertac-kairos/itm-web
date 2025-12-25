<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use Illuminate\Http\Request;

class SupportRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportRequest::query();

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortField, ['id', 'name', 'email', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.support_requests.index', compact('requests'));
    }

    public function show(SupportRequest $supportRequest)
    {
        return view('admin.support_requests.show', compact('supportRequest'));
    }

    public function update(Request $request, SupportRequest $supportRequest)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        $supportRequest->update([
            'status' => $request->get('status')
        ]);

        return back()->with('success', 'Destek talebi güncellendi.');
    }
}


