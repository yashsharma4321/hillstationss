<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CouponController extends Controller
{
    #[OA\Get(path: "/api/coupons", summary: "Get all active global coupons", tags: ["Coupons"])]
    #[OA\Response(
        response: 200,
        description: "List of active global coupons",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function index()
    {
        $userId = auth('sanctum')->id();

        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) use ($userId) {
                $q->where('is_global', true);
                if ($userId) {
                    $q->orWhere('user_id', $userId);
                }
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $coupons
        ]);
    }

    #[OA\Post(path: "/api/coupons/apply", summary: "Validate a coupon code and get its details", tags: ["Coupons"])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "code", type: "string", example: "SAVE10"),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Coupon details",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "object")
            ]
        )
    )]
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'property_id' => 'nullable|integer'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid coupon code.'
            ], 404);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This coupon has expired or reached its usage limit.'
            ], 422);
        }

        if ($coupon->property_id && $coupon->property_id != $request->property_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'This coupon is only valid for a specific property.'
            ], 422);
        }

        // Enforce user_id restriction
        if ($coupon->user_id) {
            $currentUserId = auth('sanctum')->id();
            if (!$currentUserId || $coupon->user_id !== $currentUserId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This coupon is not valid for your account.'
                ], 403);
            }
        }

        $result = [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'min_purchase' => $coupon->min_purchase,
            'description' => $coupon->description,
            'is_valid' => true
        ];

        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }

    #[OA\Get(path: "/api/customer/coupons", summary: "Get all coupons available to the logged-in user", tags: ["Coupons"], security: [['bearerAuth' => []]])]
    #[OA\Response(
        response: 200,
        description: "List of available coupons",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function userCoupons()
    {
        $userId = auth()->id();

        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) use ($userId) {
                $q->where('is_global', true)
                  ->orWhere('user_id', $userId);
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $coupons
        ]);
    }
}
