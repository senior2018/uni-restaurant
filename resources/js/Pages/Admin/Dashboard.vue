<script setup>
import AdminLayout from './Layout.vue';

defineProps({
    user: Object,
    stats: Object,
    pendingAlerts: Array
});
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">Administration Overview</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-2">System Users</h3>
                    <p class="text-3xl text-purple-600">{{ stats.total_users }}</p>
                    <p class="text-sm text-gray-500 mt-1">Registered users</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-2">Active Orders</h3>
                    <p class="text-3xl text-blue-600">{{ stats.active_orders }}</p>
                    <p class="text-sm text-gray-500 mt-1">In progress</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-2">Pending Alerts</h3>
                    <p class="text-3xl text-red-600">{{ pendingAlerts.length }}</p>
                    <p class="text-sm text-gray-500 mt-1">Require attention</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.dashboard-cards { margin-top: 2rem; }
.dashboard-card { min-height: 120px; cursor: pointer; }
</style>
