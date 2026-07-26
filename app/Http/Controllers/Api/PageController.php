<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use OpenApi\Attributes as OA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    #[OA\Get(path: "/api/pages", summary: "Get list of pages", tags: ["Pages"])]
    #[OA\Response(
        response: 200,
        description: "List of pages",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "title", type: "string"),
                        new OA\Property(property: "description", type: "string"),
                        new OA\Property(property: "banner_image", type: "string"),
                        new OA\Property(property: "banner_alt_text", type: "string")
                    ]
                ))
            ]
        )
    )]
    public function index()
    {
        $pages = Page::select('id', 'title', 'description', 'banner_image', 'banner_alt_text', 'created_at')->get();

        $pages->transform(function ($page) {
            if ($page->banner_image) {
                $page->banner_image = url(Storage::url($page->banner_image));
            }
            return $page;
        });

        return response()->json([
            'status' => 'success',
            'data' => $pages
        ]);
    }

    #[OA\Get(path: "/api/pages/{slug}", summary: "Get single page details by slug", tags: ["Pages"])]
    #[OA\Parameter(name: "slug", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(response: 200, description: "Page details containing structured blocks")]
    #[OA\Response(response: 404, description: "Page not found")]
    public function show($slug)
    {

        $page = Page::with('detail')->where('slug', $slug)->first();

        if (!$page) {
            return response()->json([
                'status' => 'error',
                'message' => 'Page not found'
            ], 404);
        }

        if ($page->banner_image) {
            $page->banner_image = url(Storage::url($page->banner_image));
        }

        $formattedSections = [];
        if ($page->detail && !empty($page->detail->json_data['sections'])) {
            $formattedSections = $this->formatSections($page->detail->json_data['sections']);
        }

        $responseData = [
            'id' => $page->id,
            'title' => $page->title,
            'description' => $page->description,
            'banner_image' => $page->banner_image,
            'banner_alt_text' => $page->banner_alt_text,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keywords' => $page->meta_keywords,
            'schema' => $page->schema,
            'created_at' => $page->created_at,
            'sections' => $formattedSections
        ];

        // Add extra context if it's the home page
        if ($slug === 'home') {
            $responseData['discovery'] = [
                'categories' => \App\Models\PropertyCategory::all()->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'image' => $c->image ? url(Storage::url($c->image)) : null
                ]),
                'destinations' => \App\Models\Destination::select('id', 'name', 'image')->get()->map(fn($d) => [
                    'id' => $d->id,
                    'name' => $d->name,
                    'image' => $d->image ? url(Storage::url($d->image)) : null
                ]),
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $responseData
        ]);
    }

    /**
     * Parse and enrich section data with live database records and full URLs.
     */
    private function formatSections(array $sections)
    {
        foreach ($sections as &$section) {
            $type = $section['type'] ?? 'text';

            // 1. Enrich Dynamic Blocks (Properties/Destinations)
            if ($type === 'best_rates') {
                $destinations = \App\Models\Destination::where('is_best_rate', 1)
                    ->with([
                        'state',
                        'properties' => function ($q) {
                            $q->where('status', 'active')->limit(4);
                        }
                    ])
                    ->get();

                foreach ($destinations as &$dest) {
                    $dest->image = $this->formatImageUrl($dest->image);
                    foreach ($dest->properties as &$prop) {
                        if (!empty($prop->gallery)) {
                            foreach ($prop->gallery as &$g) {
                                if (isset($g['image']))
                                    $g['image'] = $this->formatImageUrl($g['image']);
                            }
                        }
                    }
                }
                $section['data'] = $destinations;
            }

            if ($type === 'featured_destinations') {
                $destinations = \App\Models\Destination::where('show_on_homepage', 1)
                    ->with([
                        'properties' => function ($q) {
                            $q->where('show_on_homepage', 1)->where('status', 'active');
                        }
                    ])
                    ->get();

                foreach ($destinations as &$dest) {
                    $dest->image = $this->formatImageUrl($dest->image);
                    foreach ($dest->properties as &$prop) {
                        if (!empty($prop->gallery)) {
                            foreach ($prop->gallery as &$g) {
                                if (isset($g['image']))
                                    $g['image'] = $this->formatImageUrl($g['image']);
                            }
                        }
                    }
                }
                $section['data'] = $destinations;
            }

            if ($type === 'featured_properties') {
                $bhks = $section['bhks'] ?? [];
                $query = \App\Models\Property::where('show_on_homepage', 1)->where('status', 'active');
                if (!empty($bhks))
                    $query->whereIn('total_bedrooms', $bhks);

                $properties = $query->latest()->get();
                foreach ($properties as &$prop) {
                    if (!empty($prop->gallery)) {
                        foreach ($prop->gallery as &$g) {
                            if (isset($g['image']))
                                $g['image'] = $this->formatImageUrl($g['image']);
                        }
                    }
                }
                $section['data'] = $properties->groupBy('total_bedrooms');
            }

            // 2. Format Image URLs in Section attributes
            if (!empty($section['image'])) {
                $section['image'] = $this->formatImageUrl($section['image']);
            }
            if (!empty($section['background_image'])) {
                $section['background_image'] = $this->formatImageUrl($section['background_image']);
            }

            // 3. Format 'images' array (Gallery, etc)
            if (!empty($section['images']) && is_array($section['images'])) {
                $section['images'] = array_map(fn($img) => $this->formatImageUrl($img), $section['images']);
            }

            // 4. Format 'items' array (Carousel, Feature Grid, Stats Grid, etc)
            if (!empty($section['items']) && is_array($section['items'])) {
                foreach ($section['items'] as &$item) {
                    if (is_array($item)) {
                        if (!empty($item['image'])) {
                            $item['image'] = $this->formatImageUrl($item['image']);
                        }
                    }
                }
            }
        }

        return $sections;
    }

    /**
     * Helper to ensure absolute URL for storage paths.
     */
    private function formatImageUrl($path)
    {
        if (empty($path))
            return null;
        if (filter_var($path, FILTER_VALIDATE_URL))
            return $path;
        return url(Storage::url($path));
    }
}
