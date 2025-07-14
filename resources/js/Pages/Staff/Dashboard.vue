<script setup>
import StaffLayout from './Layout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({
    user: Object,
    unassignedOrders: Array,
    myOrders: Array
});

// Placeholder methods for claim/update (to be implemented with backend)
function claimOrder(orderId) {
    // TODO: Implement claim order action
    alert('Claim order ' + orderId);
}
function updateOrderStatus(orderId, newStatus) {
    // TODO: Implement update order status action
    alert('Update order ' + orderId + ' to ' + newStatus);
}
</script>

<template>
    <StaffLayout :user="user">
        <div class="space-y-10">
            <h2 class="text-2xl font-bold text-gray-800">Order Management</h2>

            <!-- Unassigned Orders -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Unassigned Orders</h3>
                </div>
                <div v-if="unassignedOrders.length === 0" class="text-gray-500">No unassigned orders.</div>
                <div v-else class="space-y-4">
                    <div v-for="order in unassignedOrders" :key="order.id" class="p-4 border rounded-lg flex justify-between items-center hover:bg-gray-50 transition-colors">
                        <div>
                            <span class="font-medium">Order #{{ order.id }}</span>
                            <span class="text-sm text-gray-500 ml-2">{{ order.created_at }}</span>
                            <span class="ml-4 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ order.status }}</span>
                        </div>
                        <button @click="claimOrder(order.id)" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Claim</button>
                    </div>
                </div>
            </div>

            <!-- My Orders -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">My Orders</h3>
                </div>
                <div v-if="myOrders.length === 0" class="text-gray-500">No orders assigned to you.</div>
                <div v-else class="space-y-4">
                    <div v-for="order in myOrders" :key="order.id" class="p-4 border rounded-lg flex justify-between items-center hover:bg-gray-50 transition-colors">
                        <div>
                            <span class="font-medium">Order #{{ order.id }}</span>
                            <span class="text-sm text-gray-500 ml-2">{{ order.created_at }}</span>
                            <span class="ml-4 px-3 py-1 rounded-full text-xs font-semibold"
                                :class="{
                                    'bg-yellow-100 text-yellow-700': order.status === 'pending',
                                    'bg-blue-100 text-blue-700': order.status === 'preparing',
                                    'bg-green-100 text-green-700': order.status === 'delivered'
                                }">
                                {{ order.status }}
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <button v-if="order.status === 'pending'" @click="updateOrderStatus(order.id, 'preparing')" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">Start Preparing</button>
                            <button v-if="order.status === 'preparing'" @click="updateOrderStatus(order.id, 'delivered')" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition">Mark as Delivered</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StaffLayout>
</template>
