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
    menu: Object,
    pages: Array, // Passed from controller
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
    link_type: 'external', // 'external' or 'page' - local state helper
});

const openCreateItemModal = (parentId = null) => {
    editingItem.value = false;
    itemForm.reset();
    itemForm.menu_id = props.menu.id;
    itemForm.parent_id = parentId;
    itemForm.link_type = 'external';
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
    // Determine link type based on data
    if (item.page_id) {
        itemForm.link_type = 'page';
    } else {
        itemForm.link_type = 'external';
    }
    managingItem.value = true;
};

// Watch for Page selection to auto-fill URL/Slug logic
watch(() => itemForm.page_id, (newPageId) => {
    if (itemForm.link_type === 'page' && newPageId) {
        const selectedPage = props.pages.find(p => p.id === newPageId);
        if (selectedPage) {
            // User requested: "el slug o url sebe ser el titulo de la pagina ajustado para version url web"
            // Usually we just store page_id and backend resolves URL, OR we store the slug in the URL field.
            // If we store page_id, the frontend menu renderer should handle the link generation via route('pages.show', page.slug).
            // However, the request implies filling the URL field or dealing with slug.
            // Let's assume we fill the URL field with the slug for visibility, or keep it synced.
            // Ideally, if page_id is set, URL field might be ignored or used as override. Use page slug.
            // I'll auto-fill the URL field with the slug but keep it editable if needed? 
            // Actually usually if page_id is used, URL is derived. 
            // But let's auto-fill the URL field with the page slug just in case the system relies on 'url' column.
            // If the page has a slug, use it. If not, generate from title.
            
            // Wait, existing logic in other projects usually uses `url` column for custom links and `page_id` relationship for known pages.
            // I will clear URL field if page is selected to avoid confusion, OR set it to the slug.
            // Let's set it to the slug as a visual indicator.
            itemForm.url = selectedPage.slug || ''; 
            
            // Also user might want to auto-fill Title if empty?
            if (!itemForm.title) {
                itemForm.title = selectedPage.title;
            }
        }
    }
});

// Watch link type to clear fields if switching?
watch(() => itemForm.link_type, (newType) => {
    if (newType === 'external') {
        itemForm.page_id = null;
        itemForm.url = ''; // Reset for manual entry
    } else {
        itemForm.url = ''; // Will be filled by page selection
    }
});

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

// Local State for Items (to allow reordering without mutating props)
const localItems = ref([...props.menu.items]);

// Sync with props if they change (e.g. after add/edit/delete)
watch(() => props.menu.items, (newItems) => {
    localItems.value = [...newItems];
}, { deep: true });

// Reordering Logic
const moveUp = (index, list) => {
    if (index > 0) {
        const item = list[index];
        list.splice(index, 1);
        list.splice(index - 1, 0, item);
        saveOrder();
    }
};

const moveDown = (index, list) => {
    if (index < list.length - 1) {
        const item = list[index];
        list.splice(index, 1);
        list.splice(index + 1, 0, item);
        saveOrder();
    }
};

