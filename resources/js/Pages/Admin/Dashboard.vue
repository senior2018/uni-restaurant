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

            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-2">System Users</h3>
                    <p class="text-3xl text-purple-600">{{ stats.total_users }}</p>
                    <p class="text-sm text-gray-500 mt-1">Registered users</p>
                </div>
                <div class="bg-blue-100 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-2">Pending Orders</h3>
                    <p class="text-3xl text-blue-700">{{ stats.pending_orders }}</p>
                    <p class="text-sm text-gray-500 mt-1">Awaiting processing</p>
                </div>
                <div class="bg-orange-100 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-2">Preparing Orders</h3>
                    <p class="text-3xl text-orange-700">{{ stats.preparing_orders }}</p>
                    <p class="text-sm text-gray-500 mt-1">Being prepared</p>
                </div>
                <div class="bg-green-100 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-2">Delivered Orders</h3>
                    <p class="text-3xl text-green-700">{{ stats.delivered_orders }}</p>
                    <p class="text-sm text-gray-500 mt-1">Completed</p>
                </div>
                <div class="bg-red-100 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-2">Cancelled Orders</h3>
                    <p class="text-3xl text-red-700">{{ stats.cancelled_orders }}</p>
                    <p class="text-sm text-gray-500 mt-1">Cancelled by user or staff</p>
                </div>
                <div class="bg-yellow-100 p-6 rounded-xl shadow flex flex-col items-center relative">
                    <i class="fas fa-ban text-2xl text-yellow-600 mb-2"></i>
                    <div class="text-sm text-gray-500">Pending Cancellation Requests</div>
                    <div class="text-2xl font-bold text-yellow-800">{{ stats.pending_cancellation_count || 0 }}</div>
                    <span v-if="stats.unseen_cancellation_count > 0"
                          class="absolute top-2 right-4 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                        {{ stats.unseen_cancellation_count }}
                    </span>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.dashboard-cards { margin-top: 2rem; }
.dashboard-card { min-height: 120px; cursor: pointer; }
</style>
