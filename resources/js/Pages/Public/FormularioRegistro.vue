<script setup>
import { useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    isClosed: Boolean,
    message: String,
    settings: Object,
    mainMenu: Object,
});

const form = useForm({
    municipio: '',
    nombre_completo: '',
    fecha_nacimiento: '',
    tipo_documento: '',
    numero_documento: '',
    documento_identidad_path: null,
    sexo: '',
    nacionalidad: '',
    zona_residencia: '',
    direccion: '',
    telefono: '',
    correo: '',
    clasificacion_sisben: '',
    sisben_path: null,
    tiene_iniciativa: false,
    nombre_iniciativa: '',
});

const municipios = [
    'Abrego', 'Arboledas', 'Bochalema', 'Bucarasica', 'Cachirá', 'Cácota', 'Chinácota', 
    'Chitagá', 'Convención', 'Cúcuta', 'Cucutilla', 'Durania', 'El Carmen', 'El Tarra', 
    'El Zulia', 'Gramalote', 'Hacarí', 'Herrán', 'La Esperanza', 'La Playa', 'Labateca', 
    'Los Patios', 'Lourdes', 'Mutiscua', 'Ocaña', 'Pamplona', 'Pamplonita', 'Puerto Santander', 
    'Ragonvalia', 'Salazar', 'San Calixto', 'San Cayetano', 'Santiago', 'Sardinata', 'Silos', 
    'Teorama', 'Tibú', 'Toledo', 'Villa Caro', 'Villa del Rosario'
];

