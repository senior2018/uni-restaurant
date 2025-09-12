<script setup>
import BaseLayout from '../Shared/BaseLayout.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watchEffect } from 'vue';
import TopNavBar from '@/Components/TopNavBar.vue';

const props = defineProps({
    user: Object,
});

const page = usePage();
const navLinks = [
    { name: 'Menu', route: route('menu.public'), icon: 'fas fa-utensils' },
    { name: 'My Plate', route: route('cart'), icon: 'fas fa-plate-wheat' },
    { name: 'My Orders', route: route('customer.orders'), icon: 'fas fa-receipt' },
    { name: 'Contact Support', route: route('contact'), icon: 'fas fa-headset' },
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
        <TopNavBar :links="navLinks" :user="user" role="customer" brand="Our Restaurant" />

        <!-- Flash Messages -->
        <div v-if="page.props.flash?.success" class="w-full px-responsive mt-4 relative z-10">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center justify-between shadow">
                <span><i class="fas fa-check-circle mr-2"></i>{{ page.props.flash.success }}</span>
                <button @click="page.props.flash.success = null" class="text-green-700 hover:text-green-900">&times;</button>
            </div>
        </div>

        <div v-if="page.props.flash?.error" class="w-full px-responsive mt-4 relative z-10">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center justify-between shadow">
                <span><i class="fas fa-exclamation-circle mr-2"></i>{{ page.props.flash.error }}</span>
                <button @click="page.props.flash.error = null" class="text-red-700 hover:text-red-900">&times;</button>
            </div>
        </div>

        <!-- Main Content -->
        <main class="py-responsive bg-gray-50 min-h-screen w-full">
            <div class="container-responsive">
                <slot />
            </div>
        </main>
    </BaseLayout>
</template>

<style scoped>
nav { box-shadow: 0 2px 8px 0 rgba(0,0,0,0.03); }
</style>