const saveOrder = () => {
    // Send the updated local structure
    axios.post(route('admin.menus.reorder', props.menu.id), {
        items: localItems.value
    }).then(() => {
        // Success
    }).catch(error => {
        console.error('Error saving order', error);
        alert('Error al guardar el orden.');
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
                            <div v-for="(item, index) in localItems" :key="item.id" class="border rounded-lg p-4 bg-gray-50">
                                    <div class="flex justify-between items-center">
                                        <div class="font-medium text-gray-900 flex items-center gap-2">
                                            {{ item.title }}
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded" v-if="item.page_id">
                                                Página
                                            </span>
                                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded" v-else>
                                                Enlace: {{ item.url }}
                                            </span>
                                            <span class="text-xs text-gray-400" v-if="item.target === '_blank'">
                                                (Nueva Pestaña)
                                            </span>
                                        </div>
                                        <div class="flex gap-2 text-sm items-center">
                                            <!-- Reordering Controls -->
                                            <div class="flex gap-1 mr-4 border-r pr-4">
                                                <button @click="moveUp(index, localItems)" class="p-1 hover:bg-gray-200 rounded text-gray-500 hover:text-gray-700" title="Subir">
                                                    ⬆️
                                                </button>
                                                <button @click="moveDown(index, localItems)" class="p-1 hover:bg-gray-200 rounded text-gray-500 hover:text-gray-700" title="Bajar">
                                                    ⬇️
                                                </button>
                                            </div>

                                            <button @click="openCreateItemModal(item.id)" class="text-green-600 hover:underline">+ Sub-ítem</button>
                                            <button @click="openEditItemModal(item)" class="text-blue-600 hover:underline">Editar</button>
                                            <button @click="confirmingItemDeletion = true; itemToDelete = item.id" class="text-red-600 hover:underline">Eliminar</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Children -->
                                    <div v-if="item.children && item.children.length > 0" class="mt-3 pl-6 border-l-2 border-gray-200 space-y-2">
                                        <div v-for="(child, childIndex) in item.children" :key="child.id" class="bg-white border rounded p-3 flex justify-between items-center">
                                            <div class="text-sm text-gray-700 font-medium">
                                                 {{ child.title }}
                                                 <span class="text-xs text-gray-500 font-normal ml-2">
                                                    - {{ child.page_id ? 'Página' : 'Enlace' }}
                                                </span>
                                            </div>
                                            <div class="flex gap-2 text-xs items-center">
                                                <!-- Reordering Controls for Children -->
                                                <div class="flex gap-1 mr-2 border-r pr-2">
                                                    <button @click="moveUp(childIndex, item.children)" class="p-1 hover:bg-gray-200 rounded text-gray-500 hover:text-gray-700" title="Subir">
                                                        ⬆️
                                                    </button>
                                                    <button @click="moveDown(childIndex, item.children)" class="p-1 hover:bg-gray-200 rounded text-gray-500 hover:text-gray-700" title="Bajar">
                                                        ⬇️
                                                    </button>
                                                </div>

                                                <button @click="openEditItemModal(child)" class="text-blue-600 hover:underline">Editar</button>
                                                <button @click="confirmingItemDeletion = true; itemToDelete = child.id" class="text-red-600 hover:underline">Eliminar</button>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div v-if="localItems.length === 0" class="text-center py-8 text-gray-500">
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
                    
                    <!-- Title -->
                    <div>
                        <InputLabel value="Título / Etiqueta" />
                        <TextInput v-model="itemForm.title" type="text" class="mt-1 block w-full" placeholder="Ej: Inicio" />
                        <InputError :message="itemForm.errors.title" class="mt-2" />
                    </div>

                    <!-- Link Type -->
                    <div>
                        <InputLabel value="Tipo de Enlace" />
                        <div class="flex gap-4 mt-2">
                            <label class="flex items-center">
                                <input type="radio" v-model="itemForm.link_type" value="external" class="text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded-full">
                                <span class="ml-2 text-sm text-gray-600">Enlace Externo / URL</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" v-model="itemForm.link_type" value="page" class="text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded-full">
                                <span class="ml-2 text-sm text-gray-600">Página del Sitio</span>
                            </label>
                        </div>
                    </div>

                    <!-- Page Selector -->
                    <div v-if="itemForm.link_type === 'page'">
                        <InputLabel value="Seleccionar Página" />
                        <select v-model="itemForm.page_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option :value="null" disabled>-- Seleccione una página --</option>
                            <option v-for="page in pages" :key="page.id" :value="page.id">
                                {{ page.title }} (/{{ page.slug }})
                            </option>
                        </select>
                        <InputError :message="itemForm.errors.page_id" class="mt-2" />
                    </div>

                    <!-- URL Input (Conditional) -->
                    <div v-if="itemForm.link_type === 'external'">
                        <InputLabel value="URL Completa" />
                        <TextInput v-model="itemForm.url" type="text" class="mt-1 block w-full" placeholder="https://ejemplo.com/enlace" />
                        <InputError :message="itemForm.errors.url" class="mt-2" />
                    </div>
                    
                    <!-- If page is selected, maybe show the auto-generated slug as readonly URL or hidden? -->
                    <div v-if="itemForm.link_type === 'page' && itemForm.url">
                        <InputLabel value="URL Generada (Slug)" />
                        <TextInput v-model="itemForm.url" type="text" class="mt-1 block w-full bg-gray-100 text-gray-500" readonly />
                         <div class="text-xs text-gray-400 mt-1">Este enlace se genera automáticamente desde la página seleccionada.</div>
                    </div>

                    <!-- Target -->
                     <div>
                        <InputLabel value="Abrir en" />
                        <select v-model="itemForm.target" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="_self">Misma Pestaña</option>
                            <option value="_blank">Nueva Pestaña (Enlace Externo)</option>
                        </select>
                         <InputError :message="itemForm.errors.target" class="mt-2" />
                    </div>

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
