<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\PageDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'banner_alt_text' => 'nullable|string|max:255',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sections' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'schema' => 'nullable|string',
        ]);

        $data = $request->only(['title', 'description', 'banner_alt_text', 'meta_title', 'meta_description', 'meta_keywords']);

        if ($request->schema) {
            $data['schema'] = json_decode($request->schema, true);
        }
        $slug = Str::slug($request->title);
        $count = 1;
        $originalSlug = $slug;
        while (Page::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('pages', 'public');
        }

        $page = Page::create($data);

        $sectionsData = $this->processSections($request->sections, []);

        if (!empty($sectionsData)) {
            $page->detail()->create([
                'json_data' => ['sections' => $sectionsData],
            ]);
        }

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'banner_alt_text' => 'nullable|string|max:255',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sections' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'schema' => 'nullable|string',
        ]);

        $data = $request->only(['title', 'description', 'banner_alt_text', 'meta_title', 'meta_description', 'meta_keywords']);

        if ($request->schema) {
            $data['schema'] = json_decode($request->schema, true);
        } else {
            $data['schema'] = null;
        }

        $slug = Str::slug($request->title);
        $count = 1;
        $originalSlug = $slug;
        while (Page::where('slug', $slug)->where('id', '!=', $page->id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('banner_image')) {
            if ($page->banner_image) {
                Storage::disk('public')->delete($page->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('pages', 'public');
        }

        $page->update($data);

        $existingSections = $page->detail ? ($page->detail->json_data['sections'] ?? []) : [];
        $sectionsData = $this->processSections($request->sections, $existingSections);

        if (!empty($sectionsData)) {
            $page->detail()->updateOrCreate(
                ['page_id' => $page->id],
                ['json_data' => ['sections' => $sectionsData]]
            );
        } else {
            if ($page->detail) {
                $page->detail->delete();
            }
        }

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        if ($page->banner_image) {
            Storage::disk('public')->delete($page->banner_image);
        }
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully.');
    }

    private function processSections($sectionsInput, $existingSections)
    {
        $sectionsData = [];
        if (is_array($sectionsInput)) {
            foreach ($sectionsInput as $index => $section) {
                $type = $section['type'] ?? 'text';
                $sectionData = [
                    'type' => $type,
                    'key' => $section['key'] ?? '',
                    'title' => $section['title'] ?? '',
                    'description' => $section['description'] ?? '',
                ];

                switch ($type) {
                    case 'text':
                        $sectionData['content'] = $section['content'] ?? '';
                        $sectionData['image'] = $this->handleSingleImageUpload($section, $existingSections[$index]['image'] ?? null);
                        break;
                    case 'image':
                        $sectionData['image'] = $this->handleSingleImageUpload($section, $existingSections[$index]['image'] ?? null);
                        break;
                    case 'image_points':
                        $sectionData['image'] = $this->handleSingleImageUpload($section, $existingSections[$index]['image'] ?? null);
                        $sectionData['points'] = $section['points'] ?? [];
                        // Filter out empty points
                        $sectionData['points'] = array_filter($sectionData['points'], fn($v) => !is_null($v) && $v !== '');
                        break;
                    case 'video':
                        $sectionData['url'] = $section['url'] ?? '';
                        break;
                    case 'faq':
                        $items = [];
                        if (isset($section['items']) && is_array($section['items'])) {
                            foreach ($section['items'] as $item) {
                                if (!empty($item['q'])) {
                                    $items[] = [
                                        'q' => $item['q'] ?? '',
                                        'a' => $item['a'] ?? ''
                                    ];
                                }
                            }
                        }
                        $sectionData['items'] = $items;
                        break;
                    case 'gallery':
                        $sectionData['content'] = $section['content'] ?? '';
                        $sectionData['label1'] = $section['label1'] ?? '';
                        $sectionData['label2'] = $section['label2'] ?? '';
                        $sectionData['images'] = $this->handleGalleryUpload($section, $existingSections[$index]['images'] ?? []);
                        break;
                    case 'carousel':
                        $sectionData['items'] = $this->handleCarouselUpload($section, $existingSections[$index]['items'] ?? []);
                        break;
                    case 'feature_grid':
                        $sectionData['items'] = $this->handleFeatureGridUpload($section, $existingSections[$index]['items'] ?? []);
                        break;
                    case 'stats_grid':
                        $sectionData['background_image'] = $this->handleSingleImageUpload($section, $existingSections[$index]['background_image'] ?? null, 'background_image');
                        $sectionData['items'] = $this->handleStatsGridUpload($section, $existingSections[$index]['items'] ?? []);
                        break;
                    case 'best_rates':
                    case 'featured_destinations':
                        $sectionData['subtitle'] = $section['subtitle'] ?? '';
                        break;
                    case 'featured_properties':
                        $sectionData['subtitle'] = $section['subtitle'] ?? '';
                        $sectionData['bhks'] = array_filter($section['bhks'] ?? []);
                        break;
                    case 'multi_text':
                        $items = [];
                        if (isset($section['items']) && is_array($section['items'])) {
                            foreach ($section['items'] as $item) {
                                $items[] = [
                                    'title' => $item['title'] ?? '',
                                    'content' => $item['content'] ?? ''
                                ];
                            }
                        }
                        $sectionData['items'] = $items;
                        break;
                }

                $sectionsData[] = $sectionData;
            }
        }
        return $sectionsData;
    }

    private function handleSingleImageUpload($section, $oldPath, $fieldName = 'image')
    {
        $existingFieldName = 'existing_' . $fieldName;
        if (isset($section[$fieldName]) && $section[$fieldName] instanceof \Illuminate\Http\UploadedFile) {
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            return $section[$fieldName]->store('pages/sections', 'public');
        } elseif (isset($section[$existingFieldName])) {
            return $section[$existingFieldName];
        }

        // Image was removed or never existed
        if ($oldPath && !isset($section[$existingFieldName])) {
            Storage::disk('public')->delete($oldPath);
        }
        return null;
    }

    private function handleGalleryUpload($section, $oldImagesArray)
    {
        $finalImages = [];

        // Retain existing images that were not removed
        if (isset($section['existing_images']) && is_array($section['existing_images'])) {
            $finalImages = $section['existing_images'];
        }

        // Delete any old images that are no longer in the finalImages list
        foreach ($oldImagesArray as $oldImg) {
            if (!in_array($oldImg, $finalImages)) {
                Storage::disk('public')->delete($oldImg);
            }
        }

        // Add newly uploaded images
        if (isset($section['images']) && is_array($section['images'])) {
            foreach ($section['images'] as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $finalImages[] = $file->store('pages/sections/gallery', 'public');
                }
            }
        }

        return array_values($finalImages);
    }
    private function handleCarouselUpload($section, $oldItemsArray)
    {
        $finalItems = [];
        $itemsInput = $section['items'] ?? [];

        foreach ($itemsInput as $iIdx => $item) {
            $itemData = [
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
            ];

            // Image handling for this item
            $oldImg = null;
            // Try to find the old image path if it exists for this item index
            // Note: This is a bit tricky if items are reordered, but usually sufficient for simple CMS
            if (isset($oldItemsArray[$iIdx]['image'])) {
                $oldImg = $oldItemsArray[$iIdx]['image'];
            }

            if (isset($item['image']) && $item['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($oldImg) {
                    Storage::disk('public')->delete($oldImg);
                }
                $itemData['image'] = $item['image']->store('pages/sections/carousel', 'public');
            } elseif (isset($item['existing_image'])) {
                $itemData['image'] = $item['existing_image'];
            } else {
                if ($oldImg) {
                    Storage::disk('public')->delete($oldImg);
                }
                $itemData['image'] = null;
            }

            $finalItems[] = $itemData;
        }

        return $finalItems;
    }
    private function handleFeatureGridUpload($section, $oldItemsArray)
    {
        $finalItems = [];
        $itemsInput = $section['items'] ?? [];

        foreach ($itemsInput as $iIdx => $item) {
            $itemData = [
                'title' => $item['title'] ?? '',
                'alt' => $item['alt'] ?? '',
                'description' => $item['description'] ?? '',
            ];

            $oldImg = $oldItemsArray[$iIdx]['image'] ?? null;

            if (isset($item['image']) && $item['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($oldImg) {
                    Storage::disk('public')->delete($oldImg);
                }
                $itemData['image'] = $item['image']->store('pages/sections/features', 'public');
            } elseif (isset($item['existing_image'])) {
                $itemData['image'] = $item['existing_image'];
            } else {
                if ($oldImg) {
                    Storage::disk('public')->delete($oldImg);
                }
                $itemData['image'] = null;
            }

            $finalItems[] = $itemData;
        }

        return $finalItems;
    }

    private function handleStatsGridUpload($section, $oldItemsArray)
    {
        $finalItems = [];
        $itemsInput = $section['items'] ?? [];

        foreach ($itemsInput as $iIdx => $item) {
            $itemData = [
                'title' => $item['title'] ?? '',
                'label' => $item['label'] ?? '',
                'link' => $item['link'] ?? '',
            ];

            $oldImg = $oldItemsArray[$iIdx]['image'] ?? null;

            if (isset($item['image']) && $item['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($oldImg) {
                    Storage::disk('public')->delete($oldImg);
                }
                $itemData['image'] = $item['image']->store('pages/sections/stats', 'public');
            } elseif (isset($item['existing_image'])) {
                $itemData['image'] = $item['existing_image'];
            } else {
                if ($oldImg) {
                    Storage::disk('public')->delete($oldImg);
                }
                $itemData['image'] = null;
            }

            $finalItems[] = $itemData;
        }

        return $finalItems;
    }
}
