<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen bg-gray-50">
        <!-- Left Side: Branding / Background -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-slate-900 items-center justify-center">
            <!-- Capa de fondo con gradiente (puedes ajustar los colores o usar una imagen de fondo aquí) -->
            <div class="absolute inset-0 bg-gradient-to-br from-red-800 to-red-950 opacity-95"></div>
            
            <!-- Contenido Branding -->
            <div class="relative z-10 text-center px-12 text-white">
                <!-- Aquí se carga la imagen del logo -->
                <img src="/images/logo.png" alt="Logo de Impulso Noroccidental de Norte de Santander" class="h-40 mx-auto mb-8 object-contain drop-shadow-lg" />
                
                <h1 class="text-4xl font-bold mb-4 tracking-tight">Impulso Norte</h1>
                <p class="text-lg text-gray-200">Plataforma de Gestión y Análisis de Datos Territoriales</p>
            </div>
        </div>

        <!-- Right Side: Formulario -->
        <div class="flex flex-col justify-center w-full lg:w-1/2 px-6 py-12 sm:px-12 md:px-20 shadow-[-10px_0_30px_rgba(0,0,0,0.05)] bg-white z-20">
            <!-- Logo para pantallas móviles (visible solo en móviles) -->
            <div class="flex lg:hidden justify-center mb-8">
                <Link href="/">
                    <img src="/images/logo.png" alt="Logo" class="h-24 w-auto object-contain" />
                </Link>
            </div>

            <!-- Contenedor del formulario (el slot donde inyecta Login.vue) -->
            <div class="w-full max-w-md mx-auto">
                <slot />
            </div>
        </div>
    </div>
</template>
