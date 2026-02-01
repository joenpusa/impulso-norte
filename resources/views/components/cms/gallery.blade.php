@props(['data'])

<section class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($data['images'] as $item)
            <div class="relative aspect-w-1 aspect-h-1 rounded-lg overflow-hidden shadow-lg">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($item['image']) }}" alt="Gallery Image"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
            </div>
        @endforeach
    </div>
</section>