<script setup>
import BaseLayout from '../Shared/BaseLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <BaseLayout title="Staff Dashboard">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center space-x-4">
                        <span class="text-xl font-semibold text-gray-800">
                            <i class="fas fa-concierge-bell text-blue-600 mr-2"></i>
                            {{ user.name }}'s Dashboard
                        </span>
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

<script>
export default {
    props: ['user']
}
</script>
