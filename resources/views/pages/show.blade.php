@php
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->seo_title ?? $page->title }} - {{ config('app.name', 'Laravel') }}</title>
    @if($page->seo_description)
        <meta name="description" content="{{ $page->seo_description }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --header-bg:
                {{ $settings['header_bg_color'] ?: '#ffffff' }}
            ;
            --header-text:
                {{ $settings['header_text_color'] ?: '#1b1b18' }}
            ;
            --body-bg:
                {{ $settings['body_bg_color'] ?: '#FDFDFC' }}
            ;
            --footer-bg:
                {{ $settings['footer_bg_color'] ?: '#ffffff' }}
            ;
            --footer-text:
                {{ $settings['footer_text_color'] ?: '#1b1b18' }}
            ;
            --primary-btn-bg:
                {{ $settings['primary_button_color'] ?: '#000000' }}
            ;
            --primary-btn-text: #ffffff;
            --secondary-btn-bg:
                {{ $settings['secondary_button_color'] ?: '#ffffff' }}
            ;
            --secondary-btn-text: #000000;
            --menu-bg:
                {{ $settings['menu_bg_color'] ?: 'transparent' }}
            ;
            --menu-text:
                {{ $settings['menu_text_color'] ?: 'inherit' }}
            ;
        }

        body {
            background-color: var(--body-bg) !important;
        }

        .custom-header {
            background-color: var(--header-bg);
            color: var(--header-text);
        }

        .custom-footer {
            background-color: var(--footer-bg);
            color: var(--footer-text);
        }

        .custom-menu {
            background-color: var(--menu-bg);
            color: var(--menu-text);
        }
    </style>
</head>

<body class="flex flex-col min-h-screen">
    <header class="custom-header w-full lg:max-w-4xl mx-auto text-sm mb-6 p-4 rounded-lg mt-6">
        @if(isset($settings['header_logo']) && $settings['header_logo'])
            <a href="{{ url('/') }}">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($settings['header_logo']) }}"
                    alt="Logo" class="h-10 mb-4">
            </a>
        @endif

        @if(isset($mainMenu))
            <nav class="custom-menu flex items-center justify-end gap-4 p-2 rounded">
                @foreach($mainMenu->items as $item)
                    <a href="{{ $item->url ?? ($item->page ? route('pages.show', $item->page->slug) : '#') }}"
                        target="{{ $item->target ?? '_self' }}" class="text-sm font-medium hover:underline">
                        {{ $item->title }}
                    </a>
                @endforeach
            </nav>
        @endif
    </header>

    <main class="w-full lg:max-w-4xl mx-auto flex-1 p-6 bg-white rounded-lg shadow-sm">
        <h1 class="text-4xl font-extrabold mb-6 tracking-tight text-gray-900">{{ $page->title }}</h1>
        <div
            class="prose prose-lg max-w-none prose-headings:font-bold prose-headings:tracking-tight prose-a:text-blue-600 prose-img:rounded-xl prose-strong:text-gray-900">
            {!! $page->content !!}
        </div>
    </main>

    <footer class="custom-footer w-full p-6 lg:p-8 text-center mt-auto">
        <div class="max-w-4xl mx-auto">
            {!! $settings['footer_content'] ?? '' !!}
        </div>
    </footer>
</body>

</html>