const submit = () => {
    form.post(route('form.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <PublicLayout :settings="settings" :mainMenu="mainMenu">
        <div class="py-12 bg-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Formulario de Registro - Impulso Norte</h2>

                    <div v-if="isClosed" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Formulario Cerrado</p>
                        <p>{{ message }}</p>
                    </div>

                    <div v-else>
                        <div v-if="$page.props.flash.success" class="flex items-center bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <div class="py-1"><svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.59l4.3-4.3 1.4 1.42L9 14.41l-3.7-3.7 1.4-1.42z"/></svg></div>
                            <div>
                                <p class="font-bold">¡Registro Exitoso!</p>
                                <p class="text-sm">Gracias por diligenciar el formulario.</p>
                            </div>
                        </div>

                         <div v-if="$page.props.flash.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            <p class="font-bold">Error</p>
                            <p>{{ $page.props.flash.error }}</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <!-- 1. Municipio -->
                            <div>
                                <label for="municipio" class="block text-sm font-medium text-gray-700">1. Municipio</label>
                                <select id="municipio" v-model="form.municipio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="" disabled>Seleccione un municipio</option>
                                    <option v-for="muni in municipios" :key="muni" :value="muni">{{ muni }}</option>
                                </select>
                                <div v-if="form.errors.municipio" class="text-red-500 text-xs mt-1">{{ form.errors.municipio }}</div>
                            </div>

                            <!-- 2. Nombre Completo -->
                            <div>
                                <label for="nombre_completo" class="block text-sm font-medium text-gray-700">2. Nombre y Apellidos Completos</label>
                                <input type="text" id="nombre_completo" v-model="form.nombre_completo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <div v-if="form.errors.nombre_completo" class="text-red-500 text-xs mt-1">{{ form.errors.nombre_completo }}</div>
                            </div>

                            <!-- 3. Fecha Nacimiento -->
                            <div>
                                <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">3. Fecha de Nacimiento</label>
                                <input type="date" id="fecha_nacimiento" v-model="form.fecha_nacimiento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <div v-if="form.errors.fecha_nacimiento" class="text-red-500 text-xs mt-1">{{ form.errors.fecha_nacimiento }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- 4. Tipo Documento -->
                                <div>
                                    <label for="tipo_documento" class="block text-sm font-medium text-gray-700">4. Tipo de Documento</label>
                                    <select id="tipo_documento" v-model="form.tipo_documento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="" disabled>Seleccione</option>
                                        <option value="CC">Cédula de Ciudadanía</option>
                                        <option value="TI">Tarjeta de Identidad</option>
                                        <option value="CE">Cédula de Extranjería</option>
                                        <option value="PEP">PEP</option>
                                        <option value="PPT">PPT</option>
                                    </select>
                                    <div v-if="form.errors.tipo_documento" class="text-red-500 text-xs mt-1">{{ form.errors.tipo_documento }}</div>
                                </div>

                                <!-- 5. Numero Documento -->
                                <div>
                                    <label for="numero_documento" class="block text-sm font-medium text-gray-700">5. Número de Documento</label>
                                    <input type="number" id="numero_documento" v-model="form.numero_documento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <div v-if="form.errors.numero_documento" class="text-red-500 text-xs mt-1">{{ form.errors.numero_documento }}</div>
                                </div>
                            </div>

                            <!-- 6. Adjuntar Documento -->
                            <div>
                                <label for="documento_identidad_path" class="block text-sm font-medium text-gray-700">6. Adjuntar Documento de Identidad (PDF)</label>
                                <input type="file" id="documento_identidad_path" @input="form.documento_identidad_path = $event.target.files[0]" accept=".pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="mt-2 w-full h-2 rounded overflow-hidden bg-gray-200">
                                    {{ form.progress.percentage }}%
                                </progress>
                                <div v-if="form.errors.documento_identidad_path" class="text-red-500 text-xs mt-1">{{ form.errors.documento_identidad_path }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- 7. Sexo -->
                                <div>
                                    <label for="sexo" class="block text-sm font-medium text-gray-700">7. Sexo</label>
                                    <select id="sexo" v-model="form.sexo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="" disabled>Seleccione</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                    <div v-if="form.errors.sexo" class="text-red-500 text-xs mt-1">{{ form.errors.sexo }}</div>
                                </div>

                                <!-- 8. Nacionalidad -->
                                <div>
                                    <label for="nacionalidad" class="block text-sm font-medium text-gray-700">8. Nacionalidad</label>
                                    <select id="nacionalidad" v-model="form.nacionalidad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="" disabled>Seleccione</option>
                                        <option value="Colombiana">Colombiana</option>
                                        <option value="Venezolana">Venezolana</option>
                                        <option value="Otra">Otra</option>
                                    </select>
                                    <div v-if="form.errors.nacionalidad" class="text-red-500 text-xs mt-1">{{ form.errors.nacionalidad }}</div>
                                </div>
                            </div>

                            <!-- 9. Zona Residencia -->
                            <div>
                                <label for="zona_residencia" class="block text-sm font-medium text-gray-700">9. Zona de Residencia</label>
                                <select id="zona_residencia" v-model="form.zona_residencia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="" disabled>Seleccione</option>
                                    <option value="Urbana">Urbana</option>
                                    <option value="Rural">Rural</option>
                                </select>
                                <div v-if="form.errors.zona_residencia" class="text-red-500 text-xs mt-1">{{ form.errors.zona_residencia }}</div>
                            </div>

                            <!-- 10. Direccion -->
                            <div>
                                <label for="direccion" class="block text-sm font-medium text-gray-700">10. Dirección del bien inmueble</label>
                                <input type="text" id="direccion" v-model="form.direccion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <div v-if="form.errors.direccion" class="text-red-500 text-xs mt-1">{{ form.errors.direccion }}</div>
                            </div>

                            <!-- 11. Telefono -->
                            <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700">11. Teléfono de contacto</label>
                                <input type="number" id="telefono" v-model="form.telefono" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <div v-if="form.errors.telefono" class="text-red-500 text-xs mt-1">{{ form.errors.telefono }}</div>
                            </div>

                            <!-- 12. Correo -->
                            <div>
                                <label for="correo" class="block text-sm font-medium text-gray-700">12. Correo Electrónico</label>
                                <input type="email" id="correo" v-model="form.correo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <div v-if="form.errors.correo" class="text-red-500 text-xs mt-1">{{ form.errors.correo }}</div>
                            </div>

                            <!-- 13. Clasificacion SISBEN -->
                            <div>
                                <label for="clasificacion_sisben" class="block text-sm font-medium text-gray-700">13. Clasificación SISBEN</label>
                                <select id="clasificacion_sisben" v-model="form.clasificacion_sisben" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="" disabled>Seleccione</option>
                                    <option value="A1-A5">Grupo A (A1-A5)</option>
                                    <option value="B1-B7">Grupo B (B1-B7)</option>
                                    <option value="C1-C18">Grupo C (C1-C18)</option>
                                    <option value="D1-D21">Grupo D (D1-D21)</option>
                                    <option value="No tiene">No tiene</option>
                                </select>
                                <div v-if="form.errors.clasificacion_sisben" class="text-red-500 text-xs mt-1">{{ form.errors.clasificacion_sisben }}</div>
                            </div>

                            <!-- 14. Adjuntar SISBEN -->
                            <div>
                                <label for="sisben_path" class="block text-sm font-medium text-gray-700">14. Adjuntar SISBEN (PDF)</label>
                                <input type="file" id="sisben_path" @input="form.sisben_path = $event.target.files[0]" accept=".pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <div v-if="form.errors.sisben_path" class="text-red-500 text-xs mt-1">{{ form.errors.sisben_path }}</div>
                            </div>

                            <!-- 15. Iniciativa Productiva -->
                            <div class="flex items-center">
                                <input type="checkbox" id="tiene_iniciativa" v-model="form.tiene_iniciativa" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="tiene_iniciativa" class="ml-2 block text-sm text-gray-900">
                                    15. ¿Tiene iniciativa productiva?
                                </label>
                                <div v-if="form.errors.tiene_iniciativa" class="text-red-500 text-xs mt-1 ml-2">{{ form.errors.tiene_iniciativa }}</div>
                            </div>

                            <!-- 16. Nombre Iniciativa -->
                             <transition enter-active-class="transition ease-out duration-200" leave-active-class="transition ease-in duration-150" enter-from-class="opacity-0 translate-y-1" leave-to-class="opacity-0 translate-y-1">
                                <div v-if="form.tiene_iniciativa">
                                    <label for="nombre_iniciativa" class="block text-sm font-medium text-gray-700">16. Nombre de la iniciativa</label>
                                    <input type="text" id="nombre_iniciativa" v-model="form.nombre_iniciativa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <div v-if="form.errors.nombre_iniciativa" class="text-red-500 text-xs mt-1">{{ form.errors.nombre_iniciativa }}</div>
                                </div>
                            </transition>

                            <div class="pt-6">
                                <button type="submit" :disabled="form.processing" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                    <span v-if="form.processing">Enviando...</span>
                                    <span v-else>Enviar Registro</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
