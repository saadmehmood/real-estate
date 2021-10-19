<?php

namespace App\Providers;

use App\Models\Property;
use App\Observers\PropertyObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        Property::observe(PropertyObserver::class);
//        User::observe(UserObserver::class);
//        Comment::observe(CommentObserver::class);
    }
}
