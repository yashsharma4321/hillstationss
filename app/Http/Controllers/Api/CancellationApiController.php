<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CancellationRule;
use Illuminate\Http\Request;

class CancellationApiController extends Controller
{
    /**
     * Get all cancellation rules
     * GET /api/cancellation-rules
     */
    public function index()
    {
        $rules = CancellationRule::orderBy('days_before', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'rules' => $rules
        ]);
    }

    /**
     * Get applicable rule based on days before check-in
     * GET /api/cancellation-rules/calculate?days=20
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:0'
        ]);

        $rule = CancellationRule::where('days_before', '<=', $request->days)
            ->orderBy('days_before', 'desc')
            ->first();

        if (!$rule) {
            return response()->json([
                'success' => false,
                'message' => 'No rule found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'days_before_checkin' => $request->days,
            'rule' => $rule
        ]);
    }
}