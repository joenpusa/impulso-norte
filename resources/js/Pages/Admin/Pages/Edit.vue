<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { watch, ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    page: Object, // Optional, implies Edit mode if present
});

const form = useForm({
    title: props.page ? props.page.title : '',
    slug: props.page ? props.page.slug : '',
    content: props.page ? props.page.content : '',
    is_published: props.page ? Boolean(props.page.is_published) : false,
    seo_title: props.page ? props.page.seo_title : '',
    seo_description: props.page ? props.page.seo_description : '',
});

// Auto-generate slug from title if creating
if (!props.page) {
    watch(() => form.title, (newTitle) => {
        form.slug = newTitle
            .toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    });
}

const submit = () => {
    if (props.page) {
        form.put(route('admin.pages.update', props.page.id));
    } else {
        form.post(route('admin.pages.store'));
    }
};
</script>

<template>
    <Head :title="page ? 'Editar Página' : 'Crear Página'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ page ? 'Editar Página' : 'Crear Nueva Página' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel value="Título" />
                                    <TextInput v-model="form.title" type="text" class="mt-1 block w-full text-lg font-semibold" required />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel value="Slug (URL)" />
                                    <TextInput v-model="form.slug" type="text" class="mt-1 block w-full bg-gray-50 text-gray-600" required />
                                    <InputError :message="form.errors.slug" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <InputLabel value="Contenido" class="mb-2" />
                                <!-- Rich Text Editor -->
                                <RichTextEditor v-model="form.content" />
                                <InputError :message="form.errors.content" class="mt-2" />
                            </div>

                            <div class="border-t pt-4 mt-4">
                                <h3 class="font-medium text-gray-900 mb-4">Configuración SEO y Estado</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div>
                                            <InputLabel value="Título SEO (Meta Title)" />
                                            <TextInput v-model="form.seo_title" type="text" class="mt-1 block w-full" placeholder="Dejar vacío para usar el título de la página" />
                                        </div>
                                        <div>
                                            <InputLabel value="Descripción SEO (Meta Description)" />
                                            <textarea v-model="form.seo_description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="flex items-start pt-6">
                                        <label class="flex items-center">
                                            <Checkbox v-model:checked="form.is_published" />
                                            <span class="ml-2 text-sm text-gray-600 font-bold">Publicar Inmediatamente</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 mt-6">
                                <Link :href="route('admin.pages.index')">
                                    <SecondaryButton>Cancelar</SecondaryButton>
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ page ? 'Guardar Cambios' : 'Crear Página' }}
                                </PrimaryButton>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
