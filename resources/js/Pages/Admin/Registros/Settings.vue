<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    startDate: String,
    endDate: String,
    maxParticipants: [String, Number],
});

const form = useForm({
    start_date: props.startDate || '',
    end_date: props.endDate || '',
    max_participants: props.maxParticipants || '',
});

const submit = () => {
    form.post(route('admin.registros.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {},
    });
};
</script>

<template>
    <Head title="Configuración del Formulario" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Configuración del Formulario
                </h2>
                <Link :href="route('admin.registros.index')" class="text-indigo-600 hover:text-indigo-900">
                    &larr; Volver a Registros
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                         <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                        </div>

                        <form @submit.prevent="submit" class="max-w-xl space-y-6">
                            
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Fecha de Apertura</label>
                                <input type="date" id="start_date" v-model="form.start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">El formulario estará visible al público a partir de esta fecha.</p>
                                <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</div>
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Fecha de Cierre</label>
                                <input type="date" id="end_date" v-model="form.end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">El formulario dejará de estar disponible después de esta fecha.</p>
                                <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</div>
                            </div>

                            <div>
                                <label for="max_participants" class="block text-sm font-medium text-gray-700">Número Máximo de Personas Registradas</label>
                                <input type="number" id="max_participants" v-model="form.max_participants" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">Deje en blanco para ilimitado.</p>
                                <div v-if="form.errors.max_participants" class="text-red-500 text-xs mt-1">{{ form.errors.max_participants }}</div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" :disabled="form.processing" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                    Guardar Configuración
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
