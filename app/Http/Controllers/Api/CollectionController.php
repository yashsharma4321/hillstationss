<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class CollectionController extends Controller
{
    #[OA\Get(path: "/api/collections", summary: "Get all property collections", tags: ["Collections"])]
    #[OA\Response(
        response: 200,
        description: "List of collections",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "success", type: "boolean"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function index()
    {
        try {
            $collections = Collection::latest()->get()->map(function($item) {
                if ($item->image) {
                    $item->image = url(Storage::url($item->image));
                }
                return $item;
            });

            return response()->json([
                'success' => true,
                'data' => $collections
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch collections'
            ], 500);
        }
    }
}
