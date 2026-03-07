<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

const page = usePage();
const currentUrl = computed(() => page.url);

const isMobileMenuOpen = ref(false);

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    },
    mainMenu: {
        type: Object,
        default: () => null
    }
});

const backgroundStyle = computed(() => {
    const type = props.settings.background_type || 'color';
    const value = props.settings.background_value || '#f9fafb';

    if (type === 'image') {
        return { backgroundImage: `url('${value}')`, backgroundSize: 'cover', backgroundPosition: 'center', backgroundAttachment: 'fixed' };
    } else if (type === 'gradient') {
        return { background: value };
    } else {
        return { backgroundColor: value };
    }
});

// We can inject custom CSS in a style tag if needed, logic for that is complex in Vue. 
// For now, let's rely on the wrapper or simple scoped styles if user provides simple css related to body.
</script>

<template>
    <div class="min-h-screen flex flex-col font-sans text-gray-900 antialiased" :style="backgroundStyle">
        <!-- Optional: Custom CSS injection if really needed, though risky -->
        <component is="style" v-if="settings.custom_css">
            {{ settings.custom_css }}
        </component>

        <!-- Custom Header Content -->
        <!-- This is independent of the menu as requested -->
        <header v-if="settings.header_content" class="site-header flex justify-center w-full" v-html="settings.header_content"></header>

        <!-- Main Navigation (Independent) -->
        <!-- You might want to style this container or let the user style it if they want full control, 
             but typically the menu logic is app-controlled. -->
        <nav v-if="mainMenu && mainMenu.items && mainMenu.items.length > 0" class="site-navigation bg-white/80 backdrop-blur-sm border-b border-gray-100 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex w-full justify-center">
                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <template v-for="item in mainMenu.items" :key="item.id">
                                <Link 
                                    v-if="item.page_id && item.page" 
                                    :href="route('pages.show', item.page.slug)" 
                                    :class="[
                                        'inline-flex items-center px-1 pt-1 border-b-2 text-base font-medium leading-5 transition duration-150 ease-in-out focus:outline-none',
                                        currentUrl === '/pages/' + item.page.slug
                                            ? 'border-blue-500 text-blue-600 focus:border-blue-700'
                                            : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300 focus:text-gray-900 focus:border-gray-300'
                                    ]"
                                >
                                    {{ item.title }}
                                </Link>
                                <a 
                                    v-else 
                                    :href="item.url || '#'" 
                                    :target="item.target || '_self'"
                                    :class="[
                                        'inline-flex items-center px-1 pt-1 border-b-2 text-base font-medium leading-5 transition duration-150 ease-in-out focus:outline-none',
                                        currentUrl === item.url
                                            ? 'border-blue-500 text-blue-600 focus:border-blue-700'
                                            : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300 focus:text-gray-900 focus:border-gray-300'
                                    ]"
                                >
                                    {{ item.title }}
                                </a>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button 
                            @click="isMobileMenuOpen = !isMobileMenuOpen" 
                            type="button" 
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" 
                            aria-expanded="false"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': isMobileMenuOpen, 'inline-flex': !isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !isMobileMenuOpen, 'inline-flex': isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform -translate-y-4 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="transform translate-y-0 opacity-100"
                leave-to-class="transform -translate-y-4 opacity-0"
            >
                <div v-show="isMobileMenuOpen" class="sm:hidden border-t border-gray-200 absolute w-full bg-white shadow-lg z-40 origin-top">
                    <div class="pt-2 pb-3 space-y-1">
                        <template v-for="item in mainMenu.items" :key="item.id">
                            <Link 
                                v-if="item.page_id && item.page" 
                                :href="route('pages.show', item.page.slug)" 
                                :class="[
                                    'block pl-3 pr-4 py-2 border-l-4 text-lg font-medium transition duration-150 ease-in-out focus:outline-none',
                                    currentUrl === '/pages/' + item.page.slug
                                        ? 'border-blue-500 text-blue-700 bg-blue-50 focus:text-blue-800 focus:bg-blue-100 focus:border-blue-700'
                                        : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300'
                                ]"
                                @click="isMobileMenuOpen = false"
                            >
                                {{ item.title }}
                            </Link>
                            <a 
                                v-else 
                                :href="item.url || '#'" 
                                :target="item.target || '_self'"
                                :class="[
                                    'block pl-3 pr-4 py-2 border-l-4 text-lg font-medium transition duration-150 ease-in-out focus:outline-none',
                                    currentUrl === item.url
                                        ? 'border-blue-500 text-blue-700 bg-blue-50 focus:text-blue-800 focus:bg-blue-100 focus:border-blue-700'
                                        : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300'
                                ]"
                                @click="isMobileMenuOpen = false"
                            >
                                {{ item.title }}
                            </a>
                        </template>
                    </div>
                </div>
            </transition>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Custom Footer Content -->
        <footer v-if="settings.footer_content" class="site-footer flex justify-center w-full" v-html="settings.footer_content"></footer>
        
        <!-- Fallback Footer if no content -->
        <footer v-else class="bg-gray-800 text-white py-8 text-center">
            <p>&copy; {{ new Date().getFullYear() }} {{ settings.site_title || 'Impulso Norte' }}. Todos los derechos reservados.</p>
        </footer>
    </div>
</template>

<style scoped>
/* Basic resets for injected content */
.site-header :deep(img), .site-footer :deep(img) {
    max-width: 100%;
    height: auto;
}
</style>
