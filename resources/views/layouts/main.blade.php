<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="flex flex-col min-h-screen bg-gray-50">
    <header class="bg-white shadow relative z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="font-bold text-xl text-indigo-600">Impulso Norte</a>
                    </div>
                </div>
                <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-8">
                    @if(isset($mainMenu))
                        @foreach($mainMenu->items as $item)
                             <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                <a href="{{ $item->url ?? ($item->page ? route('page.show', $item->page->slug) : '#') }}" 
                                   target="{{ $item->target }}"
                                   class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium inline-flex items-center">
                                   {{ $item->title }}
                                   @if($item->children->count() > 0)
                                     <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                   @endif
                                </a>
                                @if($item->children->count() > 0)
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-1"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 translate-y-1"
                                         class="absolute z-10 left-0 mt-0 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                                         style="display: none;">
                                        <div class="py-1">
                                            @foreach($item->children as $child)
                                                <a href="{{ $child->url ?? ($child->page ? route('page.show', $child->page->slug) : '#') }}" 
                                                   target="{{ $child->target }}"
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    {{ $child->title }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                             </div>
                        @endforeach
                    @endif
                </div>
                <!-- Mobile menu button could go here -->
            </div>
        </nav>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm mb-4 md:mb-0">&copy; {{ date('Y') }} Impulso Norte. Todos los derechos reservados.</p>
                <div class="flex space-x-6">
                    @if(isset($footerMenu))
                        @foreach($footerMenu->items as $item)
                            <a href="{{ $item->url ?? ($item->page ? route('page.show', $item->page->slug) : '#') }}" 
                               target="{{ $item->target }}"
                               class="text-gray-500 hover:text-gray-900 text-sm">
                                {{ $item->title }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
