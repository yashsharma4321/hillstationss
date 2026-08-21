<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Property extends Model
{
    protected $fillable = [
        'vendor_id', 'category_id', 'destination_id', 'name', 'slug', 'description', 'amount',
        'total_bedrooms', 'total_bathrooms', 'max_guests', 'max_capacity', 'address', 'gallery',
        'city', 'state', 'zip_code', 'country', 'latitude', 'longitude', 
        'check_in_time', 'check_out_time', 'status', 'average_rating', 'show_on_homepage', 
        'is_featured', 'show_in_menu', 'gst', 'extra_person_charge', 'instagram_videos', 'nearby_attractions', 'brochure', 'meals',
        'pets_allowed', 'pet_charge_type', 'pet_charge', 'house_rules', 'house_rules_description', 'things_to_do'
    ];

    protected $casts = [
        'gallery' => 'array',
        'instagram_videos' => 'array',
        'nearby_attractions' => 'array',
        'meals' => 'array',
        'things_to_do' => 'array',
        'show_in_menu' => 'boolean',
        'show_on_homepage' => 'boolean',
        'is_featured' => 'boolean',
        'pets_allowed' => 'boolean',
        'pet_charge' => 'decimal:2',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function amenities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenities');
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function cancellationRules(): HasMany
    {
        return $this->hasMany(CancellationRule::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'property_collections');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function specialDates(): HasMany
    {
        return $this->hasMany(PropertySpecialDate::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(\App\Models\Booking::class);
    }

    public function mealTypes(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_property');
    }
}
