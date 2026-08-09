<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use Illuminate\Http\Request;

class BookingRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = BookingRequest::with(['property', 'vendor.user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.booking-requests.index', compact('requests'));
    }

    public function updateStatus(Request $request, BookingRequest $bookingRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,converted,rejected',
        ]);

        $bookingRequest->update(['status' => $request->status]);

        return back()->with('success', 'Status updated.');
    }

    public function destroy(BookingRequest $bookingRequest)
    {
        $bookingRequest->delete();
        return back()->with('success', 'Request deleted.');
    }
}
