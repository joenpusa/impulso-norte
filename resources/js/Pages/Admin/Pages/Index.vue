<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ToastNotification from '@/Components/UI/ToastNotification.vue';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    pages: Object,
});

const confirmingDeletion = ref(false);
const itemToDelete = ref(null);

const deleteItem = () => {
    router.delete(route('admin.pages.destroy', itemToDelete.value), {
        onSuccess: () => {
            confirmingDeletion.value = false;
            itemToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Páginas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Páginas (CMS)</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <div class="mb-4">
                            <Link :href="route('admin.pages.create')">
                                <PrimaryButton>+ Crear Nueva Página</PrimaryButton>
                            </Link>
                        </div>

                         <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Título</th>
                                        <th scope="col" class="px-6 py-3">Slug</th>
                                        <th scope="col" class="px-6 py-3">Estado</th>
                                        <th scope="col" class="px-6 py-3">Última Modificación</th>
                                        <th scope="col" class="px-6 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="page in pages.data" :key="page.id" class="bg-white border-b hover:bg-gray-50">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            {{ page.title }}
                                        </th>
                                        <td class="px-6 py-4">
                                            /{{ page.slug }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span v-if="page.is_published" class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">Publicado</span>
                                            <span v-else class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">Borrador</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ new Date(page.updated_at).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 flex gap-2">
                                            <a :href="route('pages.show', page.slug)" target="_blank" class="font-medium text-gray-600 hover:underline">Ver</a>
                                            <Link :href="route('admin.pages.edit', page.id)" class="font-medium text-blue-600 hover:underline">Editar</Link>
                                            <button @click="confirmingDeletion = true; itemToDelete = page.id" class="font-medium text-red-600 hover:underline">Eliminar</button>
                                        </td>
                                    </tr>
                                     <tr v-if="pages.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center">No hay páginas creadas.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
         <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 text-red-600">Eliminar Página</h2>
                <p class="mt-1 text-sm text-gray-600">
                    ¿Estás seguro? Esta acción es irreversible.
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
