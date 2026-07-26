<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Http\Requests\StoreDestinationRequest;
use App\Http\Requests\UpdateDestinationRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::latest()->paginate(10);
        return view('admin.destinations.index', compact('destinations'));
    }

    public function create()
    {
        $states = \App\Models\State::where('status', 'active')->orderBy('name')->get();
        return view('admin.destinations.create', compact('states'));
    }

    public function store(StoreDestinationRequest $request)
    {
        $data = $request->validated();
        
        $slug = Str::slug($request->name);
        $count = 1;
        $originalSlug = $slug;
        while (Destination::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('destinations', 'public');
        }

        $data['is_best_rate'] = $request->has('is_best_rate');
        $data['show_on_homepage'] = $request->has('show_on_homepage');
        $data['show_in_menu'] = $request->has('show_in_menu');
        $data['state_id'] = $request->state_id;

        Destination::create($data);

        return redirect()->route('admin.destinations.index')->with('success', 'Destination created successfully.');
    }

    public function edit(Destination $destination)
    {
        $states = \App\Models\State::where('status', 'active')->orderBy('name')->get();
        return view('admin.destinations.edit', compact('destination', 'states'));
    }

    public function update(UpdateDestinationRequest $request, Destination $destination)
    {
        $data = $request->validated();

        $slug = Str::slug($request->name);
        $count = 1;
        $originalSlug = $slug;
        while (Destination::where('slug', $slug)->where('id', '!=', $destination->id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('image')) {
            if ($destination->image) {
                Storage::disk('public')->delete($destination->image);
            }
            $data['image'] = $request->file('image')->store('destinations', 'public');
        }

        $data['is_best_rate'] = $request->has('is_best_rate');
        $data['show_on_homepage'] = $request->has('show_on_homepage');
        $data['show_in_menu'] = $request->has('show_in_menu');
        $data['state_id'] = $request->state_id;

        $destination->update($data);

        return redirect()->route('admin.destinations.index')->with('success', 'Destination updated successfully.');
    }

    public function destroy(Destination $destination)
    {
        if ($destination->image) {
            Storage::disk('public')->delete($destination->image);
        }
        $destination->delete();

        return redirect()->route('admin.destinations.index')->with('success', 'Destination deleted successfully.');
    }
}
