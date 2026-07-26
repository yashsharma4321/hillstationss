<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $wallet = $vendor->wallet; // Assuming relationship in Vendor model
        
        $stats = [
            'total_properties' => Property::where('vendor_id', $vendor->id)->count(),
            'total_bookings' => Booking::where('vendor_id', $vendor->id)->count(),
            'total_earned' => $wallet->total_earned ?? 0,
            'current_balance' => $wallet->balance ?? 0,
            'pending_bookings' => Booking::where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
        ];

        return view('vendor.dashboard', compact('stats'));
    }
}
