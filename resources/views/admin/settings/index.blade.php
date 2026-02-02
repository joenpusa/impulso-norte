<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Structure / Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ activeTab: 'header' }">

                    <!-- Tabs Navigation -->
                    <div class="mb-4 border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                            <li class="mr-2">
                                <button @click="activeTab = 'header'"
                                    :class="activeTab === 'header' ? 'inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300'">
                                    Header
                                </button>
                            </li>
                            <li class="mr-2">
                                <button @click="activeTab = 'footer'"
                                    :class="activeTab === 'footer' ? 'inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300'">
                                    Footer
                                </button>
                            </li>
                            <li class="mr-2">
                                <button @click="activeTab = 'styles'"
                                    :class="activeTab === 'styles' ? 'inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300'">
                                    Styles
                                </button>
                            </li>
                            <li class="mr-2">
                                <button @click="activeTab = 'menu'"
                                    :class="activeTab === 'menu' ? 'inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300'">
                                    Menu
                                </button>
                            </li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Header Tab -->
                        <div x-show="activeTab === 'header'">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Logo</label>
                                @if(isset($settings['header_logo']))
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $settings['header_logo']) }}" alt="Current Logo"
                                            class="h-20">
                                    </div>
                                @endif
                                <input type="file" name="header_logo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Header Background
                                    Color</label>
                                <input type="color" name="header_bg_color"
                                    value="{{ $settings['header_bg_color'] ?? '#ffffff' }}"
                                    class="h-10 w-20 p-1 rounded border">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Header Text Color</label>
                                <input type="color" name="header_text_color"
                                    value="{{ $settings['header_text_color'] ?? '#000000' }}"
                                    class="h-10 w-20 p-1 rounded border">
                            </div>
                        </div>

                        <!-- Footer Tab -->
                        <div x-show="activeTab === 'footer'" style="display: none;">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Footer Content</label>
                                <textarea name="footer_content" rows="5"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ $settings['footer_content'] ?? '' }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Accepts HTML content.</p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Footer Background
                                    Color</label>
                                <input type="color" name="footer_bg_color"
                                    value="{{ $settings['footer_bg_color'] ?? '#ffffff' }}"
                                    class="h-10 w-20 p-1 rounded border">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Footer Text Color</label>
                                <input type="color" name="footer_text_color"
                                    value="{{ $settings['footer_text_color'] ?? '#000000' }}"
                                    class="h-10 w-20 p-1 rounded border">
                            </div>
                        </div>

                        <!-- Styles Tab -->
                        <div x-show="activeTab === 'styles'" style="display: none;">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Body Background Color</label>
                                <input type="color" name="body_bg_color"
                                    value="{{ $settings['body_bg_color'] ?? '#FDFDFC' }}"
                                    class="h-10 w-20 p-1 rounded border">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Primary Button Color</label>
                                <input type="color" name="primary_button_color"
                                    value="{{ $settings['primary_button_color'] ?? '#000000' }}"
                                    class="h-10 w-20 p-1 rounded border">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Secondary Button Color</label>
                                <input type="color" name="secondary_button_color"
                                    value="{{ $settings['secondary_button_color'] ?? '#ffffff' }}"
                                    class="h-10 w-20 p-1 rounded border">
                            </div>
                        </div>

                        <!-- Menu Tab -->
                        <div x-show="activeTab === 'menu'" style="display: none;">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Menu Background Color</label>
                                <input type="color" name="menu_bg_color"
                                    value="{{ $settings['menu_bg_color'] ?? 'transparent' }}"
                                    class="h-10 w-20 p-1 rounded border">
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Menu Text Color</label>
                                <input type="color" name="menu_text_color"
                                    value="{{ $settings['menu_text_color'] ?? 'inherit' }}"
                                    class="h-10 w-20 p-1 rounded border">
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>