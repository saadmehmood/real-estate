<?php

namespace App\Observers;

use App\Models\Property;
use Illuminate\Support\Str;

class PropertyObserver
{
    /**
     * Listen to the Post saving event.
     */
    public function creating(Property $property): void
    {
        $property->slug = Str::slug($property->title, '-');
    }
}
