<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('vendor.login')->with('error', 'Please login first');
        }
        
        // Check if user has vendor role
        $user = Auth::user();
        
        // Check if role column exists and user is vendor
        if (isset($user->role) && $user->role === 'vendor') {
            return $next($request);
        }
        
        // Check if user has vendor relationship or vendor_id
        if (isset($user->vendor_id) && $user->vendor_id !== null) {
            return $next($request);
        }
        
        // If not vendor, redirect to login
        return redirect()->route('vendor.login')->with('error', 'Access denied. Vendor only area.');
    }
}