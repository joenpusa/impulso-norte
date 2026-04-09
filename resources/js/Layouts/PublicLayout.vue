<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

const page = usePage();
const currentUrl = computed(() => page.url);

const isMobileMenuOpen = ref(false);
const isSocialMenuOpen = ref(false);

const canvasRef = ref(null);

onMounted(() => {
    const canvas = canvasRef.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    let width = window.innerWidth;
    let height = window.innerHeight;
    canvas.width = width;
    canvas.height = height;

    let particles = [];
    const particleCount = Math.floor((width * height) / 10000); // Amount of nodes
    const mouse = { x: -1000, y: -1000, radius: 180 };

    class Particle {
        constructor() {
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            this.vx = (Math.random() - 0.5) * 1.5; 
            this.vy = (Math.random() - 0.5) * 1.5;
            this.radius = Math.random() * 2 + 1;
        }
        update() {
            this.x += this.vx;
            this.y += this.vy;
            if (this.x < 0 || this.x > width) this.vx *= -1;
            if (this.y < 0 || this.y > height) this.vy *= -1;
        }
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(29, 78, 216, 0.5)'; 
            ctx.fill();
        }
    }

    for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }

    let isMouseMoving = false;
    let mouseTimeout;

    const connect = () => {
        for (let a = 0; a < particles.length; a++) {
            for (let b = a; b < particles.length; b++) {
                const dx = particles[a].x - particles[b].x;
                const dy = particles[a].y - particles[b].y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < 110) {
                    ctx.beginPath();
                    ctx.strokeStyle = `rgba(29, 78, 216, ${0.25 - distance / 440})`;
                    ctx.lineWidth = 1;
                    ctx.moveTo(particles[a].x, particles[a].y);
                    ctx.lineTo(particles[b].x, particles[b].y);
                    ctx.stroke();
                }
            }
            const dxMouse = particles[a].x - mouse.x;
            const dyMouse = particles[a].y - mouse.y;
            const distanceMouse = Math.sqrt(dxMouse * dxMouse + dyMouse * dyMouse);
            
            if (isMouseMoving && distanceMouse < mouse.radius) {
                ctx.beginPath();
                ctx.strokeStyle = `rgba(29, 78, 216, ${0.5 - distanceMouse / (mouse.radius * 2)})`; 
                ctx.lineWidth = 1.5;
                ctx.moveTo(particles[a].x, particles[a].y);
                ctx.lineTo(mouse.x, mouse.y);
                ctx.stroke();
                // Pull particles to mouse
                particles[a].x -= dxMouse * 0.025;
                particles[a].y -= dyMouse * 0.025;
            }
        }
    };

    let animationFrameId;
    const animate = () => {
        ctx.clearRect(0, 0, width, height);
        for (let i = 0; i < particles.length; i++) {
            particles[i].update();
            particles[i].draw();
        }
        connect();
        animationFrameId = requestAnimationFrame(animate);
    };

    const handleResize = () => {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
    };

    const handleMouseMove = (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
        
        isMouseMoving = true;
        clearTimeout(mouseTimeout);
        mouseTimeout = setTimeout(() => {
            isMouseMoving = false;
        }, 150);
    };
    
    const handleMouseOut = () => {
        mouse.x = -1000;
        mouse.y = -1000;
    };

    window.addEventListener('resize', handleResize);
    window.addEventListener('mousemove', handleMouseMove);
    window.addEventListener('mouseout', handleMouseOut);
    
    canvas._cleanup = () => {
        window.removeEventListener('resize', handleResize);
        window.removeEventListener('mousemove', handleMouseMove);
        window.removeEventListener('mouseout', handleMouseOut);
        cancelAnimationFrame(animationFrameId);
    };

    animate();
});

onUnmounted(() => {
    if (canvasRef.value && canvasRef.value._cleanup) {
        canvasRef.value._cleanup();
    }
});

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    },
    mainMenu: {
        type: Object,
        default: () => null
    }
});

const backgroundStyle = computed(() => {
    const type = props.settings.background_type || 'color';
    const value = props.settings.background_value || '#f9fafb';

    if (type === 'image') {
        return { backgroundImage: `url('${value}')`, backgroundSize: 'cover', backgroundPosition: 'center', backgroundAttachment: 'fixed' };
    } else if (type === 'gradient') {
        return { background: value };
    } else {
        return { backgroundColor: value };
    }
});

// We can inject custom CSS in a style tag if needed, logic for that is complex in Vue. 
// For now, let's rely on the wrapper or simple scoped styles if user provides simple css related to body.
</script>

