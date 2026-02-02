<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue'; // Need to make sure this component exists in Breeze
import ToastNotification from '@/Components/UI/ToastNotification.vue';

const props = defineProps({
    menus: Object,
});

// Create/Edit/Delete State
const managingMenu = ref(false);
const editing = ref(false);
const form = useForm({
    id: null,
    name: '',
    location: '',
    is_active: true,
});

const openCreateModal = () => {
    editing.value = false;
    form.reset();
    form.is_active = true;
    managingMenu.value = true;
};

const openEditModal = (menu) => {
    editing.value = true;
    form.id = menu.id;
    form.name = menu.name;
    form.location = menu.location || '';
    form.is_active = Boolean(menu.is_active);
    managingMenu.value = true;
};

const submitForm = () => {
    if (editing.value) {
        form.put(route('admin.menus.update', form.id), {
            onSuccess: () => managingMenu.value = false,
        });
    } else {
        form.post(route('admin.menus.store'), {
            onSuccess: () => managingMenu.value = false,
        });
    }
};

const confirmingDeletion = ref(false);
const itemToDelete = ref(null);

const deleteItem = () => {
    router.delete(route('admin.menus.destroy', itemToDelete.value), {
        onSuccess: () => {
            confirmingDeletion.value = false;
            itemToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Menús" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Menús</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <div class="mb-4">
                            <PrimaryButton @click="openCreateModal">+ Crear Nuevo Menú</PrimaryButton>
                        </div>

                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Nombre</th>
                                        <th scope="col" class="px-6 py-3">Ubicación</th>
                                        <th scope="col" class="px-6 py-3">Estado</th>
                                        <th scope="col" class="px-6 py-3">Ítems</th>
                                        <th scope="col" class="px-6 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="menu in menus.data" :key="menu.id" class="bg-white border-b hover:bg-gray-50">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            {{ menu.name }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ menu.location || '---' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span v-if="menu.is_active" class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">Activo</span>
                                            <span v-else class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">Inactivo</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <Link :href="route('admin.menus.show', menu.id)" class="text-blue-600 hover:underline">
                                                Gestionar Ítems ->
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 flex gap-2">
                                            <button @click="openEditModal(menu)" class="font-medium text-blue-600 hover:underline">Editar</button>
                                            <button @click="confirmingDeletion = true; itemToDelete = menu.id" class="font-medium text-red-600 hover:underline">Eliminar</button>
                                        </td>
                                    </tr>
                                    <tr v-if="menus.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center">No hay menús creados.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="managingMenu" @close="managingMenu = false">
             <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ editing ? 'Editar Menú' : 'Crear Nuevo Menú' }}</h2>
                <div class="mt-6 space-y-4">
                    <div>
                        <InputLabel value="Nombre del Menú" />
                        <TextInput v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Ej: Menú Principal" />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="Ubicación" />
                         <select v-model="form.location" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Ninguna</option>
                            <option value="header">Header (Principal)</option>
                            <option value="footer">Footer</option>
                        </select>
                        <InputError :message="form.errors.location" class="mt-2" />
                    </div>
                    <div>
                         <label class="flex items-center">
                            <input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">Activo</span>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="managingMenu = false">Cancelar</SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submitForm">
                        Guardar
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Delete Modal -->
         <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 text-red-600">Eliminar Menú</h2>
                <p class="mt-1 text-sm text-gray-600">
                    ¿Estás seguro? Esto eliminará también todos los ítems asociados.
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
