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

const getClasses = (idx) => {
    if (props.images.length === 1) {
        return 'left-1/2 -translate-x-1/2 w-full max-w-md scale-100 z-20 opacity-100';
    }
    
    if (idx === activeIndex.value) {
        return 'left-1/2 -translate-x-1/2 w-[80%] md:w-[40%] scale-100 z-20 opacity-100';
    }
    
    let prevIndex = (activeIndex.value - 1 + props.images.length) % props.images.length;
    let nextIndex = (activeIndex.value + 1) % props.images.length;
    
    if (idx === prevIndex) {
        return 'left-[-10%] md:left-0 w-[60%] md:w-[35%] scale-90 z-10 opacity-70 cursor-pointer';
    }
    
    if (idx === nextIndex) {
        return 'right-[-10%] md:right-0 w-[60%] md:w-[35%] scale-90 z-10 opacity-70 cursor-pointer';
    }
    
    return 'opacity-0 scale-75 z-0 hidden pointer-events-none';
};
</script>

<template>
    <div v-if="images && images.length > 0" class="relative w-full group overflow-hidden py-10" data-carousel="slide">
        <!-- Carousel wrapper -->
        <div class="relative w-full h-[500px] md:h-[600px] max-w-7xl mx-auto flex items-center">
            <div v-for="(img, idx) in images" :key="idx" 
                 class="absolute transition-all duration-700 ease-in-out h-[90%] md:h-full rounded-2xl shadow-xl overflow-hidden"
                 :class="getClasses(idx)"
                 @click="idx !== activeIndex ? goTo(idx) : null">
                <img :src="img" class="w-full h-full object-cover" :alt="'Slide ' + (idx + 1)">
            </div>
        </div>
        
        <!-- Slider indicators -->
        <div v-if="images.length > 1" class="absolute z-30 flex space-x-3 -translate-x-1/2 bottom-2 left-1/2">
            <button v-for="(img, idx) in images" :key="idx" 
                    type="button" 
                    class="w-3 h-3 rounded-full transition-all duration-300 border border-gray-400"
                    :class="activeIndex === idx ? 'bg-indigo-600 border-indigo-600 scale-110' : 'bg-gray-300/50 hover:bg-gray-400'"
                    @click="goTo(idx)"
                    :aria-label="'Slide ' + (idx + 1)"></button>
        </div>
        
        <!-- Slider controls -->
        <button v-if="images.length > 1" @click="prev" type="button" class="absolute top-1/2 -translate-y-1/2 left-2 md:left-8 z-30 flex items-center justify-center h-12 w-12 cursor-pointer group focus:outline-none">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/50 group-hover:bg-white/80 group-focus:ring-4 group-focus:ring-white/70 group-focus:outline-none shadow">
                <svg class="w-4 h-4 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
                </svg>
                <span class="sr-only">Previous</span>
            </span>
        </button>
        <button v-if="images.length > 1" @click="next" type="button" class="absolute top-1/2 -translate-y-1/2 right-2 md:right-8 z-30 flex items-center justify-center h-12 w-12 cursor-pointer group focus:outline-none">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/50 group-hover:bg-white/80 group-focus:ring-4 group-focus:ring-white/70 group-focus:outline-none shadow">
                <svg class="w-4 h-4 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                </svg>
                <span class="sr-only">Next</span>
            </span>
        </button>
    </div>
    <div v-else class="text-center p-4 bg-gray-100 rounded text-gray-400 text-sm">
        (Carrusel vertical vacío)
    </div>
</template>
