<section class="carousel carousel-slider center" style="min-height: 600px">
    @if($sliders)
        @foreach($sliders as $slider)
            <div class="carousel-item" style="background-image: url({{Storage::url('slider/'.$slider->image)}});background-size: cover" href="#{{$slider->id}}!">
                <div class="slider-content">
                    <h2 class="white-text">{{ $slider->title }}</h2>
                    <p class="white-text">{{ $slider->description }}</p>
                </div>
            </div>
        @endforeach
    @else 
        <div class="carousel-item amber indigo-text" style="background-image: url({{ asset('frontend/images/real_estate.jpg') }})" href="#1!">
            <h2>Slider Title goes here</h2>
            <p class="indigo-text">Slider description goes here</p>
        </div>
    @endif
</section>
