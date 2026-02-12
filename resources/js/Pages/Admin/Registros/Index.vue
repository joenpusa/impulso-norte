<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    registros: Object,
});

const form = useForm({});

const deleteRegistro = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
        form.delete(route('admin.registros.destroy', id));
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('es-CO');
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
                <Link :href="route('admin.registros.settings')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Configuración
                </Link>
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
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivos</th>
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 flex space-x-2">
                                            <a v-if="registro.documento_identidad_path" :href="getFileUrl(registro.documento_identidad_path)" target="_blank" class="text-indigo-600 hover:text-indigo-900">Doc</a>
                                            <a v-if="registro.sisben_path" :href="getFileUrl(registro.sisben_path)" target="_blank" class="text-indigo-600 hover:text-indigo-900">Sisben</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="deleteRegistro(registro.id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                     <tr v-if="registros.data.length === 0">
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
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
    </AuthenticatedLayout>
</template>
