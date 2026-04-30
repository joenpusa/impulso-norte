<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    registros: Object,
});

const form = useForm({});

const viewingRegistro = ref(null);
const isModalOpen = ref(false);

const viewRegistro = (registro) => {
    viewingRegistro.value = registro;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => { viewingRegistro.value = null; }, 300);
};

const deleteRegistro = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
        form.delete(route('admin.registros.destroy', id));
    }
};

const toggleBeneficiario = (registro) => {
    const action = registro.es_beneficiario ? 'desmarcar' : 'marcar';
    if (confirm(`¿Estás seguro de que deseas ${action} a este registro como beneficiario?`)) {
        form.post(route('admin.registros.toggleBeneficiario', registro.id), {
            preserveScroll: true
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('es-CO');
};

const formatDateYYYYMMDD = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toISOString().split('T')[0];
};

const getFileUrl = (path) => {
    if (!path) return '#';
    return `/storage/${path}`;
};
</script>

<template>
    <Head title="Registros del Formulario" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Registros del Formulario
                </h2>
                <div class="flex space-x-2">
                    <a :href="route('admin.registros.export')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Exportar a Excel
                    </a>
                    <Link :href="route('admin.registros.settings')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Configuración
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 border-b border-gray-200">
                         <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Municipio</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beneficiario</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="registro in registros.data" :key="registro.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ registro.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ registro.nombre_completo }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ registro.tipo_documento }} {{ registro.numero_documento }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ registro.municipio }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(registro.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a v-if="registro.documento_identidad_path" :href="getFileUrl(registro.documento_identidad_path)" target="_blank" class="text-indigo-600 hover:text-indigo-900">Ver Anexo</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span v-if="registro.es_beneficiario" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Sí</span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="toggleBeneficiario(registro)" class="text-yellow-600 hover:text-yellow-900 mr-3" title="Marcar/Desmarcar Beneficiario">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                                </svg>
                                            </button>
                                            <button @click="viewRegistro(registro)" class="text-blue-600 hover:text-blue-900 mr-3">Ver</button>
                                            <button @click="deleteRegistro(registro.id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                     <tr v-if="registros.data.length === 0">
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No hay registros encontrados.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-4 flex justify-between items-center" v-if="registros.links && registros.data.length > 0">
                             <div class="flex space-x-1">
                                <template v-for="(link, k) in registros.links" :key="k">
                                    <div v-if="link.url === null"  class="px-4 py-2 text-sm text-gray-500 border rounded opacity-50 cursor-not-allowed" v-html="link.label" />
                                    <Link v-else :href="link.url" class="px-4 py-2 text-sm border rounded hover:bg-gray-100" :class="{ 'bg-blue-500 text-white': link.active }" v-html="link.label" />
                                </template>
                            </div>
                            <div class="text-sm text-gray-500">
                                Mostrando {{ registros.from }} a {{ registros.to }} de {{ registros.total }} resultados
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detalles -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Detalles del Registro
                </h2>
                <div v-if="viewingRegistro" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mt-4">
                    <div><span class="font-semibold text-gray-600">ID:</span> {{ viewingRegistro.id }}</div>
                    <div><span class="font-semibold text-gray-600">Fecha de Registro:</span> {{ formatDate(viewingRegistro.created_at) }}</div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="font-semibold text-gray-600">Nombre Completo:</span> {{ viewingRegistro.nombre_completo }}</div>
                    
                    <div><span class="font-semibold text-gray-600">Tipo Documento:</span> {{ viewingRegistro.tipo_documento }}</div>
                    <div><span class="font-semibold text-gray-600">Número:</span> {{ viewingRegistro.numero_documento }}</div>
                    
                    <div><span class="font-semibold text-gray-600">Municipio:</span> {{ viewingRegistro.municipio }}</div>
                    <div><span class="font-semibold text-gray-600">Fecha Nacimiento:</span> {{ formatDateYYYYMMDD(viewingRegistro.fecha_nacimiento) }}</div>
                    
                    <div><span class="font-semibold text-gray-600">Sexo:</span> {{ viewingRegistro.sexo }}</div>
                    <div><span class="font-semibold text-gray-600">Nacionalidad:</span> {{ viewingRegistro.nacionalidad }}</div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="font-semibold text-gray-600">Zona Residencia:</span> {{ viewingRegistro.zona_residencia }}</div>
                    <div class="col-span-1 md:col-span-2"><span class="font-semibold text-gray-600">Dirección:</span> {{ viewingRegistro.direccion }}</div>
                    
                    <div><span class="font-semibold text-gray-600">Teléfono:</span> {{ viewingRegistro.telefono }}</div>
                    <div><span class="font-semibold text-gray-600">Correo:</span> {{ viewingRegistro.correo }}</div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="font-semibold text-gray-600">Clasificación Sisbén:</span> {{ viewingRegistro.clasificacion_sisben }}</div>
                    
                    <div><span class="font-semibold text-gray-600">Tiene Iniciativa:</span> {{ viewingRegistro.tiene_iniciativa ? 'Sí' : 'No' }}</div>
                    <div><span class="font-semibold text-gray-600">Nombre Iniciativa:</span> {{ viewingRegistro.nombre_iniciativa || 'No aplica' }}</div>

                    <div class="col-span-1 md:col-span-2 mt-2">
                        <span class="font-semibold text-gray-600 mr-2">Es Beneficiario:</span>
                        <span v-if="viewingRegistro.es_beneficiario" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Sí</span>
                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No</span>
                    </div>

                    
                    <div class="col-span-1 md:col-span-2 mt-2" v-if="viewingRegistro.documento_identidad_path">
                        <span class="font-semibold text-gray-600">Anexo Identidad:</span>
                        <br>
                        <a :href="getFileUrl(viewingRegistro.documento_identidad_path)" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline">
                            Descargar / Ver Documento
                        </a>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">Cerrar</SecondaryButton>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>
