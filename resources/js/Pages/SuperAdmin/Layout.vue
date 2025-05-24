<script setup>
import BaseLayout from '../Shared/BaseLayout.vue';
import { Link } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
</script>

<template>
    <BaseLayout title="Super Admin">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex space-x-4">
                        <NavLink :href="route('superadmin.dashboard')" active>
                            <i class="fas fa-crown text-red-600 mr-2"></i>
                            Super Admin
                        </NavLink>
                        <NavLink :href="route('admin.list')">
                            Manage Admins
                        </NavLink>
                    </div>
                    <div class="flex items-center space-x-6">
                        <Link :href="route('profile.edit')" class="text-gray-600 hover:text-green-600">
                            Profile
                        </Link>
                        <Link :href="route('logout')" method="post" class="text-red-600 hover:text-red-800">
                            Logout
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </BaseLayout>
</template>
