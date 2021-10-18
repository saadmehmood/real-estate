<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PropertyRequest;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Property;
use App\Feature;
use App\PropertyImageGallery;

use Illuminate\Support\Facades\Storage;
use Toastr;

class PropertyController extends Controller
{

    public function index()
    {
        $properties = Property::latest()->withCount('comments')->get();

        return view('admin.properties.index', compact('properties'));
    }


    public function create()
    {
        $features = Feature::all();

        return view('admin.properties.create', compact('features'));
    }


    public function store(PropertyRequest $request): RedirectResponse
    {
        $propertyService = new PropertyService();
        $propertyService->saveProperty($request);

        Toastr::success('message', 'Property created successfully.');
        return redirect()->route('admin.properties.index');
    }


    public function show(Property $property)
    {
        $property = Property::withCount('comments')->find($property->id);

        $videoembed = $this->convertYoutube($property->video, 560, 315);

        return view('admin.properties.show', compact('property', 'videoembed'));
    }


    public function edit(Property $property)
    {
        $features = Feature::all();
        $property = Property::find($property->id);

        $videoembed = $this->convertYoutube($property->video);

        return view('admin.properties.edit', compact('property', 'features', 'videoembed'));
    }


    public function update(PropertyRequest $request, $property)
    {
        $propertyService = new PropertyService();
        $propertyService->saveProperty($request, $property);

        Toastr::success('message', 'Property updated successfully.');
        return redirect()->route('admin.properties.index');
    }

 
    public function destroy(Property $property)
    {
        $property = Property::find($property->id);

        if (Storage::disk('public')->exists('property/'.$property->image)) {
            Storage::disk('public')->delete('property/'.$property->image);
        }
        if (Storage::disk('public')->exists('property/'.$property->floor_plan)) {
            Storage::disk('public')->delete('property/'.$property->floor_plan);
        }

        $property->delete();
        
        $galleries = $property->gallery;
        if ($galleries) {
            foreach ($galleries as $key => $gallery) {
                if (Storage::disk('public')->exists('property/gallery/'.$gallery->name)) {
                    Storage::disk('public')->delete('property/gallery/'.$gallery->name);
                }
                PropertyImageGallery::destroy($gallery->id);
            }
        }

        $property->features()->detach();
        $property->comments()->delete();

        Toastr::success('message', 'Property deleted successfully.');
        return back();
    }


    public function galleryImageDelete(Request $request)
    {
        
        $gallaryimg = PropertyImageGallery::find($request->id)->delete();

        if (Storage::disk('public')->exists('property/gallery/'.$request->image)) {
            Storage::disk('public')->delete('property/gallery/'.$request->image);
        }

        if ($request->ajax()) {
            return response()->json(['msg' => $gallaryimg]);
        }
    }

    // YOUTUBE LINK TO EMBED CODE
    private function convertYoutube($youtubelink, $w = 250, $h = 140)
    {
        return preg_replace(
            "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
            "<iframe width=\"$w\" height=\"$h\" src=\"//www.youtube.com/embed/$2\" frameborder=\"0\" allowfullscreen></iframe>",
            $youtubelink
        );
    }
}
