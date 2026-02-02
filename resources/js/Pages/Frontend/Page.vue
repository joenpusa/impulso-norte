<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    page: Object,
    settings: Object,
    mainMenu: Object,
});

// Styles computed from settings
const customStyles = computed(() => {
    return {
        '--header-bg': props.settings.header_bg_color || '#ffffff',
        '--footer-bg': props.settings.footer_bg_color || '#1f2937',
        '--primary-color': props.settings.primary_color || '#3b82f6',
    };
});
</script>

<template>
    <Head>
        <title>{{ page.seo_title || page.title }}</title>
        <meta name="description" :content="page.seo_description || ''" />
    </Head>

    <div class="min-h-screen flex flex-col bg-gray-50" :style="customStyles">
        
        <!-- HEADER -->
        <header class="bg-[var(--header-bg)] shadow z-10 p-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <Link href="/">
                        <img v-if="settings.header_logo" :src="'/storage/' + settings.header_logo" alt="Logo" class="h-10 w-auto" />
                        <span v-else class="text-xl font-bold text-gray-800">{{ settings.site_title || 'Impulso Norte' }}</span>
                    </Link>
                </div>

                <!-- Menu -->
                <nav v-if="mainMenu && mainMenu.items" class="hidden md:flex space-x-6">
                    <template v-for="item in mainMenu.items" :key="item.id">
                        <Link 
                            v-if="item.page_id && item.page" 
                            :href="route('pages.show', item.page.slug)" 
                            class="text-gray-600 hover:text-[var(--primary-color)] font-medium transition"
                        >
                            {{ item.title }}
                        </Link>
                        <a 
                            v-else 
                            :href="item.url || '#'" 
                            :target="item.target || '_self'"
                            class="text-gray-600 hover:text-[var(--primary-color)] font-medium transition"
                        >
                            {{ item.title }}
                        </a>
                    </template>
                </nav>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-grow">
            <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <article class="prose prose-lg max-w-none bg-white p-8 rounded-lg shadow-sm">
                    <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ page.title }}</h1>
                    <div class="mt-6 text-gray-700" v-html="page.content"></div>
                </article>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="bg-[var(--footer-bg)] text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Info -->
                <div>
                   <h3 class="text-lg font-bold mb-4">{{ settings.site_title }}</h3>
                   <p class="text-sm text-gray-300">{{ settings.footer_text }}</p>
                </div>
                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Contacto</h3>
                    <p v-if="settings.contact_email" class="text-sm text-gray-300">Email: {{ settings.contact_email }}</p>
                    <p v-if="settings.phone" class="text-sm text-gray-300">Tel: {{ settings.phone }}</p>
                </div>
                 <!-- Social -->
                 <div>
                    <h3 class="text-lg font-bold mb-4">Síguenos</h3>
                    <div class="flex space-x-4">
                        <a v-if="settings.facebook_url" :href="settings.facebook_url" target="_blank" class="text-gray-300 hover:text-white">Facebook</a>
                        <a v-if="settings.instagram_url" :href="settings.instagram_url" target="_blank" class="text-gray-300 hover:text-white">Instagram</a>
                        <a v-if="settings.twitter_url" :href="settings.twitter_url" target="_blank" class="text-gray-300 hover:text-white">Twitter</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
