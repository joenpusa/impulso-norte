<script setup>
import { ref } from 'vue';

const props = defineProps({
    images: {
        type: Array,
        required: true,
        default: () => []
    }
});

const activeImage = ref(null);

const openLightbox = (index) => {
    activeImage.value = index;
};

const closeLightbox = () => {
    activeImage.value = null;
};

const nextImage = () => {
    if (activeImage.value < props.images.length - 1) {
        activeImage.value++;
    } else {
        activeImage.value = 0;
    }
};

const prevImage = () => {
    if (activeImage.value > 0) {
        activeImage.value--;
    } else {
        activeImage.value = props.images.length - 1;
    }
};
</script>

<template>
    <div class="gallery-container w-full my-8">
        
        <!-- Masonry Grid -->
        <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4 group">
            <div 
                v-for="(img, index) in images" 
                :key="index" 
                class="relative break-inside-avoid cursor-pointer overflow-hidden rounded-xl shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:z-10 bg-gray-100"
                @click="openLightbox(index)"
            >
                <img 
                    :src="img" 
                    class="w-full h-auto object-cover transition-transform duration-700 hover:scale-110" 
                    loading="lazy" 
                    alt="Gallery image"
                />
                
                <!-- Overlay effect on hover -->
                <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform scale-50 group-hover:scale-100" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                </div>
            </div>
        </div>

        <!-- Lightbox -->
        <transition name="fade">
            <div v-if="activeImage !== null" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-90 backdrop-blur-sm" @click.self="closeLightbox">
                
                <!-- Close button -->
                <button @click="closeLightbox" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-[101]">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <!-- Navigation Controls -->
                <button v-if="images.length > 1" @click.stop="prevImage" class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition-colors z-[101] bg-black bg-opacity-50 p-2 rounded-full hover:bg-opacity-70">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                
                <button v-if="images.length > 1" @click.stop="nextImage" class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition-colors z-[101] bg-black bg-opacity-50 p-2 rounded-full hover:bg-opacity-70">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>

                <!-- Current Image -->
                <div class="relative max-w-7xl max-h-[90vh] mx-4 flex flex-col items-center justify-center">
                    <img 
                        :src="images[activeImage]" 
                        class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl" 
                        alt="Enlarged gallery image"
                    />
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-1.5 bg-black bg-opacity-60 text-white text-sm rounded-full backdrop-blur-md">
                        {{ activeImage + 1 }} / {{ images.length }}
                    </div>
                </div>
            </div>
        </transition>

    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
