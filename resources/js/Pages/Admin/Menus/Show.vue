<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import ToastNotification from '@/Components/UI/ToastNotification.vue';

const props = defineProps({
    menu: Object,
});

// Item Modal State
const managingItem = ref(false);
const editingItem = ref(false);
const itemForm = useForm({
    id: null,
    menu_id: props.menu.id,
    parent_id: null,
    title: '',
    url: '',
    page_id: null,
    target: '_self',
    order: 0,
});

const openCreateItemModal = (parentId = null) => {
    editingItem.value = false;
    itemForm.reset();
    itemForm.menu_id = props.menu.id;
    itemForm.parent_id = parentId;
    managingItem.value = true;
};

const openEditItemModal = (item) => {
    editingItem.value = true;
    itemForm.id = item.id;
    itemForm.menu_id = item.menu_id;
    itemForm.parent_id = item.parent_id;
    itemForm.title = item.title;
    itemForm.url = item.url;
    itemForm.page_id = item.page_id;
    itemForm.target = item.target || '_self';
    itemForm.order = item.order;
    managingItem.value = true;
};

const submitItemForm = () => {
    if (editingItem.value) {
        itemForm.put(route('admin.menu-items.update', itemForm.id), {
            onSuccess: () => managingItem.value = false,
        });
    } else {
        itemForm.post(route('admin.menu-items.store'), {
            onSuccess: () => managingItem.value = false,
        });
    }
};

const confirmingItemDeletion = ref(false);
const itemToDelete = ref(null);

const deleteItem = () => {
    router.delete(route('admin.menu-items.destroy', itemToDelete.value), {
        onSuccess: () => {
            confirmingItemDeletion.value = false;
            itemToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="Gestionar Ítems del Menú" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ítems: {{ menu.name }}
                </h2>
                <Link :href="route('admin.menus.index')" class="text-sm text-gray-600 hover:text-gray-900">
                    <- Volver a Menús
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <div class="mb-6">
                            <PrimaryButton @click="openCreateItemModal(null)">+ Añadir Ítem Principal</PrimaryButton>
                        </div>

                        <!-- Nested List -->
                        <div class="space-y-4">
                            <div v-for="item in menu.items" :key="item.id" class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <div class="font-medium text-gray-900">
                                        {{ item.title }}
                                        <span class="text-xs text-gray-500 ml-2" v-if="item.url || item.page_id">
                                            ({{ item.page_id ? 'Página ID: ' + item.page_id : item.url }})
                                        </span>
                                    </div>
                                    <div class="flex gap-2 text-sm">
                                        <button @click="openCreateItemModal(item.id)" class="text-green-600 hover:underline">+ Sub-ítem</button>
                                        <button @click="openEditItemModal(item)" class="text-blue-600 hover:underline">Editar</button>
                                        <button @click="confirmingItemDeletion = true; itemToDelete = item.id" class="text-red-600 hover:underline">Eliminar</button>
                                    </div>
                                </div>
                                
                                <!-- Children -->
                                <div v-if="item.children && item.children.length > 0" class="mt-3 pl-6 border-l-2 border-gray-200 space-y-2">
                                    <div v-for="child in item.children" :key="child.id" class="bg-white border rounded p-3 flex justify-between items-center">
                                        <div class="text-sm text-gray-700">
                                            {{ child.title }}
                                             <span class="text-xs text-gray-500 ml-2">
                                                {{ child.page_id ? 'Página' : (child.url ? 'Link' : '') }}
                                            </span>
                                        </div>
                                        <div class="flex gap-2 text-xs">
                                            <button @click="openEditItemModal(child)" class="text-blue-600 hover:underline">Editar</button>
                                            <button @click="confirmingItemDeletion = true; itemToDelete = child.id" class="text-red-600 hover:underline">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="menu.items.length === 0" class="text-center py-8 text-gray-500">
                                No hay ítems en este menú.
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Item Modal -->
        <Modal :show="managingItem" @close="managingItem = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ editingItem ? 'Editar Ítem' : 'Añadir Ítem' }}</h2>
                <div class="mt-6 space-y-4">
                    <div>
                        <InputLabel value="Título" />
                        <TextInput v-model="itemForm.title" type="text" class="mt-1 block w-full" />
                        <InputError :message="itemForm.errors.title" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel value="URL (Opcional si usas página)" />
                        <div class="text-xs text-gray-500 mb-1">Para enlaces externos o personalizados.</div>
                        <TextInput v-model="itemForm.url" type="text" class="mt-1 block w-full" placeholder="https://..." />
                        <InputError :message="itemForm.errors.url" class="mt-2" />
                    </div>
                     <div>
                        <InputLabel value="Target" />
                        <select v-model="itemForm.target" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="_self">Misma Ventana</option>
                            <option value="_blank">Nueva Pestaña</option>
                        </select>
                         <InputError :message="itemForm.errors.target" class="mt-2" />
                    </div>
                    <!-- Future: Page Selector logic matching Admin/Pages -->
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="managingItem = false">Cancelar</SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': itemForm.processing }" :disabled="itemForm.processing" @click="submitItemForm">
                        Guardar
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Delete Modal -->
         <Modal :show="confirmingItemDeletion" @close="confirmingItemDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 text-red-600">Eliminar Ítem</h2>
                <p class="mt-1 text-sm text-gray-600">
                    ¿Estás seguro?
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="confirmingItemDeletion = false">Cancelar</SecondaryButton>
                    <DangerButton class="ml-3" @click="deleteItem">Eliminar</DangerButton>
                </div>
            </div>
        </Modal>

        <ToastNotification />
    </AuthenticatedLayout>
</template>
