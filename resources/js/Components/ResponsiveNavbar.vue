<template>
    <nav class="fixed top-4 left-4 right-4 z-50 bg-white/95 backdrop-blur-md shadow-lg rounded-xl border border-green-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <Link :href="route('home')" class="text-xl font-bold text-green-600 hover:text-green-800 flex items-center gap-2">
                        <img src="/storage/image/logo-final.svg" alt="Logo" class="h-8 w-8 sm:h-10 sm:w-10" />
                        <span class="hidden lg:block">Our Restaurant</span>
                    </Link>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <Link :href="route('contact')"
                        class="px-4 py-2 border-2 border-blue-600 text-blue-600 rounded hover:bg-blue-50 transition flex items-center gap-2">
                        <i class="fas fa-headset"></i> Contact Support
                    </Link>
                    <Link v-if="canLogin"
                        :href="route('login')"
                        class="px-4 py-2 text-green-600 hover:text-green-800 transition">
                        Login
                    </Link>
                    <Link v-if="canRegister"
                        :href="route('register')"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Register
                    </Link>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button
                        @click="toggleMobileMenu"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500"
                        aria-expanded="false"
                    >
                        <span class="sr-only">Open main menu</span>
                        <!-- Hamburger icon -->
                        <svg v-if="!mobileMenuOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Close icon -->
                        <svg v-else class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-show="mobileMenuOpen" class="md:hidden absolute top-full right-0 mt-2 w-48 bg-white/95 backdrop-blur-md shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                <div class="py-2">
                    <Link :href="route('contact')"
                        class="flex items-center px-4 py-3 text-sm font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition">
                        <i class="fas fa-headset mr-3"></i> Contact Support
                    </Link>
                    <Link v-if="canLogin"
                        :href="route('login')"
                        class="flex items-center px-4 py-3 text-sm font-medium text-green-600 hover:text-green-800 hover:bg-green-50 transition">
                        <i class="fas fa-sign-in-alt mr-3"></i> Login
                    </Link>
                    <Link v-if="canRegister"
                        :href="route('register')"
                        class="flex items-center px-4 py-3 text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition">
                        <i class="fas fa-user-plus mr-3"></i> Register
                    </Link>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
        default: true
    },
    canRegister: {
        type: Boolean,
        default: true
    }
});

const mobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

// Close mobile menu when clicking outside
const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};

// Close mobile menu on route change
import { router } from '@inertiajs/vue3';
router.on('navigate', () => {
    mobileMenuOpen.value = false;
});
</script>
