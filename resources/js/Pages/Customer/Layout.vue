<script setup>
import BaseLayout from '../Shared/BaseLayout.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watchEffect } from 'vue';

const props = defineProps({
    user: Object,
});

const page = usePage();
const navLinks = [
    { name: 'Menu', route: route('menu.public'), icon: 'fas fa-utensils' },
    { name: 'My Plate', route: route('cart'), icon: 'fas fa-plate-wheat' },
    { name: 'My Orders', route: route('customer.orders'), icon: 'fas fa-receipt' },
];

const isActive = (href) => {
    return window.location.pathname === href;
};

const showDropdown = ref(false);
const initials = computed(() => {
    if (!props.user?.name) return '';
    return props.user.name.split(' ').map(n => n[0]).join('').toUpperCase();
});

function logout() {
    router.post(route('logout'));
}

watchEffect(() => {
    if (page.props.flash?.success) {
        setTimeout(() => { page.props.flash.success = null; }, 10000);
    }
    if (page.props.flash?.error) {
        setTimeout(() => { page.props.flash.error = null; }, 10000);
    }
});
</script>

<template>
    <BaseLayout>
        <nav class="bg-white shadow sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo/App Name -->
                    <div class="flex items-center gap-4">
                        <span class="text-2xl font-bold text-green-700 tracking-tight">
                            <i class="fas fa-leaf mr-2"></i> Uni Restaurant
                        </span>
                    </div>
                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center gap-6">
                        <Link v-for="link in navLinks" :key="link.name" :href="link.route"
                              :class="[isActive(link.route) ? 'text-green-700 font-bold' : 'text-gray-600 hover:text-green-600', 'flex items-center gap-2 transition-colors']">
                            <i :class="link.icon"></i>
                            {{ link.name }}
                        </Link>
                    </div>
                    <!-- User Avatar/Dropdown -->
                    <div class="relative flex items-center">
                        <button @click="showDropdown = !showDropdown" class="flex items-center gap-2 focus:outline-none">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-700 font-bold text-lg">
                                {{ initials }}
                            </span>
                            <span class="hidden md:inline text-gray-800 font-medium">{{ user.name }}</span>
                            <i class="fas fa-chevron-down ml-1 text-gray-500"></i>
                        </button>
                        <div v-if="showDropdown" class="absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-50">
                            <Link :href="route('profile.edit')" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</Link>
                            <button @click="logout" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">Logout</button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Flash Messages -->
        <div v-if="page.props.flash?.success" class="max-w-2xl mx-auto mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <span><i class="fas fa-check-circle mr-2"></i>{{ page.props.flash.success }}</span>
                <button @click="page.props.flash.success = null" class="text-green-700 hover:text-green-900">&times;</button>
            </div>
        </div>
        <div v-if="page.props.flash?.error" class="max-w-2xl mx-auto mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <span><i class="fas fa-exclamation-circle mr-2"></i>{{ page.props.flash.error }}</span>
                <button @click="page.props.flash.error = null" class="text-red-700 hover:text-red-900">&times;</button>
            </div>
        </div>
        <!-- Main Content -->
        <main class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </BaseLayout>
</template>

<style scoped>
nav { box-shadow: 0 2px 8px 0 rgba(0,0,0,0.03); }
</style>
