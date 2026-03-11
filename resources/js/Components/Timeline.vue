<template>
    <div class="w-full mx-auto py-8">
        <div class="flex flex-wrap justify-center items-stretch gap-y-16 lg:gap-y-24">
            <template v-for="(step, index) in steps" :key="index">
                <!-- Wrapper for step + optional arrow -->
                <div class="flex items-start justify-center w-full lg:w-1/3 px-4 relative">
                    
                    <!-- Step Content -->
                    <div class="flex flex-col items-center max-w-xs relative z-10 w-full group">
                        
                        <!-- Image Container with Hover Effect -->
                        <div class="w-48 h-32 md:w-64 md:h-44 rounded-lg overflow-hidden border-[4px] border-white shadow-lg bg-gray-50 mb-6 relative transition-transform duration-300 transform group-hover:-translate-y-2 group-hover:shadow-2xl flex items-center justify-center">
                            
                            <img 
                                v-if="step.url" 
                                :src="step.url" 
                                :alt="step.text || `Paso ${index + 1}`"
                                class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105"
                            />
                            
                            <!-- Placeholder if no image -->
                            <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-100">
                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-xs uppercase font-bold tracking-wider">Paso {{ index + 1 }}</span>
                            </div>

                            <!-- Badge with Number -->
                            <div class="absolute top-2 left-2 bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shadow-md border-2 border-white z-20">
                                {{ index + 1 }}
                            </div>
                        </div>
                        
                        <!-- Text Content --> 
                        <div class="text-center px-2">
                            <h3 v-if="step.text" class="text-lg md:text-xl font-bold text-gray-800 leading-tight">
                                {{ step.text }}
                            </h3>
                        </div>

                    </div>

                    <!-- Horizontal Connector Arrow (ONLY Desktop, IF not the last total step, AND IF not the last step in the current row of 3) -->
                    <div 
                        v-if="index < steps.length - 1 && (index + 1) % 3 !== 0" 
                        class="hidden lg:flex absolute top-16 md:top-20 right-0 translate-x-1/2 w-16 md:w-24 items-center justify-center text-indigo-300 z-0"
                    >
                        <hr class="absolute w-full border-t-4 border-dashed border-indigo-200" />
                        <svg class="w-10 h-10 md:w-12 md:h-12 text-indigo-400 relative bg-white/80 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>

                    <!-- Vertical Connector Arrow (Mobile/Tablet OR when breaking to next row on Desktop) -->
                    <!-- We use flex column to push it below the step content -->
                    <div 
                        v-if="index < steps.length - 1" 
                        class="lg:hidden absolute -bottom-8 md:-bottom-12 left-1/2 -translate-x-1/2 text-indigo-300 z-0 h-10 md:h-14 flex items-center justify-center overflow-visible"
                    >
                        <svg class="w-8 h-8 md:w-12 md:h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </div>

                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
defineProps({
    steps: {
        type: Array,
        required: true,
        default: () => []
    }
});
</script>
