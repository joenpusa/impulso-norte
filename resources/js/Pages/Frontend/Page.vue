<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

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
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <article class="prose prose-lg max-w-none bg-white/90 p-8 rounded-lg shadow-sm">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-6">{{ page.title }}</h1>
                <div class="mt-6 text-gray-700" v-html="page.content"></div>
            </article>
        </div>
    </PublicLayout>
</template>
