<script setup>
import BaseLayout from '../Shared/BaseLayout.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watchEffect } from 'vue';
import TopNavBar from '@/Components/TopNavBar.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const user = page.props.user;

const navLinks = [
    { name: 'Dashboard', route: route('dashboard'), icon: 'fas fa-tachometer-alt' },
    { name: 'My Orders', route: route('staff.myOrders', {}), icon: 'fas fa-tasks' },
    { name: 'Unassigned Orders', route: route('staff.unassignedOrders', {}), icon: 'fas fa-inbox' },
    { name: 'Pending Cancellations', route: route('staff.pendingCancellations'), icon: 'fas fa-ban', badge: page.props.unseenCancellationCount || 0 },
    { name: 'Alerts', route: route('staff.alerts.index'), icon: 'fas fa-exclamation-triangle', badge: page.props.unresolvedAlertCount || 0 },
    { name: 'Report Issue', route: route('staff.report-issue'), icon: 'fas fa-bug' },
];

const isActive = (href) => window.location.pathname === href;

const showDropdown = ref(false);
const initials = computed(() => {
    if (!user?.name) return '';
    return user.name.split(' ').map(n => n[0]).join('').toUpperCase();
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
        <TopNavBar :links="navLinks" :user="user" role="staff" brand="Staff Panel" />
        <!-- Flash Messages -->
        <div v-if="page.props.flash?.success" class="w-full px-responsive mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <span><i class="fas fa-check-circle mr-2"></i>{{ page.props.flash.success }}</span>
                <button @click="page.props.flash.success = null" class="text-green-700 hover:text-green-900">&times;</button>
            </div>
        </div>
        <div v-if="page.props.flash?.error" class="w-full px-responsive mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
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
