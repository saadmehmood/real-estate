<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{
    protected $fillable = [
        'title',
        'price',
        'featured',
        'purpose',
        'type',
        'image',
        'slug',
        'bedroom',
        'bathroom',
        'city',
        'city_slug',
        'address',
        'area',
        'agent_id',
        'description',
        'video',
        'floor_plan',
        'location_latitude',
        'location_longitude',
        'nearby',

    ];

    public function features()
    {
        return $this->belongsToMany(Feature::class)->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function gallery()
    {
        return $this->hasMany(PropertyImageGallery::class);
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function rating()
    {
        return $this->hasMany(Rating::class, 'property_id');
    }

    /**
     * Set slug value.
     *
     * @param $value
     */
    public function setSlugAttribute($value)
    {
        if (static::whereSlug($slug = Str::slug($value))->exists()) {
            $slug = $this->incrementSlug($slug);
        }

        $this->attributes['slug'] = $slug;
    }

    /**
     * add increment if slug already exists.
     * @param $slug
     * @return mixed|string
     */
    private function incrementSlug($slug)
    {
        $original = $slug;
        $count = 2;

        while (static::whereSlug($slug)->exists()) {
            $slug = "{$original}-" . $count++;
        }

        return $slug;
    }

    /**
     * Format Price.
     *
     * @param $price
     * @return mixed|string
     */
    public function getPriceAttribute($price)
    {
        if ($price > 100000000) {
            $price = number_format($price/100000000, 2) . ' Billion';
        } elseif ($price > 10000000) {
            $price = number_format($price/10000000, 2) . ' Crore';
        } elseif ($price > 1000000) {
            $price = number_format($price/1000000, 2) . ' Million';
        } elseif ($price > 100000) {
            $price = number_format($price/100000, 2) . ' Lackh';
        } elseif ($price > 1000) {
            $price = number_format($price/1000, 2) . ' K';
        }
        return $price;
    }
}
