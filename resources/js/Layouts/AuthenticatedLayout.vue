<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const navItems = [
    { name: 'Inicio', route: 'dashboard', active: 'dashboard' },
    { name: 'Multimedia', route: 'admin.media.index', active: 'admin.media.*' },
    { name: 'Páginas', route: 'admin.pages.index', active: 'admin.pages.*' },
    { name: 'Menús', route: 'admin.menus.index', active: 'admin.menus.*' },
    { name: 'Registros Convocatoria', route: 'admin.registros.index', active: 'admin.registros.*' },
    { name: 'Ajustes', route: 'admin.settings.index', active: 'admin.settings.*' },
];
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        
        <!-- Sidebar (Desktop) -->
        <aside class="hidden md:flex flex-col w-64 bg-white border-r border-gray-200">
            <div class="flex items-center justify-center h-16 border-b border-gray-200 bg-gray-50">
                <Link :href="route('dashboard')">
                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                </Link>
            </div>

            <div class="flex-1 overflow-y-auto py-4">
                <nav class="space-y-1 px-2">
                    <Link 
                        v-for="item in navItems" 
                        :key="item.name"
                        :href="route(item.route)"
                        :class="[
                            route().current(item.active)
                                ? 'bg-indigo-50 text-indigo-700 border-indigo-600'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent',
                            'group flex items-center px-4 py-2 text-sm font-medium rounded-md border-l-4 transition-colors duration-150'
                        ]"
                    >
                        {{ item.name }}
                    </Link>
                </nav>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Top Navbar (Desktop User Menu + Mobile Hamburger) -->
            <header class="bg-white shadow flex items-center justify-between p-4 z-10 h-16 shrink-0">
                <!-- Mobile Logo / Trigger -->
                <div class="flex items-center md:hidden">
                    <Link :href="route('dashboard')">
                        <ApplicationLogo class="block h-8 w-auto fill-current text-gray-800" />
                    </Link>
                </div>

                <!-- Spacer for Desktop to align right -->
                <div class="hidden md:block flex-1"></div>

                <!-- User Dropdown (Desktop) -->
                <div class="hidden md:flex items-center ms-6">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                <div>{{ $page.props.auth.user.name }}</div>
                                <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')"> Perfil </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button"> Cerrar Sesión </DropdownLink>
                        </template>
                    </Dropdown>
                </div>

                 <!-- Mobile Hamburger -->
                <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <!-- Mobile Menu Dropdown (Overlay) -->
            <div v-if="showingNavigationDropdown" class="md:hidden fixed inset-0 z-40 bg-gray-800 bg-opacity-75" @click="showingNavigationDropdown = false"></div>
            <div v-show="showingNavigationDropdown" class="md:hidden fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform transition-transform duration-300">
                <div class="flex justify-between items-center px-4 py-4 border-b">
                    <span class="font-bold text-lg text-gray-800">Menú</span>
                    <button @click="showingNavigationDropdown = false" class="text-gray-500">X</button>
                </div>
                <nav class="px-2 py-4 space-y-1">
                     <Link 
                        v-for="item in navItems" 
                        :key="item.name"
                        :href="route(item.route)"
                        :class="[
                            route().current(item.active)
                                ? 'bg-indigo-50 text-indigo-700 border-indigo-600'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent',
                            'block px-4 py-2 text-base font-medium rounded-md border-l-4'
                        ]"
                        @click="showingNavigationDropdown = false"
                    >
                        {{ item.name }}
                    </Link>
                </nav>
                <div class="border-t border-gray-200 p-4">
                    <div class="font-medium text-base text-gray-800">{{ $page.props.auth.user.name }}</div>
                    <div class="font-medium text-sm text-gray-500 mb-2">{{ $page.props.auth.user.email }}</div>
                    <div class="mt-3 space-y-1">
                        <Link :href="route('profile.edit')" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-md">
                            Perfil
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-md">
                            Cerrar Sesión
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Page Header (for Breadcrumbs/Title if used) -->
            <div class="bg-gray-50 border-b border-gray-200" v-if="$slots.header">
                 <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </div>

            <!-- Scrollable Content -->
            <main class="flex-1 overflow-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
