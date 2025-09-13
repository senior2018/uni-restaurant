<script setup>
import BaseLayout from '../Shared/BaseLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import TopNavBar from '@/Components/TopNavBar.vue';

const page = usePage();
const user = page.props.user;

const navLinks = [
    { name: 'Dashboard', route: route('superadmin.dashboard'), icon: 'fas fa-tachometer-alt' },
    { name: 'Admin Management', route: route('superadmin.admins'), icon: 'fas fa-users-cog' },
    { name: 'User Management', route: route('superadmin.users'), icon: 'fas fa-users' },
    { name: 'Restaurant Config', route: route('superadmin.restaurant.config'), icon: 'fas fa-store' },
    { name: 'Analytics', route: route('superadmin.analytics'), icon: 'fas fa-chart-line' },
    { name: 'System Config', route: route('superadmin.system.config'), icon: 'fas fa-cogs' },
    { name: 'System Logs', route: route('superadmin.system.logs'), icon: 'fas fa-file-alt' },
];
</script>

<template>
    <BaseLayout title="Super Admin">
        <TopNavBar :links="navLinks" :user="user" role="superadmin" brand="Super Admin" />

        <main class="py-responsive bg-gray-50 min-h-screen w-full">
            <div class="container-responsive">
                <slot />
            </div>
        </main>
    </BaseLayout>
</template>
