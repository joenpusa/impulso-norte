<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

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
        <header v-if="settings.header_content" class="site-header" v-html="settings.header_content"></header>

        <!-- Main Navigation (Independent) -->
        <!-- You might want to style this container or let the user style it if they want full control, 
             but typically the menu logic is app-controlled. -->
        <nav v-if="mainMenu && mainMenu.items && mainMenu.items.length > 0" class="site-navigation bg-white/80 backdrop-blur-sm border-b border-gray-100 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <template v-for="item in mainMenu.items" :key="item.id">
                                <Link 
                                    v-if="item.page_id && item.page" 
                                    :href="route('pages.show', item.page.slug)" 
                                    class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                                >
                                    {{ item.title }}
                                </Link>
                                <a 
                                    v-else 
                                    :href="item.url || '#'" 
                                    :target="item.target || '_self'"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                                >
                                    {{ item.title }}
                                </a>
                            </template>
                        </div>
                    </div>
                    <!-- Mobile Menu Button could go here -->
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Custom Footer Content -->
        <footer v-if="settings.footer_content" class="site-footer" v-html="settings.footer_content"></footer>
        
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
