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
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        if ($request->filled('start_date')) {
            $query->where('check_in', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('check_in', '<=', $request->end_date);
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
        $properties = \App\Models\Property::select('id', 'name')->orderBy('name')->get();

        return view('admin.booking-requests.index', compact('requests', 'properties'));
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
