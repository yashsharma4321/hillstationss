<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingRequestController extends Controller
{
    public function index(Request $request)
    {
        $vendorId = Auth::user()->vendor->id;

        $query = BookingRequest::with('property')
            ->where('vendor_id', $vendorId)
            ->latest();

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

        $requests = $query->paginate(20)->withQueryString();

        // Properties for filter dropdown
        $properties = \App\Models\Property::where('vendor_id', $vendorId)
            ->select('id', 'name')->orderBy('name')->get();

        return view('vendor.booking-requests.index', compact('requests', 'properties'));
    }

    public function updateStatus(Request $request, BookingRequest $bookingRequest)
    {
        // Ensure vendor owns this request
        if ($bookingRequest->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,contacted,converted,rejected',
        ]);

        $bookingRequest->update(['status' => $request->status]);

        return back()->with('success', 'Status updated.');
    }
}
