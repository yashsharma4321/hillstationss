<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorRegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('vendor.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'business_name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'address' => 'required|string',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'vendor',
            ]);

            $brandLogoPath = null;
            if ($request->hasFile('brand_logo')) {
                $brandLogoPath = $request->file('brand_logo')->store('vendors/logos', 'public');
            }

            // Create Vendor Profile
            Vendor::create([
                'user_id' => $user->id,
                'business_name' => $request->business_name,
                'business_phone' => $request->phone,
                'business_email' => $request->email,
                'city' => $request->city,
                'state' => $request->state,
                'address' => $request->address,
                'brand_logo' => $brandLogoPath,
                'status' => 'active',
                'is_approved' => false, // Requires admin approval
            ]);

            DB::commit();

            return redirect()->route('vendor.login')->with('success', 'Registration successful! Your account is pending admin approval. You can login once approved.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Registration failed. Please try again.')->withInput();
        }
    }
}
