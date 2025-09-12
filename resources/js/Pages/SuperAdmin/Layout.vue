<script setup>
import BaseLayout from '../Shared/BaseLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import TopNavBar from '@/Components/TopNavBar.vue';

const page = usePage();
const user = page.props.user;

const navLinks = [
    { name: 'Dashboard', route: route('superadmin.dashboard'), icon: 'fas fa-crown' },
    { name: 'Manage Admins', route: route('admin.list'), icon: 'fas fa-users-cog' },
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
