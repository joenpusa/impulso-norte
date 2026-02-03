<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import ToastNotification from '@/Components/UI/ToastNotification.vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    settings: Object,
});

const activeTab = ref('header');
const showCode = ref({ header: false, footer: false });

const form = useForm({
    site_title: props.settings.site_title || '', // Keep for SEO
    header_content: props.settings.header_content || '',
    footer_content: props.settings.footer_content || '',
    background_type: props.settings.background_type || 'color', // color, image, gradient
    background_value: props.settings.background_value || '#f9fafb', // hex, url, or gradient string
    custom_css: props.settings.custom_css || '',
});

const submit = () => {
    form.post(route('admin.settings.store'), {
        preserveScroll: true,
        onSuccess: () => {
             // Handle success
        }
    });
};

const bgTypes = [
    { value: 'color', label: 'Color Sólido' },
    { value: 'image', label: 'Imagen (URL)' },
    { value: 'gradient', label: 'Degradado' },
];
</script>

<template>
    <Head title="Ajustes y Estructura" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración del Sitio</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!-- Tabs -->
                        <div class="border-b border-gray-200 mb-6">
                            <nav class="-mb-px flex space-x-8">
                                <button @click="activeTab = 'header'" :class="{'border-blue-500 text-blue-600': activeTab === 'header', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'header'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Header
                                </button>
                                <button @click="activeTab = 'footer'" :class="{'border-blue-500 text-blue-600': activeTab === 'footer', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'footer'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Footer
                                </button>
                                <button @click="activeTab = 'styles'" :class="{'border-blue-500 text-blue-600': activeTab === 'styles', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'styles'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Fondo y Estilos
                                </button>
                            </nav>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- Header Tab -->
                            <div v-show="activeTab === 'header'" class="space-y-6">
                                <div>
                                    <InputLabel value="Título del Sitio (Para SEO)" class="mb-2"/>
                                    <TextInput v-model="form.site_title" type="text" class="mt-1 block w-full" placeholder="Ej: Impulso Norte" />
                                    <p class="text-xs text-gray-500 mt-1">Este título aparecerá en la pestaña del navegador.</p>
                                </div>
                                <div class="mt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <InputLabel value="Contenido del Header" />
                                        <button type="button" @click="showCode.header = !showCode.header" class="text-xs text-blue-600 hover:text-blue-800 underline">
                                            {{ showCode.header ? 'Ver Editor Visual' : 'Ver Código HTML' }}
                                        </button>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-2">Diseñe el encabezado aquí. Puede agregar logos usando URLs de la sección Multimedia.</p>
                                    
                                    <div v-if="!showCode.header" class="bg-white">
                                        <QuillEditor theme="snow" v-model:content="form.header_content" contentType="html" toolbar="full" style="height: 300px;" />
                                    </div>
                                    <div v-else>
                                        <textarea v-model="form.header_content" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-mono text-sm h-[342px]" placeholder="<header>...</header>"></textarea>
                                        <p class="text-xs text-orange-500 mt-1">⚠ Editando código fuente HTML directamente.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Tab -->
                            <div v-show="activeTab === 'footer'" class="space-y-6">
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <InputLabel value="Contenido del Footer" />
                                        <button type="button" @click="showCode.footer = !showCode.footer" class="text-xs text-blue-600 hover:text-blue-800 underline">
                                            {{ showCode.footer ? 'Ver Editor Visual' : 'Ver Código HTML' }}
                                        </button>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-2">Diseñe el pie de página aquí.</p>
                                    
                                    <div v-if="!showCode.footer" class="bg-white">
                                        <QuillEditor theme="snow" v-model:content="form.footer_content" contentType="html" toolbar="full" style="height: 300px;" />
                                    </div>
                                    <div v-else>
                                        <textarea v-model="form.footer_content" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-mono text-sm h-[342px]" placeholder="<footer>...</footer>"></textarea>
                                         <p class="text-xs text-orange-500 mt-1">⚠ Editando código fuente HTML directamente.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Styles Tab -->
                            <div v-show="activeTab === 'styles'" class="space-y-6">
                                <div>
                                    <InputLabel value="Tipo de Fondo" class="mb-2"/>
                                    <select v-model="form.background_type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full md:w-1/3">
                                        <option v-for="type in bgTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
                                    </select>
                                </div>

                                <div v-if="form.background_type === 'color'">
                                    <InputLabel value="Color de Fondo" class="mb-2" />
                                    <div class="flex items-center gap-4">
                                        <input type="color" v-model="form.background_value" class="h-10 w-20 border rounded cursor-pointer" />
                                        <TextInput v-model="form.background_value" type="text" class="w-40" />
                                    </div>
                                </div>

                                <div v-if="form.background_type === 'image'">
                                    <InputLabel value="URL de la Imagen" class="mb-2" />
                                    <TextInput v-model="form.background_value" type="text" class="w-full" placeholder="https://..." />
                                    <p class="text-xs text-gray-500 mt-1">Copie la URL desde la sección Multimedia.</p>
                                </div>

                                <div v-if="form.background_type === 'gradient'">
                                    <InputLabel value="CSS del Degradado" class="mb-2" />
                                    <TextInput v-model="form.background_value" type="text" class="w-full" placeholder="linear-gradient(to right, #ff7e5f, #feb47b)" />
                                    <div class="mt-2 h-20 w-full rounded border" :style="{ background: form.background_value }"></div>
                                </div>

                                <div class="mt-6">
                                    <InputLabel value="Estilos CSS Personalizados Globales (Body)" class="mb-2" />
                                    <textarea v-model="form.custom_css" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-mono text-sm h-32" placeholder="body { font-family: 'Roboto', sans-serif; }"></textarea>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Guardar Configuración
                                </PrimaryButton>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <ToastNotification />
    </AuthenticatedLayout>
</template>
