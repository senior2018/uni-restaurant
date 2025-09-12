<script setup>
import SuperAdminLayout from './Layout.vue';

defineProps({
    user: Object,
    stats: Object,
    recentAdmins: Array
});
</script>

<template>
    <SuperAdminLayout>
        <div class="space-y-6 sm:space-y-8">
            <h2 class="text-responsive-lg font-bold text-gray-800">Super Admin Dashboard</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div class="card-responsive bg-white">
                    <h3 class="text-base sm:text-lg font-semibold mb-2">Total Users</h3>
                    <p class="text-2xl sm:text-3xl text-blue-600">{{ stats.total_users }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Across the system</p>
                </div>

                <div class="card-responsive bg-white">
                    <h3 class="text-base sm:text-lg font-semibold mb-2">Admins</h3>
                    <p class="text-2xl sm:text-3xl text-purple-600">{{ stats.total_admins }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Registered admins</p>
                </div>

                <div class="card-responsive bg-white">
                    <h3 class="text-base sm:text-lg font-semibold mb-2">Active Orders</h3>
                    <p class="text-2xl sm:text-3xl text-green-600">{{ stats.active_orders }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Current orders</p>
                </div>
            </div>

            <div class="card-responsive bg-white">
                <h3 class="text-lg font-semibold mb-4">Recent Admins</h3>
                <ul class="space-y-2">
                    <li v-for="admin in recentAdmins" :key="admin.id" class="flex flex-col sm:flex-row sm:justify-between gap-1 sm:gap-0">
                        <span class="text-sm sm:text-base">{{ admin.name }} ({{ admin.email }})</span>
                        <span class="text-xs sm:text-sm text-gray-500">{{ admin.created_at }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </SuperAdminLayout>
</template>
