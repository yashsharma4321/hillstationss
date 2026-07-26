<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use OpenApi\Attributes as OA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    #[OA\Get(path: "/api/blogs", summary: "Get list of active blogs", tags: ["Blogs"])]
    #[OA\Response(
        response: 200,
        description: "List of paginated blogs",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "object")
            ]
        )
    )]
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 18);

        $blogs = Blog::where('status', 1)
            ->select(
                'id',
                'title',
                'slug',
                'description',
                'image',
                'image_alt',
                'status',
                'author_id',
                'featured',
                'meta_title',
                'meta_description'
            )
            ->latest()
            ->paginate($perPage);

        $blogs->getCollection()->transform(function ($blog) {
            if ($blog->image) {
                $blog->image = url(Storage::url($blog->image));
            }
            return $blog;
        });

        return response()->json([
            'status' => 'success',
            'data' => $blogs
        ]);
    }

    #[OA\Get(path: "/api/blogs/{slug}", summary: "Get single blog by slug", tags: ["Blogs"])]
    #[OA\Parameter(name: "slug", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Detailed blog data")]
    #[OA\Response(response: 404, description: "Blog not found")]
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->first();

        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found'
            ], 404);
        }

        if ($blog->image) {
            $blog->image = url(Storage::url($blog->image));
        }

        if ($blog->other_images) {
            $blog->other_images = array_map(fn($img) => url(Storage::url($img)), $blog->other_images);
        }

        return response()->json([
            'status' => 'success',
            'data' => $blog
        ]);
    }
}
