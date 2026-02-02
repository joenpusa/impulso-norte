<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import ToastNotification from '@/Components/UI/ToastNotification.vue';

const props = defineProps({
    settings: Object,
});

const activeTab = ref('general');

const form = useForm({
    site_title: props.settings.site_title || '',
    header_logo: null,
    contact_email: props.settings.contact_email || '',
    phone: props.settings.phone || '',
    footer_text: props.settings.footer_text || '',
    facebook_url: props.settings.facebook_url || '',
    twitter_url: props.settings.twitter_url || '',
    instagram_url: props.settings.instagram_url || '',
    primary_color: props.settings.primary_color || '#3b82f6',
    header_bg_color: props.settings.header_bg_color || '#ffffff',
    footer_bg_color: props.settings.footer_bg_color || '#1f2937',
});

// Helper validation (simplified)
const logoPreview = ref(props.settings.header_logo ? '/storage/' + props.settings.header_logo : null);

const handleLogoChange = (e) => {
    const file = e.target.files[0];
    form.header_logo = file;
    if (file) {
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('admin.settings.store'), {
        preserveScroll: true,
        onSuccess: () => {
             // Reset file input if needed, but keeping values is fine for settings
        }
    });
};
</script>

<template>
    <Head title="Ajustes y Estructura" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajustes Generales</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!-- Tabs -->
                        <div class="border-b border-gray-200 mb-6">
                            <nav class="-mb-px flex space-x-8">
                                <button @click="activeTab = 'general'" :class="{'border-blue-500 text-blue-600': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'general'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    General y Logo
                                </button>
                                <button @click="activeTab = 'social'" :class="{'border-blue-500 text-blue-600': activeTab === 'social', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'social'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Redes Sociales y Footer
                                </button>
                                <button @click="activeTab = 'styles'" :class="{'border-blue-500 text-blue-600': activeTab === 'styles', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'styles'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Estilos y Colores
                                </button>
                            </nav>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- General Tab -->
                            <div v-show="activeTab === 'general'" class="space-y-6">
                                <div>
                                    <InputLabel value="Título del Sitio" />
                                    <TextInput v-model="form.site_title" type="text" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <InputLabel value="Logo del Header" />
                                    <div class="mt-2 flex items-center gap-4">
                                        <div v-if="logoPreview" class="h-16 w-16 border rounded overflow-hidden bg-gray-100 flex items-center justify-center">
                                            <img :src="logoPreview" class="max-h-full max-w-full" />
                                        </div>
                                        <input type="file" @change="handleLogoChange" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                    </div>
                                </div>
                                <div>
                                    <InputLabel value="Email de Contacto" />
                                    <TextInput v-model="form.contact_email" type="email" class="mt-1 block w-full" />
                                </div>
                                <div>
                                    <InputLabel value="Teléfono" />
                                    <TextInput v-model="form.phone" type="text" class="mt-1 block w-full" />
                                </div>
                            </div>

                            <!-- Social Tab -->
                            <div v-show="activeTab === 'social'" class="space-y-6">
                                <div>
                                    <InputLabel value="Texto del Footer" />
                                    <textarea v-model="form.footer_text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                                </div>
                                <div>
                                    <InputLabel value="Facebook URL" />
                                    <TextInput v-model="form.facebook_url" type="url" class="mt-1 block w-full" placeholder="https://facebook.com/..." />
                                </div>
                                <div>
                                    <InputLabel value="Twitter / X URL" />
                                    <TextInput v-model="form.twitter_url" type="url" class="mt-1 block w-full" placeholder="https://twitter.com/..." />
                                </div>
                                <div>
                                    <InputLabel value="Instagram URL" />
                                    <TextInput v-model="form.instagram_url" type="url" class="mt-1 block w-full" placeholder="https://instagram.com/..." />
                                </div>
                            </div>

                            <!-- Styles Tab -->
                            <div v-show="activeTab === 'styles'" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <InputLabel value="Color Primario" />
                                        <div class="mt-1 flex items-center gap-2">
                                            <input type="color" v-model="form.primary_color" class="h-10 w-20 border rounded cursor-pointer" />
                                            <span class="text-sm text-gray-600">{{ form.primary_color }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <InputLabel value="Fondo del Header" />
                                        <div class="mt-1 flex items-center gap-2">
                                            <input type="color" v-model="form.header_bg_color" class="h-10 w-20 border rounded cursor-pointer" />
                                            <span class="text-sm text-gray-600">{{ form.header_bg_color }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <InputLabel value="Fondo del Footer" />
                                        <div class="mt-1 flex items-center gap-2">
                                            <input type="color" v-model="form.footer_bg_color" class="h-10 w-20 border rounded cursor-pointer" />
                                            <span class="text-sm text-gray-600">{{ form.footer_bg_color }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Guardar Cambios
                                </PrimaryButton>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <ToastNotification />
    </AuthenticatedLayout>
</template>
