<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Destination;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class VendorRegisterController extends Controller
{
    #[OA\Get(path: "/api/vendor-registration/setup", summary: "Get necessary data for vendor registration wizard", tags: ["Vendor Registration"])]
    #[OA\Response(
        response: 200,
        description: "Registration setup data (categories, amenities, destinations)",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "object")
            ]
        )
    )]
    public function setup()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => PropertyCategory::select('id', 'name', 'slug')->get(),
                'destinations' => Destination::where('status', 'active')->select('id', 'name', 'slug')->get(),
                'amenities' => Amenity::select('id', 'name', 'icon')->get(),
            ]
        ]);
    }

    #[OA\Post(path: "/api/vendor-registration/submit", summary: "Submit multi-step vendor and property registration", tags: ["Vendor Registration"])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "name", type: "string"),
                new OA\Property(property: "email", type: "string"),
                new OA\Property(property: "phone", type: "string"),
                new OA\Property(property: "business_name", type: "string", nullable: true),
                new OA\Property(property: "property_name", type: "string"),
                new OA\Property(property: "property_amount", type: "number", format: "float", nullable: true, description: "Base amount/price for the property"),
                new OA\Property(property: "vendor_amount", type: "number", format: "float", nullable: true, description: "Vendor's set fee/amount"),
                new OA\Property(property: "category_id", type: "integer"),
                new OA\Property(property: "destination_id", type: "integer"),
                new OA\Property(property: "total_bedrooms", type: "integer"),
                new OA\Property(property: "address", type: "string"),
                new OA\Property(property: "city", type: "string"),
                new OA\Property(property: "state", type: "string"),
                new OA\Property(property: "zip_code", type: "string"),
                new OA\Property(property: "amenities", type: "array", items: new OA\Items(type: "integer")),
                new OA\Property(property: "password", type: "string"),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Registration submitted successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "message", type: "string")
            ]
        )
    )]
    public function submit(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users',
            'phone'           => 'required|string|max:20',
            'business_name'   => 'nullable|string|max:255',
            'property_name'   => 'required|string|max:255',
            'property_amount' => 'nullable|numeric|min:0',
            'vendor_amount'   => 'nullable|numeric|min:0',
            'category_id'     => 'required|exists:property_categories,id',
            'destination_id'  => 'required|exists:destinations,id',
            'total_bedrooms'  => 'required|integer|min:1',
            'address'         => 'required|string',
            'city'            => 'required|string|max:255',
            'state'           => 'required|string|max:255',
            'zip_code'        => 'required|string|max:10',
            'amenities'       => 'nullable|array',
            'amenities.*'     => 'exists:amenities,id',
            'password'        => 'required|string|min:8',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'vendor',
            ]);

            // 2. Create Vendor Profile
            $vendor = Vendor::create([
                'user_id'        => $user->id,
                'business_name'  => $request->business_name ?? $request->name,
                'business_phone' => $request->phone,
                'business_email' => $request->email,
                'city'           => $request->city,
                'state'          => $request->state,
                'address'        => $request->address,
                'amount'         => $request->vendor_amount ?? 0,
                'is_approved'    => false,
                'status'         => 'active',
            ]);

            // 3. Create First Property
            $property = Property::create([
                'vendor_id'      => $vendor->id,
                'category_id'    => $request->category_id,
                'destination_id' => $request->destination_id,
                'name'           => $request->property_name,
                'slug'           => Str::slug($request->property_name) . '-' . Str::random(5),
                'total_bedrooms' => $request->total_bedrooms,
                'address'        => $request->address,
                'city'           => $request->city,
                'state'          => $request->state,
                'zip_code'       => $request->zip_code,
                'country'        => 'India',
                'amount'         => $request->property_amount ?? 0,
                'status'         => 'pending',
            ]);

            // 4. Attach Amenities
            if ($request->has('amenities')) {
                $property->amenities()->attach($request->amenities);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Your property and vendor account have been submitted for review. Please wait for admin approval.',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
