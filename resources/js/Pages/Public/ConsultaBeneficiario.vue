<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    settings: Object,
    mainMenu: Object,
});

const form = useForm({
    numero_documento: '',
});

const submit = () => {
    form.post(route('consulta.check'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Consulta de Beneficiarios" />
    <PublicLayout :settings="settings" :mainMenu="mainMenu">
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Consulta de Beneficiarios - Impulso Productivo Sur Occidental</h2>

                    <div v-if="$page.props.flash.success" class="flex items-center bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.59l4.3-4.3 1.4 1.42L9 14.41l-3.7-3.7 1.4-1.42z"/></svg></div>
                        <div>
                            <p class="font-bold">¡Felicidades!</p>
                            <p class="text-sm">{{ $page.props.flash.success }}</p>
                        </div>
                    </div>

                    <div v-if="$page.props.flash.error" class="flex items-center bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
                        <div>
                            <p class="font-bold">Atención</p>
                            <p class="text-sm">{{ $page.props.flash.error }}</p>
                        </div>
                    </div>

                    <p class="text-gray-600 mb-6 text-center">
                        Ingresa tu número de documento para verificar si has sido seleccionado como beneficiario del programa.
                    </p>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="numero_documento" class="block text-sm font-medium text-gray-700">Número de Documento</label>
                            <input type="text" id="numero_documento" v-model="form.numero_documento" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required placeholder="Ingresa tu número de documento (sin puntos ni espacios)">
                            <div v-if="form.errors.numero_documento" class="text-red-500 text-xs mt-1">{{ form.errors.numero_documento }}</div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" :disabled="form.processing" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                                Consultar Estado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
