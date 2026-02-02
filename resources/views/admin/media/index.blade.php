<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Multimedia / Files') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Upload Form -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data"
                            class="flex items-end gap-4">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Upload File</label>
                                <input type="file" name="file" required class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100
                                " />
                            </div>
                            <div class="flex-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Title (Optional)</label>
                                <input type="text" name="title"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    placeholder="Image Description">
                            </div>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Upload
                            </button>
                        </form>
                    </div>

                    <!-- Gallery Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($files as $file)
                            <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition bg-white">
                                <div class="h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                                    @if(str_starts_with($file->type, 'image/'))
                                        <img src="{{ $file->url }}" alt="{{ $file->title }}" class="object-cover w-full h-full">
                                    @else
                                        <div class="text-gray-400 text-center">
                                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span class="text-xs uppercase">{{ Str::afterLast($file->path, '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h4 class="font-bold text-sm mb-1 truncate" title="{{ $file->title }}">
                                        {{ $file->title }}</h4>
                                    <p class="text-xs text-gray-500 mb-3">{{ $file->created_at->format('Y-m-d') }}</p>

                                    <div class="flex justify-between items-center gap-2">
                                        <button onclick="copyToClipboard('{{ $file->url }}')"
                                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold py-1 px-2 rounded border border-gray-300">
                                            Copy URL
                                        </button>
                                        <form action="{{ route('admin.media.destroy', $file) }}" method="POST"
                                            onsubmit="return confirm('Delete this file?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    <input type="text" readonly value="{{ $file->url }}"
                                        class="mt-2 w-full text-[10px] text-gray-400 bg-gray-50 border-none rounded p-1">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $files->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('URL copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
</x-app-layout>