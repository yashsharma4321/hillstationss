<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        $query = Booking::with(['customer', 'property'])
            ->where('vendor_id', $vendor->id);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $bookings = $query->latest()
            ->paginate(15)
            ->withQueryString();

        $properties = \App\Models\Property::where('vendor_id', $vendor->id)
            ->orderBy('name')
            ->get();
            
        return view('vendor.bookings.index', compact('bookings', 'properties'));
    }
}
