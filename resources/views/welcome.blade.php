@extends('layouts.main')

@section('title', 'Bienvenido - ' . config('app.name'))

@section('content')
    <div class="relative flex items-top justify-center min-h-[calc(100vh-4rem)] bg-gray-100 sm:items-center py-4 sm:pt-0">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
                @php
                    $logo = \App\Models\Setting::where('key', 'header_logo')->value('value');
                 @endphp
                @if($logo)
                    <img src="{{ Storage::disk('public')->url($logo) }}" alt="Logo" class="h-24 w-auto">
                @else
                    <h1 class="text-4xl font-bold text-gray-700">Impulso Norte</h1>
                @endif
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8 text-gray-500">
                            <path
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        <div class="ml-4 text-lg leading-7 font-semibold text-gray-900">Bienvenido</div>
                    </div>

                    <div class="ml-12">
                        <div class="mt-2 text-gray-600 text-sm">
                            Sistema de Gestión de Contenidos y Administración. Seleccione una opción del menú para
                            continuar.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection