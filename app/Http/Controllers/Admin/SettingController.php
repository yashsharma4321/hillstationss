<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        // Pluck returns an array-like collection matching key -> value
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        
        // Validate the request
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'white_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:512',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'footer_short_content' => 'nullable|string|max:500',
            'header_script' => 'nullable|string',
            'footer_script' => 'nullable|string',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'razorpay_key' => 'nullable|string|max:255',
            'razorpay_secret' => 'nullable|string|max:255',
        ]);

        // Get all data except token, method, and file fields
        $data = $request->except(['_token', '_method', 'logo', 'white_logo', 'favicon']);
        
        // Update or create text/string settings
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::where('key', 'logo')->first();
            if ($oldLogo && $oldLogo->value && Storage::disk('public')->exists($oldLogo->value)) {
                Storage::disk('public')->delete($oldLogo->value);
            }
            
            $path = $request->file('logo')->store('settings', 'public');
            Setting::updateOrCreate(
                ['key' => 'logo'],
                ['value' => $path]
            );
        }

        // Handle White Logo Upload
        if ($request->hasFile('white_logo')) {
            // Delete old white logo if exists
            $oldWhiteLogo = Setting::where('key', 'white_logo')->first();
            if ($oldWhiteLogo && $oldWhiteLogo->value && Storage::disk('public')->exists($oldWhiteLogo->value)) {
                Storage::disk('public')->delete($oldWhiteLogo->value);
            }
            
            $path = $request->file('white_logo')->store('settings', 'public');
            Setting::updateOrCreate(
                ['key' => 'white_logo'],
                ['value' => $path]
            );
        }

        // Handle Favicon Upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon if exists
            $oldFavicon = Setting::where('key', 'favicon')->first();
            if ($oldFavicon && $oldFavicon->value && Storage::disk('public')->exists($oldFavicon->value)) {
                Storage::disk('public')->delete($oldFavicon->value);
            }
            
            $path = $request->file('favicon')->store('settings', 'public');
            Setting::updateOrCreate(
                ['key' => 'favicon'],
                ['value' => $path]
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    // Optional: Method to remove specific settings
    public function removeImage(Request $request)
    {
        $request->validate([
            'key' => 'required|in:logo,white_logo,favicon'
        ]);

        $setting = Setting::where('key', $request->key)->first();
        if ($setting && $setting->value) {
            // Delete file from storage
            if (Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }
            
            // Delete or set null in database
            $setting->delete(); // or $setting->update(['value' => null]);
            
            return response()->json([
                'success' => true,
                'message' => 'Image removed successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Image not found.'
        ], 404);
    }
}