<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import Spinner from '@/Components/UI/Spinner.vue';
import ToastNotification from '@/Components/UI/ToastNotification.vue';

const props = defineProps({
    page: Object,
});

// Basic Info Form
const form = useForm({
    title: props.page.title || '',
    is_published: props.page.is_published ? true : false,
    seo_title: props.page.seo_title || '',
    seo_description: props.page.seo_description || '',
});

const submitBasicInfo = () => {
    form.put(route('admin.pages.update', props.page.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Toast automatically handled by ToastNotification via flash prop
        }
    });
};

// --- Elements Management ---
const isSavingElements = ref(false);
const elements = ref(props.page.elements ? JSON.parse(JSON.stringify(props.page.elements)) : []);

const addElement = (type) => {
    let content = '';
    if (type === 'carousel') {
        content = [];
    }
    
    elements.value.push({
        id: null, // New element
        type: type,
        content: content,
        order: elements.value.length,
    });
};

const removeElement = (index) => {
    if (confirm('¿Eliminar esta sección?')) {
        elements.value.splice(index, 1);
        updateOrder();
    }
};

const moveUp = (index) => {
    if (index > 0) {
        const item = elements.value[index];
        elements.value.splice(index, 1);
        elements.value.splice(index - 1, 0, item);
        updateOrder();
    }
};

const moveDown = (index) => {
    if (index < elements.value.length - 1) {
        const item = elements.value[index];
        elements.value.splice(index, 1);
        elements.value.splice(index + 1, 0, item);
        updateOrder();
    }
};

const updateOrder = () => {
    elements.value.forEach((el, index) => {
        el.order = index;
    });
};

// Carousel Logic
const addImageToCarousel = (element) => {
    const url = prompt('Ingrese URL de la imagen:');
    if (url) {
        if (!Array.isArray(element.content)) element.content = [];
        element.content.push(url);
    }
};
const removeImageFromCarousel = (element, imgIndex) => {
    element.content.splice(imgIndex, 1);
};

const saveElements = () => {
    if (isSavingElements.value) return;

    updateOrder();
    isSavingElements.value = true;

    router.put(route('admin.pages.update', props.page.id), {
        elements: elements.value
    }, {
        preserveScroll: true,
        onFinish: () => {
            isSavingElements.value = false;
        }
    });
};

</script>

<template>
    <Head title="Editar Página" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editar Página: {{ page.title }}
                </h2>
                <a :href="`/pages/${page.slug}`" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900" v-if="page.slug">
                    Ver Página
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- 1. Basic Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Información General</h3>
                        <form @submit.prevent="submitBasicInfo" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel value="Título" />
                                    <TextInput v-model="form.title" type="text" class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>
                                <div class="flex items-end pb-2">
                                    <label class="flex items-center">
                                        <Checkbox v-model:checked="form.is_published" />
                                        <span class="ml-2 text-sm text-gray-600 font-bold">Página Activa</span>
                                    </label>
                                </div>
                                <div>
                                    <InputLabel value="Título SEO" />
                                    <TextInput v-model="form.seo_title" type="text" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <InputLabel value="Descripción SEO" />
                                    <TextInput v-model="form.seo_description" type="text" class="mt-1 block w-full" />
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <PrimaryButton :class="{ 'opacity-75': form.processing }" :disabled="form.processing">
                                    <Spinner v-if="form.processing" />
                                    Guardar Información Básica
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 2. Sections Builder -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center border-b pb-2 mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Secciones de la Página</h3>
                            <div class="flex gap-2">
                                <SecondaryButton @click="addElement('title')" class="text-xs">
                                    + Título
                                </SecondaryButton>
                                <SecondaryButton @click="addElement('content')" class="text-xs">
                                    + Contenido
                                </SecondaryButton>
                                <SecondaryButton @click="addElement('carousel')" class="text-xs">
                                    + Carrusel
                                </SecondaryButton>
                            </div>
                        </div>

                        <!-- Elements List -->
                        <div class="space-y-6" v-if="elements.length > 0">
                            <div v-for="(element, index) in elements" :key="index" class="border rounded-lg p-4 bg-gray-50 relative group">
                                
                                <!-- Toolbar -->
                                <div class="absolute top-2 right-2 flex gap-1 opacity-50 group-hover:opacity-100 transition-opacity">
                                    <button @click="moveUp(index)" class="p-1 hover:bg-gray-200 rounded" title="Subir">⬆️</button>
                                    <button @click="moveDown(index)" class="p-1 hover:bg-gray-200 rounded" title="Bajar">⬇️</button>
                                    <button @click="removeElement(index)" class="p-1 hover:bg-red-200 rounded text-red-600" title="Eliminar">🗑️</button>
                                </div>

                                <div class="pr-24">
                                    <span class="text-xs font-bold uppercase text-gray-400 tracking-wider mb-2 block">{{ element.type }}</span>
                                    
                                    <!-- Type: Title -->
                                    <div v-if="element.type === 'title'">
                                        <TextInput v-model="element.content" type="text" class="w-full text-lg font-semibold" placeholder="Escriba el título de la sección..." />
                                    </div>

                                    <!-- Type: Content -->
                                    <div v-if="element.type === 'content'">
                                        <RichTextEditor v-model="element.content" placeholder="Escriba el contenido..." />
                                    </div>

                                    <!-- Type: Carousel -->
                                    <div v-if="element.type === 'carousel'">
                                        <div class="flex flex-wrap gap-2 mb-2">
                                            <div v-for="(img, imgIdx) in element.content" :key="imgIdx" class="relative w-24 h-24 border rounded overflow-hidden">
                                                <img :src="img" class="w-full h-full object-cover" />
                                                <button @click="removeImageFromCarousel(element, imgIdx)" class="absolute top-0 right-0 bg-red-500 text-white p-0.5 text-xs rounded-bl">x</button>
                                            </div>
                                            <button @click="addImageToCarousel(element)" class="w-24 h-24 border-2 border-dashed border-gray-300 rounded flex items-center justify-center text-gray-400 hover:border-gray-500 hover:text-gray-500">
                                                + img
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500">Haga clic en "+" para agregar URL de imagen.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-400 border-2 border-dashed rounded-lg">
                            No hay secciones. Agregue una usando los botones de arriba.
                        </div>

                        <div class="mt-6 flex justify-end">
                            <PrimaryButton @click="saveElements" :class="{ 'opacity-75': isSavingElements }" :disabled="isSavingElements">
                                <Spinner v-if="isSavingElements" />
                                <span v-if="isSavingElements">Guardando...</span>
                                <span v-else>Guardar Secciones</span>
                            </PrimaryButton>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <ToastNotification />
    </AuthenticatedLayout>
</template>
