<script setup>
import StaffLayout from './Layout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '../../Components/Modal.vue';

const showModal = ref(false);
const selectedOrder = ref(null);

const props = defineProps({
    orders: Array,
    unseenCancellationCount: Number
});

function openModal(order) {
    selectedOrder.value = order;
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    selectedOrder.value = null;
}

function approveCancellation(orderId) {
    router.post(route('staff.orders.approveCancellation', orderId), {}, {
        preserveScroll: true,
        onSuccess: () => window.location.reload(),
    });
}
function rejectCancellation(orderId) {
    router.post(route('staff.orders.rejectCancellation', orderId), {}, {
        preserveScroll: true,
        onSuccess: () => window.location.reload(),
    });
}
</script>

<template>
    <StaffLayout>
        <div class="max-w-4xl mx-auto p-6">
            <h1 class="text-2xl font-bold text-yellow-700 mb-6 flex items-center gap-2">
                <i class="fas fa-ban"></i> Pending Cancellation Requests
                <span v-if="unseenCancellationCount > 0" class="ml-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                    {{ unseenCancellationCount }} new
                </span>
            </h1>
            <div v-if="orders.length === 0" class="text-gray-500">No pending cancellation requests.</div>
            <div v-else class="space-y-4">
                <div v-for="order in orders" :key="order.id" class="p-4 border rounded-lg flex justify-between items-center bg-yellow-50">
                    <div>
                        <span class="font-medium">Order #{{ order.id }}</span>
                        <span class="text-sm text-gray-500 ml-2">{{ order.created_at }}</span>
                        <span class="ml-4 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ order.status }}</span>
                        <span class="ml-2 text-xs text-gray-700">Reason: {{ order.cancellation_reason }}</span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="openModal(order)" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">View</button>
                    </div>
                </div>
            </div>
            <!-- Order Details Modal -->
            <Modal :show="showModal" @close="closeModal">
                <div v-if="selectedOrder" class="p-4">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="font-bold">Order #{{ selectedOrder.id }}</span>
                        <span class="text-xs text-gray-500">{{ selectedOrder.created_at }}</span>
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ selectedOrder.status }}</span>
                    </div>
                    <div class="mb-4 space-y-1">
                        <div><span class="font-medium">Customer:</span> <span class="text-gray-700">{{ selectedOrder.user?.name || 'N/A' }}</span></div>
                        <div><span class="font-medium">Delivery:</span> <span class="text-gray-700">{{ selectedOrder.delivery_location }}</span></div>
                        <div><span class="font-medium">Payment:</span> <span class="text-gray-700">{{ selectedOrder.payment_method }}</span></div>
                        <div><span class="font-medium">Total:</span> <span class="text-gray-700">{{ selectedOrder.total_price }}</span></div>
                        <div><span class="font-medium">Cancellation Reason:</span> <span class="text-gray-700">{{ selectedOrder.cancellation_reason }}</span></div>
                    </div>
                    <div class="flex gap-2 mt-4">
                        <button @click="approveCancellation(selectedOrder.id)" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Approve</button>
                        <button @click="rejectCancellation(selectedOrder.id)" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Reject</button>
                        <button @click="closeModal" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Close</button>
                    </div>
                </div>
            </Modal>
        </div>
    </StaffLayout>
</template>
