<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $bookings = Booking::with(['customer', 'property'])
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->paginate(15);
            
        return view('vendor.bookings.index', compact('bookings'));
    }
}
