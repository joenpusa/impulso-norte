<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import ToastNotification from '@/Components/UI/ToastNotification.vue';

const props = defineProps({
    files: Object,
    filters: Object,
});

// Search State
const search = ref(props.filters.search || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

// Debounced Search Watcher could be added here, but simple form submit is safer for now.
const submitSearch = () => {
    router.get(route('admin.media.index'), {
        search: search.value,
        date_from: dateFrom.value,
        date_to: dateTo.value,
    }, { preserveState: true, preserveScroll: true });
};

const clearSearch = () => {
    search.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    submitSearch();
};

// Upload Modal State
const uploading = ref(false);
const uploadForm = useForm({
    file: null,
    title: '',
});

const submitUpload = () => {
    uploadForm.post(route('admin.media.store'), {
        onSuccess: () => {
            uploading.value = false;
            uploadForm.reset();
        },
    });
};

// Delete Modal State
const confirmingDeletion = ref(false);
const itemToDelete = ref(null);

const confirmDelete = (id) => {
    itemToDelete.value = id;
    confirmingDeletion.value = true;
};

const deleteItem = () => {
    router.delete(route('admin.media.destroy', itemToDelete.value), {
        onSuccess: () => {
            confirmingDeletion.value = false;
            itemToDelete.value = null;
        },
    });
};

// Copy URL
const copyUrl = (url) => {
    if (!url) return;

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(() => {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: 'Enlace copiado al portapapeles', type: 'success' }
            }));
        }).catch((err) => {
            console.error('Copy failed', err);
            fallbackCopyTextToClipboard(url);
        });
    } else {
        fallbackCopyTextToClipboard(url);
    }
};

const fallbackCopyTextToClipboard = (text) => {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    // Ensure it's not visible but part of the DOM
    textArea.style.position = "fixed";
    textArea.style.left = "-9999px";
    textArea.style.top = "0";
    document.body.appendChild(textArea);
    
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: 'Enlace copiado al portapapeles (fallback)', type: 'success' }
            }));
        } else {
            throw new Error('Fallback copy failed');
        }
    } catch (err) {
        console.error('Fallback copy error', err);
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { message: 'Error al actualizar el portapapeles', type: 'error' }
        }));
    }

    document.body.removeChild(textArea);
};
</script>

<template>
    <Head title="Multimedia" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Multimedia / Archivos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <!-- Top Action: Upload -->
                        <div class="mb-4">
                            <PrimaryButton @click="uploading = true" class="w-full sm:w-auto justify-center">
                                <span class="mr-2">+</span> Subir Nuevo Archivo
                            </PrimaryButton>
                        </div>

                        <!-- Search Bar -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <InputLabel value="Nombre" class="mb-1" />
                                <TextInput v-model="search" type="text" class="w-full text-sm" placeholder="Buscar..." @keyup.enter="submitSearch" />
                            </div>
                            <div>
                                <InputLabel value="Desde" class="mb-1" />
                                <TextInput v-model="dateFrom" type="date" class="w-full text-sm" />
                            </div>
                            <div>
                                <InputLabel value="Hasta" class="mb-1" />
                                <TextInput v-model="dateTo" type="date" class="w-full text-sm" />
                            </div>
                            <div class="flex gap-2">
                                <SecondaryButton @click="submitSearch" class="h-[42px]">Buscar</SecondaryButton>
                                <SecondaryButton @click="clearSearch" class="h-[42px]">Limpiar</SecondaryButton>
                            </div>
                        </div>

                        <!-- Gallery Grid -->
                        <div v-if="files.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <div v-for="file in files.data" :key="file.id" class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition bg-white group relative">
                                <div class="h-40 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                                    <img v-if="file.type.startsWith('image/')" :src="file.url" :alt="file.title" class="object-cover w-full h-full" />
                                    <div v-else class="text-gray-400 text-center p-4">
                                        <span class="block text-xs uppercase font-bold">{{ file.type }}</span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h4 class="font-bold text-sm mb-1 truncate" :title="file.title">{{ file.title }}</h4>
                                    <p class="text-xs text-gray-500 mb-3">{{ new Date(file.created_at).toLocaleDateString() }}</p>
                                    
                                    <div class="flex justify-between items-center gap-2">
                                        <button @click="copyUrl(file.url)" class="flex-1 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold py-2 px-3 rounded border border-gray-300 shadow-sm transition">
                                            Copiar URL
                                        </button>
                                        <button @click="confirmDelete(file.id)" class="text-red-500 hover:text-red-700 p-2 border rounded hover:bg-red-50">
                                            🗑
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            No se encontraron archivos.
                        </div>

                        <!-- Pagination -->
                        <div v-if="files.links.length > 3" class="mt-6 flex justify-center space-x-1">
                            <template v-for="(link, key) in files.links" :key="key">
                                <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                                <Link v-else class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-blue-700 text-white': link.active, 'bg-white': !link.active }" :href="link.url" v-html="link.label" />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        <Modal :show="uploading" @close="uploading = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Subir Nuevo Archivo</h2>
                <div class="mt-6">
                    <div class="mb-4">
                        <InputLabel value="Archivo" />
                        <input type="file" @input="uploadForm.file = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded p-1" />
                        <InputError :message="uploadForm.errors.file" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <InputLabel value="Título (Opcional)" />
                        <TextInput v-model="uploadForm.title" type="text" class="mt-1 block w-full" placeholder="Descripción" />
                        <InputError :message="uploadForm.errors.title" class="mt-2" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="uploading = false">Cancelar</SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': uploadForm.processing }" :disabled="uploadForm.processing" @click="submitUpload">
                        Subir
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 text-red-600">Eliminar Archivo</h2>
                <p class="mt-1 text-sm text-gray-600">
                    ¿Estás seguro de que deseas eliminar este archivo? Esta acción es irreversible.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="confirmingDeletion = false">Cancelar</SecondaryButton>
                    <DangerButton class="ml-3" @click="deleteItem">Eliminar</DangerButton>
                </div>
            </div>
        </Modal>

        <ToastNotification />
    </AuthenticatedLayout>
</template>
