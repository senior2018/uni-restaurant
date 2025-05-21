<script setup>
import StaffLayout from './Layout.vue';

defineProps({
    user: Object,
    recentOrders: Array
});
</script>

<template>
    <StaffLayout :user="user">
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">Order Management</h2>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Recent Orders</h3>
                    <span class="text-sm text-gray-500">Last 24 hours</span>
                </div>

                <div class="space-y-4">
                    <div v-for="order in recentOrders" :key="order.id"
                        class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-medium">Order #{{ order.id }}</span>
                                <span class="text-sm text-gray-500 ml-2">{{ order.created_at }}</span>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm"
                                :class="{
                                    'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                    'bg-green-100 text-green-800': order.status === 'completed'
                                }">
                                {{ order.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StaffLayout>
</template>
