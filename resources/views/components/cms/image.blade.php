@props(['data'])

<section class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="relative rounded-lg overflow-hidden shadow-lg">
        <img src="{{ \Illuminate\Support\Facades\Storage::url($data['image']) }}" alt="{{ $data['caption'] ?? '' }}"
            class="w-full h-auto">
        @if(isset($data['caption']))
            <div class="absolute bottom-0 left-0 bg-black bg-opacity-50 text-white p-4 w-full">
                <p class="text-sm">{{ $data['caption'] }}</p>
            </div>
        @endif
    </div>
</section>