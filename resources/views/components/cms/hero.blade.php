@props(['data'])

<section class="relative bg-gray-900 text-white overflow-hidden">
    @if(isset($data['image']))
        <div class="absolute inset-0">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($data['image']) }}"
                alt="{{ $data['title'] ?? 'Hero Image' }}" class="w-full h-full object-cover opacity-50">
        </div>
    @endif
    <div class="relative max-w-7xl mx-auto py-24 px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
        <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">{{ $data['title'] }}</h1>
        @if(isset($data['cta_text']) && isset($data['cta_url']))
            <div class="mt-10">
                <a href="{{ $data['cta_url'] }}"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-gray-900 bg-white hover:bg-gray-50">
                    {{ $data['cta_text'] }}
                </a>
            </div>
        @endif
    </div>
</section>