<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import axios from 'axios';

const props = defineProps({
    settings: Object,
    mainMenu: Object,
});

const numeroDocumento = ref('');
const isSearching = ref(false);
const errorValidacion = ref('');
const resultado = ref(null);

const limpiarBusqueda = () => {
    resultado.value = null;
    errorValidacion.value = '';
};

const consultar = async () => {
    const docLimpio = numeroDocumento.value.replace(/[^0-9a-zA-Z]/g, '');
    if (!docLimpio) {
        errorValidacion.value = 'Por favor ingresa un número de cédula válido.';
        resultado.value = null;
        return;
    }

    errorValidacion.value = '';
    isSearching.value = true;
    resultado.value = null;

    try {
        const response = await axios.post('/consultabeneficiariosinsumos', {
            numero_documento: docLimpio,
        }, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        resultado.value = response.data;
    } catch (err) {
        if (err.response && err.response.data) {
            resultado.value = {
                encontrado: false,
                mensaje: err.response.data.mensaje || 'No se encontró el número de documento.'
            };
        } else {
            resultado.value = {
                encontrado: false,
                mensaje: 'Ocurrió un error al procesar la consulta. Por favor intenta de nuevo.'
            };
        }
    } finally {
        isSearching.value = false;
    }
};
</script>

<template>
    <Head title="Consulta de Beneficiarios - Etapa de Equipamiento Productivo" />

    <PublicLayout :settings="settings" :mainMenu="mainMenu">
        <div class="min-h-[80vh] py-10 md:py-16 bg-gradient-to-b from-blue-50/40 via-white to-slate-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-5xl bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 min-h-[550px]">
                    
                    <!-- Columna Izquierda: Imagen Oficial del Diseñador -->
                    <div class="lg:col-span-6 bg-slate-50/70 flex items-center justify-center p-4 sm:p-8 lg:p-10 border-b lg:border-b-0 lg:border-r border-slate-200">
                        <div class="w-full max-w-md mx-auto rounded-2xl overflow-hidden shadow-lg border border-slate-200/90 bg-white transform transition-transform duration-200 hover:scale-[1.01]">
                            <img 
                                src="/images/consulta-beneficiarios-banner.jpg" 
                                alt="Consulta aquí si has sido seleccionado como beneficiario de la etapa de equipamiento productivo" 
                                class="w-full h-auto object-cover block"
                            />
                        </div>
                    </div>

                    <!-- Columna Derecha: Formulario de Consulta y Resultados -->
                    <div class="lg:col-span-6 p-6 sm:p-12 flex flex-col justify-center bg-white">
                        <div class="max-w-md w-full mx-auto space-y-8">
                            
                            <!-- Encabezado del Formulario -->
                            <div>
                                <label for="cedula" class="block text-slate-800 text-base sm:text-lg font-bold tracking-wider uppercase mb-3">
                                    NÚMERO DE CEDULA
                                </label>
                                
                                <div class="relative">
                                    <input
                                        id="cedula"
                                        type="text"
                                        inputmode="numeric"
                                        v-model="numeroDocumento"
                                        @input="limpiarBusqueda"
                                        @keyup.enter="consultar"
                                        placeholder="Ingresa tu número de documento"
                                        class="w-full px-4 py-3.5 text-lg sm:text-xl text-gray-800 bg-slate-50/60 border-2 border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 transition-all placeholder:text-gray-400 shadow-sm"
                                        autocomplete="off"
                                    />
                                    <button 
                                        v-if="numeroDocumento" 
                                        @click="numeroDocumento = ''; limpiarBusqueda();" 
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1 cursor-pointer"
                                        type="button"
                                        title="Limpiar"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <p v-if="errorValidacion" class="mt-2 text-sm text-red-600 font-medium">
                                    {{ errorValidacion }}
                                </p>
                            </div>

                            <!-- Botón Buscar -->
                            <div class="flex justify-center">
                                <button
                                    type="button"
                                    @click="consultar"
                                    :disabled="isSearching"
                                    class="inline-flex items-center justify-center px-12 py-3.5 rounded-full text-white text-lg font-bold bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-700 hover:from-blue-800 hover:via-indigo-700 hover:to-purple-800 active:scale-95 shadow-lg shadow-indigo-500/25 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                                >
                                    <svg v-if="isSearching" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ isSearching ? 'Buscando...' : 'Buscar' }}</span>
                                </button>
                            </div>

                            <!-- Resultado: NO ENCONTRADO -->
                            <div 
                                v-if="resultado && !resultado.encontrado" 
                                class="pt-4 transition-all duration-300 ease-out"
                            >
                                <p class="text-rose-600 font-bold text-xl sm:text-2xl text-center leading-snug">
                                    No se encontró el número de documento.
                                </p>
                                <p class="text-slate-500 text-sm text-center mt-2">
                                    Verifica que hayas digitado correctamente tu documento de identidad (sin puntos ni espacios).
                                </p>
                            </div>

                            <!-- Resultado: ENCONTRADO / BENEFICIARIO -->
                            <div 
                                v-if="resultado && resultado.encontrado" 
                                class="pt-2 transition-all duration-300 ease-out"
                            >
                                <div class="bg-gradient-to-br from-emerald-50 via-teal-50 to-green-50 border-2 border-emerald-500/80 rounded-2xl p-6 shadow-md text-center space-y-3">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-600 text-white shadow-md mb-1">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>

                                    <h4 class="text-xl sm:text-2xl font-black text-emerald-900 tracking-tight">
                                        ¡FELICITACIONES!
                                    </h4>

                                    <p class="text-lg sm:text-xl font-bold text-slate-800 bg-white/90 py-2.5 px-4 rounded-xl border border-emerald-200 shadow-sm">
                                        {{ resultado.nombre }}
                                    </p>

                                    <p class="text-emerald-950 font-medium text-sm sm:text-base leading-relaxed pt-1">
                                        {{ resultado.mensaje }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </PublicLayout>
</template>
