<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use OpenApi\Attributes as OA;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    #[OA\Get(path: "/api/settings", summary: "Get all global application settings", tags: ["Settings"])]
    #[OA\Response(
        response: 200,
        description: "Key-value pair object of the global settings.",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean", example: true),
                new OA\Property(property: "data", type: "object", additionalProperties: true, example: ["email" => "info@example.com", "phone" => "+1234567890", "address" => "123 Street City", "footer_short_content" => "We are the best company in the world providing top tier services.", "commission_percentage" => "10.00", "logo" => "http://localhost/storage/settings/logo.png", "favicon" => "http://localhost/storage/settings/favicon.ico", "header_script" => "<!-- script -->", "footer_script" => ""])
            ]
        )
    )]
    public function index()
    {
        $defaults = [
            'email' => null,
            'phone' => null,
            'address' => null,
            'footer_short_content' => null,
            'facebook' => null,
            'instagram' => null,
            'youtube' => null,
            'twitter' => null,
            'razorpay_key' => null,
            'razorpay_secret' => null,
            'commission_percentage' => null,
            'logo' => null,
            'favicon' => null,
            'header_script' => null,
            'footer_script' => null,
        ];

        $settings = array_merge($defaults, Setting::all()->pluck('value', 'key')->toArray());

        if (!empty($settings['logo'])) {
            $settings['logo'] = url(\Illuminate\Support\Facades\Storage::url($settings['logo']));
        }

        if (!empty($settings['favicon'])) {
            $settings['favicon'] = url(\Illuminate\Support\Facades\Storage::url($settings['favicon']));
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
}
