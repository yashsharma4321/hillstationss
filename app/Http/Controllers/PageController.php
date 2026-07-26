<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Property;
use App\Models\Destination;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug = 'home')
    {
        $page = Page::where('slug', $slug)->with('detail')->firstOrFail();
        $sections = $page->detail ? ($page->detail->json_data['sections'] ?? []) : [];

        // Enrich sections with dynamic data
        foreach ($sections as &$section) {
            if ($section['type'] === 'best_rates') {
                $section['data'] = Destination::where('is_best_rate', 1)
                    ->with([
                        'state',
                        'properties' => function ($q) {
                            $q->where('status', 'active')->limit(4);
                        }
                    ])
                    ->get();
            }

            if ($section['type'] === 'featured_destinations') {
                $section['data'] = Destination::where('show_on_homepage', 1)
                    ->with([
                        'properties' => function ($q) {
                            $q->where('show_on_homepage', 1)->where('status', 'active');
                        }
                    ])
                    ->get();
            }

            if ($section['type'] === 'featured_properties') {
                $bhks = $section['bhks'] ?? [];
                $query = Property::where('show_on_homepage', 1)->where('status', 'active');

                if (!empty($bhks)) {
                    $query->whereIn('total_bedrooms', $bhks);
                }

                $section['data'] = $query->latest()->get()->groupBy('total_bedrooms');
            }
        }

        return view('page.show', compact('page', 'sections'));
    }
}
