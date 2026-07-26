<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'nullable|image|max:10240',
            'other_images.*' => 'nullable|image|max:10240',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'schema' => 'nullable|string',
        ]);

        $data = $request->except(['image', 'other_images', 'schema']);
        $data['slug'] = Str::slug($request->title);
        $data['status'] = $request->has('status');
        
        if ($request->filled('schema')) {
            $data['schema'] = json_decode($request->schema, true);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        if ($request->hasFile('other_images')) {
            $otherImages = [];
            foreach ($request->file('other_images') as $file) {
                $otherImages[] = $file->store('blogs', 'public');
            }
            $data['other_images'] = $otherImages;
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'nullable|image|max:10240',
            'other_images.*' => 'nullable|image|max:10240',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'schema' => 'nullable|string',
        ]);

        $data = $request->except(['image', 'other_images', 'schema', 'existing_images']);
        $data['slug'] = Str::slug($request->title);
        $data['status'] = $request->has('status');

        if ($request->filled('schema')) {
            $data['schema'] = json_decode($request->schema, true);
        } else {
            $data['schema'] = null;
        }

        if ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $otherImages = $request->input('existing_images', []);
        
        // Delete images that were removed from the UI
        $deletedImages = array_diff($blog->other_images ?? [], $otherImages);
        foreach ($deletedImages as $delImg) {
            Storage::disk('public')->delete($delImg);
        }

        if ($request->hasFile('other_images')) {
            foreach ($request->file('other_images') as $file) {
                $otherImages[] = $file->store('blogs', 'public');
            }
        }
        $data['other_images'] = $otherImages;

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted successfully.');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/blogs/content'), $filename);
            $url = url('storage/blogs/content/' . $filename);

            // Check if it's a CKEditor 4 callback request
            if ($request->has('CKEditorFuncNum')) {
                $funcNum = $request->input('CKEditorFuncNum');
                return response("<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', 'Image uploaded successfully');</script>")
                    ->header('Content-Type', 'text/html');
            }

            // Fallback to JSON for newer versions or plugins
            return response()->json([
                'uploaded' => 1,
                'fileName' => $filename,
                'url' => $url
            ]);
        }

        return response()->json([
            'uploaded' => 0,
            'error' => ['message' => 'Could not upload image.']
        ]);
    }
}
