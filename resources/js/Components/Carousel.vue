<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    images: {
        type: Array,
        required: true,
        default: () => []
    }
});

const activeIndex = ref(0);
const intervalId = ref(null);

const next = () => {
    if (props.images.length === 0) return;
    activeIndex.value = (activeIndex.value + 1) % props.images.length;
};

const prev = () => {
    if (props.images.length === 0) return;
    activeIndex.value = (activeIndex.value - 1 + props.images.length) % props.images.length;
};

const goTo = (index) => {
    activeIndex.value = index;
};

const startAutoPlay = () => {
     stopAutoPlay();
     if (props.images.length > 1) {
         intervalId.value = setInterval(next, 5000);
     }
};

const stopAutoPlay = () => {
    if (intervalId.value) clearInterval(intervalId.value);
};

onMounted(() => {
    startAutoPlay();
});

onUnmounted(() => {
    stopAutoPlay();
});
</script>

<template>
    <div v-if="images && images.length > 0" class="relative w-full group" data-carousel="slide">
        <!-- Carousel wrapper -->
        <div class="relative overflow-hidden rounded-lg shadow-lg aspect-video md:aspect-[21/9] bg-gray-200">
            <div v-for="(img, idx) in images" :key="idx" 
                 class="absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out"
                 :class="{'opacity-100 z-10': activeIndex === idx, 'opacity-0 z-0': activeIndex !== idx}">
                <img :src="img" class="absolute block w-full h-full object-cover" :alt="'Slide ' + (idx + 1)">
            </div>
            
            <!-- Link to edit if empty or error? No, just render. -->
        </div>
        
        <!-- Slider indicators -->
        <div v-if="images.length > 1" class="absolute z-30 flex space-x-3 -translate-x-1/2 bottom-5 left-1/2">
            <button v-for="(img, idx) in images" :key="idx" 
                    type="button" 
                    class="w-3 h-3 rounded-full transition-all duration-300 border border-white/50"
                    :class="activeIndex === idx ? 'bg-white scale-110' : 'bg-white/30 hover:bg-white/60'"
                    @click="goTo(idx)"
                    :aria-label="'Slide ' + (idx + 1)"></button>
        </div>
        
        <!-- Slider controls -->
        <button v-if="images.length > 1" @click="prev" type="button" class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none backdrop-blur-sm">
                <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                </svg>
                <span class="sr-only">Previous</span>
            </span>
        </button>
        <button v-if="images.length > 1" @click="next" type="button" class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none backdrop-blur-sm">
                <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                </svg>
                <span class="sr-only">Next</span>
            </span>
        </button>
    </div>
    <div v-else class="text-center p-4 bg-gray-100 rounded text-gray-400 text-sm">
        (Carrusel vacío)
    </div>
</template>
