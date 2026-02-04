<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

const form = useForm({
    title: '',
    is_published: false,
    seo_title: '',
    seo_description: '',
});

const submit = () => {
    form.post(route('admin.pages.store'));
};
</script>

<template>
    <Head title="Crear Página" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear Nueva Página
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <!-- Basic Info -->
                            <div>
                                <InputLabel value="Título de la Página" />
                                <TextInput v-model="form.title" type="text" class="mt-1 block w-full text-lg" required placeholder="Ej: Quiénes Somos" />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>

                            <!-- SEO & Status -->
                            <div class="border-t pt-4 mt-4">
                                <h3 class="font-medium text-gray-900 mb-4">Configuración SEO y Estado</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div>
                                            <InputLabel value="Título SEO (Opcional)" />
                                            <TextInput v-model="form.seo_title" type="text" class="mt-1 block w-full" placeholder="Título para buscadores" />
                                            <InputError :message="form.errors.seo_title" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel value="Descripción SEO (Opcional)" />
                                            <textarea v-model="form.seo_description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" placeholder="Descripción breve para resultados de búsqueda"></textarea>
                                            <InputError :message="form.errors.seo_description" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="flex items-start pt-6">
                                        <label class="flex items-center">
                                            <Checkbox v-model:checked="form.is_published" />
                                            <span class="ml-2 text-sm text-gray-600 font-bold">Activa / Publicada</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 mt-6">
                                <Link :href="route('admin.pages.index')">
                                    <SecondaryButton>Cancelar</SecondaryButton>
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Crear y Agregar Secciones
                                </PrimaryButton>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
