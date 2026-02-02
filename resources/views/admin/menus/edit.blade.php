<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Menu: ') . $menu->name }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ addingItem: false, parentId: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Menu Details Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.menus.update', $menu) }}" method="POST" class="flex items-end gap-4">
                        @csrf
                        @method('PUT')
                        <div class="flex-1">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                            <input type="text" name="name" value="{{ $menu->name }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                        </div>
                        <div class="w-48">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                            <select name="location"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="" {{ $menu->location == '' ? 'selected' : '' }}>None</option>
                                <option value="header" {{ $menu->location == 'header' ? 'selected' : '' }}>Header</option>
                                <option value="footer" {{ $menu->location == 'footer' ? 'selected' : '' }}>Footer</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="inline-flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    class="form-checkbox h-5 w-5 text-blue-600" {{ $menu->is_active ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">Active</span>
                            </label>
                        </div>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update Details
                        </button>
                    </form>
                </div>
            </div>

            <!-- Menu Items Builder -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Menu Items</h3>
                        <button @click="addingItem = true; parentId = null"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                            + Add Top-Level Item
                        </button>
                    </div>

                    <!-- Items List -->
                    <ul class="space-y-2">
                        @foreach($menu->items->where('parent_id', null) as $item)
                            <li class="border rounded p-3 bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold">{{ $item->title }}</span>
                                    <div class="flex items-center gap-2">
                                        <button @click="addingItem = true; parentId = {{ $item->id }}"
                                            class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Add Child</button>
                                        <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST"
                                            onsubmit="return confirm('Delete item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 text-xs">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $item->url ? 'Link: ' . $item->url : ($item->page ? 'Page: ' . $item->page->title : 'No Link') }}
                                </div>

                                <!-- Children -->
                                @if($item->children->count() > 0)
                                    <ul class="ml-6 mt-2 space-y-1">
                                        @foreach($item->children as $child)
                                            <li class="border rounded p-2 bg-white flex justify-between items-center">
                                                <span>{{ $child->title }}</span>
                                                <form action="{{ route('admin.menu-items.destroy', $child) }}" method="POST"
                                                    onsubmit="return confirm('Delete child item?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 text-xs">Delete</button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Add Item Modal/Form (Inline) -->
            <div x-show="addingItem" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full"
                style="display: none;">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg leading-6 font-medium text-gray-900"
                            x-text="parentId ? 'Add Child Item' : 'Add Top-Level Item'"></h3>

                        <form action="{{ route('admin.menu-items.store') }}" method="POST" class="mt-4 text-left">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                            <input type="hidden" name="parent_id" x-model="parentId">

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                                <input type="text" name="title" class="border rounded w-full py-2 px-3" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Target</label>
                                <select name="target" class="border rounded w-full py-2 px-3">
                                    <option value="_self">Same Tab</option>
                                    <option value="_blank">New Tab</option>
                                </select>
                            </div>

                            <hr class="my-4 border-gray-200">
                            <p class="text-xs text-gray-500 mb-2">Link To (Choose One):</p>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Internal Page</label>
                                <select name="page_id" class="border rounded w-full py-2 px-3">
                                    <option value="">-- Select Page --</option>
                                    @foreach(\App\Models\Page::where('is_published', true)->get() as $p)
                                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">OR External URL</label>
                                <input type="text" name="url" placeholder="https://"
                                    class="border rounded w-full py-2 px-3">
                            </div>

                            <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
                                <button type="button" @click="addingItem = false"
                                    class="mr-4 text-gray-500 hover:text-gray-700">Cancel</button>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add
                                    Item</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>