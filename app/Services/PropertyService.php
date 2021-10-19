<?php

namespace App\Services;

use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class PropertyService extends Mailable
{
    public function saveProperty($request, $property = null)
    {
        $image = $request->file('image');
        $slug  = Str::slug($request->get('title'));

        if (!is_null($property)) {
            $property = Property::find($property->id);
        }

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imageName = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('property')) {
                Storage::disk('public')->makeDirectory('property');
            }
            if (!is_null($property) && Storage::disk('public')->exists('property/'.$property->image)) {
                Storage::disk('public')->delete('property/'.$property->image);
            }
            $propertyImage = Image::make($image)->stream();
            Storage::disk('public')->put('property/'.$imageName, $propertyImage);
        } elseif (!is_null($property)) {
            $imageName = $property->image;
        } else {
            $imageName = null;
        }

        $floorPlan = $request->file('floor_plan');
        if (isset($floorPlan)) {
            $currentDate = Carbon::now()->toDateString();
            $imageFloorPlan = 'floor-plan-'.$currentDate.'-'.uniqid().'.'.$floorPlan->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('property')) {
                Storage::disk('public')->makeDirectory('property');
            }
            if (!is_null($property) && Storage::disk('public')->exists('property/'.$property->floor_plan)) {
                Storage::disk('public')->delete('property/'.$property->floor_plan);
            }
            $propertyFloorPlan = Image::make($floorPlan)->stream();
            Storage::disk('public')->put('property/'.$imageFloorPlan, $propertyFloorPlan);
        } elseif (!is_null($property)) {
            $imageFloorPlan = $property->floor_plan;
        } else {
            $imageFloorPlan = 'default.png';
        }

        if (is_null($property)) {
            $property = new Property();
            $property->agent_id = Auth::id();
        }
        $property->title    = $request->title;
        $property->price    = $request->price;
        $property->purpose  = $request->purpose;
        $property->type     = $request->type;
        $property->image    = $imageName;
        $property->bedroom  = $request->bedroom;
        $property->bathroom = $request->bathroom;
        $property->city     = $request->city;
        $property->city_slug= Str::slug($request->city);
        $property->address  = $request->address;
        $property->area     = $request->area;

        if (isset($request->featured)) {
            $property->featured = true;
        } else {
            $property->featured = false;
        }
        $property->description          = $request->description;
        $property->video                = $request->video;
        $property->floor_plan           = $imageFloorPlan;
        $property->location_latitude    = $request->location_latitude;
        $property->location_longitude   = $request->location_longitude;
        $property->nearby               = $request->nearby;
        $property->save();

        $property->features()->sync($request->features);


        $gallery = $request->file('gallaryimage');

        if ($gallery) {
            foreach ($gallery as $images) {
                $currentDate = Carbon::now()->toDateString();
                $galimage['name'] = 'gallary-'.$currentDate.'-'.uniqid().'.'.$images->getClientOriginalExtension();
                $galimage['size'] = $images->getSize();
                $galimage['property_id'] = $property->id;

                if (!Storage::disk('public')->exists('property/gallery')) {
                    Storage::disk('public')->makeDirectory('property/gallery');
                }
                $propertyImage = Image::make($images)->stream();
                Storage::disk('public')->put('property/gallery/'.$galimage['name'], $propertyImage);

                $property->gallery()->create($galimage);
            }
        }
    }
}