<template>
    <div class="min-h-screen flex flex-col font-sans text-gray-900 antialiased relative" :style="backgroundStyle">
        <!-- Interactive Nodes Graph Overlay -->
        <canvas ref="canvasRef" class="pointer-events-none fixed inset-0 z-[100]"></canvas>

        <div class="relative z-10 flex flex-col min-h-screen">
        <!-- Optional: Custom CSS injection if really needed, though risky -->
        <component is="style" v-if="settings.custom_css">
            {{ settings.custom_css }}
        </component>

        <!-- Custom Header Content -->
        <!-- This is independent of the menu as requested -->
        <header v-if="settings.header_content" class="site-header flex justify-center w-full" v-html="settings.header_content"></header>

        <!-- Main Navigation (Independent) -->
        <!-- You might want to style this container or let the user style it if they want full control, 
             but typically the menu logic is app-controlled. -->
        <nav v-if="mainMenu && mainMenu.items && mainMenu.items.length > 0" class="site-navigation bg-white/80 backdrop-blur-sm border-b border-gray-100 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex w-full justify-center">
                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <template v-for="item in mainMenu.items" :key="item.id">
                                <Link 
                                    v-if="item.page_id && item.page" 
                                    :href="route('pages.show', item.page.slug)" 
                                    :class="[
                                        'menu-item-custom inline-flex items-center px-3 py-2 mx-1 border-b-2 font-medium leading-5 focus:outline-none',
                                        currentUrl === '/pages/' + item.page.slug
                                            ? 'active-nav-item'
                                            : 'border-transparent text-gray-600 focus:text-gray-900 focus:border-gray-300'
                                    ]"
                                >
                                    {{ item.title }}
                                </Link>
                                <a 
                                    v-else 
                                    :href="item.url || '#'" 
                                    :target="item.target || '_self'"
                                    :class="[
                                        'menu-item-custom inline-flex items-center px-3 py-2 mx-1 border-b-2 font-medium leading-5 focus:outline-none',
                                        currentUrl === item.url
                                            ? 'active-nav-item'
                                            : 'border-transparent text-gray-600 focus:text-gray-900 focus:border-gray-300'
                                    ]"
                                >
                                    {{ item.title }}
                                </a>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button 
                            @click="isMobileMenuOpen = !isMobileMenuOpen" 
                            type="button" 
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" 
                            aria-expanded="false"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': isMobileMenuOpen, 'inline-flex': !isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !isMobileMenuOpen, 'inline-flex': isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform -translate-y-4 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="transform translate-y-0 opacity-100"
                leave-to-class="transform -translate-y-4 opacity-0"
            >
                <div v-show="isMobileMenuOpen" class="sm:hidden border-t border-gray-200 absolute w-full bg-white shadow-lg z-40 origin-top">
                    <div class="pt-2 pb-3 space-y-1">
                        <template v-for="item in mainMenu.items" :key="item.id">
                            <Link 
                                v-if="item.page_id && item.page" 
                                :href="route('pages.show', item.page.slug)" 
                                :class="[
                                    'mobile-menu-item-custom block pl-3 pr-4 py-3 border-l-4 font-medium focus:outline-none',
                                    currentUrl === '/pages/' + item.page.slug
                                        ? 'active-nav-item'
                                        : 'border-transparent text-gray-600 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300'
                                ]"
                                @click="isMobileMenuOpen = false"
                            >
                                {{ item.title }}
                            </Link>
                            <a 
                                v-else 
                                :href="item.url || '#'" 
                                :target="item.target || '_self'"
                                :class="[
                                    'mobile-menu-item-custom block pl-3 pr-4 py-3 border-l-4 font-medium focus:outline-none',
                                    currentUrl === item.url
                                        ? 'active-nav-item'
                                        : 'border-transparent text-gray-600 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300'
                                ]"
                                @click="isMobileMenuOpen = false"
                            >
                                {{ item.title }}
                            </a>
                        </template>
                    </div>
                </div>
            </transition>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Custom Footer Content -->
        <footer v-if="settings.footer_content" class="site-footer flex justify-center w-full" v-html="settings.footer_content"></footer>
        
        <!-- Fallback Footer if no content -->
        <footer v-else class="bg-gray-800 text-white py-8 text-center">
            <p>&copy; {{ new Date().getFullYear() }} {{ settings.site_title || 'Impulso Norte' }}. Todos los derechos reservados.</p>
        </footer>

        <!-- Floating Social Media Menu -->
        <div class="fixed right-0 top-1/2 transform -translate-y-1/2 z-50 flex shadow-lg rounded-l-lg bg-white border border-r-0 border-gray-200" 
             @mouseleave="isSocialMenuOpen = false">
            
            <!-- Toggle / Trigger -->
            <button @click="isSocialMenuOpen = !isSocialMenuOpen" 
                    @mouseenter="isSocialMenuOpen = true"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-3 rounded-l-lg focus:outline-none flex flex-col items-center justify-center transition-colors shadow-md z-10 relative">
                <!-- Share / Network Icon -->
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                </svg>
            </button>

            <!-- Social Icons Container with Width Animation -->
            <div class="overflow-hidden transition-all duration-300 ease-in-out bg-white"
                 :class="isSocialMenuOpen ? 'w-14 opacity-100' : 'w-0 opacity-0'">
                <div class="flex flex-col space-y-5 py-4 items-center w-14 border-l border-gray-100 h-full justify-center">
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/share/1CxBGxVkjF" target="_blank" class="text-blue-600 hover:scale-110 transition-transform" title="Facebook">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/impulsoprosur1" target="_blank" class="text-pink-600 hover:scale-110 transition-transform" title="Instagram">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.476 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

/* Basic resets for injected content */
.site-header :deep(img), .site-footer :deep(img) {
    max-width: 100%;
    height: auto;
}

/* Custom Menu Styles */
.menu-item-custom {
    font-family: 'Montserrat', sans-serif !important;
    font-size: 18pt !important;
    transition: all 0.3s ease-in-out;
}

.menu-item-custom:hover {
    transform: translateY(-3px) scale(1.02);
    color: #2563eb !important; /* blue-600 */
    background-color: #f3f4f6 !important; /* gray-100 */
    border-radius: 0.375rem 0.375rem 0 0; /* rounded-t-md */
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-bottom-color: #93c5fd !important; /* blue-300 */
}

.mobile-menu-item-custom {
    font-family: 'Montserrat', sans-serif !important;
    font-size: 18pt !important;
    transition: all 0.3s ease-in-out;
}

.mobile-menu-item-custom:hover {
    transform: translateX(8px);
    color: #2563eb !important;
    background-color: #f3f4f6 !important;
    border-left-color: #93c5fd !important;
}

.active-nav-item {
    font-weight: 700 !important;
    color: #1e40af !important; /* blue-800 */
    border-color: #2563eb !important; /* blue-600 desktop border-bottom, mobile border-left */
    background-color: #eff6ff !important; /* blue-50 */
}
</style>
