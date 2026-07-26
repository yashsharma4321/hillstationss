<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'is_best_rate' => 'nullable|boolean',
            'show_on_homepage' => 'nullable|boolean',
            'show_in_menu' => 'nullable|boolean',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'state_id' => 'nullable|exists:states,id',
        ];
    }
}
