<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Carousel from '@/Components/Carousel.vue';

const props = defineProps({
    page: Object,
    settings: Object,
    mainMenu: Object,
});
</script>

<template>
    <Head>
        <title>{{ page.seo_title || page.title }}</title>
        <meta name="description" :content="page.seo_description || ''" />
    </Head>

    <PublicLayout :settings="settings" :mainMenu="mainMenu">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 mt-4">
            <!-- Dynamic Elements -->
            <div v-if="page.elements && page.elements.length > 0">
                <div v-for="element in page.elements" :key="element.id" class="mb-10">
                    
                    <!-- Title Element -->
                    <div v-if="element.type === 'title'">
                        <h1 v-if="element.settings?.style === 'primary'" class="titulo-primary mb-6 text-gray-900">
                            {{ element.content }}
                        </h1>
                        <h3 v-else-if="element.settings?.style === 'secondary'" class="titulo-secondary mb-6 text-gray-800">
                            {{ element.content }}
                        </h3>
                        <h2 v-else class="text-3xl font-bold text-gray-800 mb-6">
                            {{ element.content }}
                        </h2>
                    </div>

                    <!-- Content Element (Rich Text) -->
                    <div v-if="element.type === 'content'" class="prose prose-lg max-w-none text-gray-700" v-html="element.content">
                    </div>

                    <!-- Carousel Element -->
                    <div v-if="element.type === 'carousel'">
                        <!-- Debug: Uncomment if needed -->
                        <!-- <pre>{{ element.content }}</pre> -->
                        <Carousel :images="element.content" />
                    </div>

                </div>
            </div>

            <!-- Fallback Legacy Content -->
            <div v-else-if="page.content" class="prose prose-lg max-w-none text-gray-700" v-html="page.content"></div>
            
            <div v-else class="text-gray-500 italic text-center py-10">
                Página en construcción.
            </div>
        </div>
    </PublicLayout>
</template>
