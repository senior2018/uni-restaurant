<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Layout from './Layout.vue';
defineOptions({ layout: Layout });

const props = defineProps({
    orders: Object, // paginated
    staff: Array,
    customers: Array
});

const showModal = ref(false);
const selectedOrder = ref(null);
const staffAssignId = ref(null);
const assignLoading = ref(false);

function openModal(order) {
    selectedOrder.value = order;
    staffAssignId.value = order.staff_id || '';
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    selectedOrder.value = null;
}

function approveCancellation(orderId) {
    router.post(route('admin.orders.approveCancellation', orderId), {}, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {},
    });
}
function rejectCancellation(orderId) {
    router.post(route('admin.orders.rejectCancellation', orderId), {}, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {},
    });
}
</script>

<template>
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Pending Cancellation Requests</h1>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Order #</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Staff</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders.data" :key="order.id" :class="['border-b', 'hover:bg-blue-50 transition']">
                        <td class="px-4 py-3 font-semibold">{{ order.id }}</td>
                        <td class="px-4 py-3">{{ order.user?.name || 'N/A' }}</td>
                        <td class="px-4 py-3">{{ order.staff?.name || 'Unassigned' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                  :class="{
                                    'bg-yellow-100 text-yellow-700': order.status === 'pending',
                                    'bg-orange-100 text-orange-700': order.status === 'preparing' && order.cancellation_requested,
                                    'bg-blue-100 text-blue-700': order.status === 'preparing' && !order.cancellation_requested,
                                    'bg-green-100 text-green-700': order.status === 'delivered',
                                    'bg-red-100 text-red-700': order.status === 'cancelled'
                                  }">
                                {{ order.status === 'preparing' && order.cancellation_requested ? 'Preparing (Cancellation Requested)' : order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                            </span>
                            <i v-if="order.status === 'preparing' && order.cancellation_requested" class="fas fa-question-circle text-orange-500 ml-2" title="Cancellation Requested"></i>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ order.created_at }}</td>
                        <td class="px-4 py-3">
                            <button @click="openModal(order)" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="flex justify-center mt-6 gap-2">
            <button :disabled="!orders.prev_page_url" @click="router.get(orders.prev_page_url, {}, { preserveState: true, replace: true })" class="px-3 py-1 rounded border" :class="orders.prev_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Prev</button>
            <span class="px-2">Page {{ orders.current_page }} of {{ orders.last_page }}</span>
            <button :disabled="!orders.next_page_url" @click="router.get(orders.next_page_url, {}, { preserveState: true, replace: true })" class="px-3 py-1 rounded border" :class="orders.next_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Next</button>
        </div>
        <!-- Order Details Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="font-bold">Order #{{ selectedOrder?.id }}</span>
                    <span class="text-xs text-gray-500">{{ selectedOrder?.created_at }}</span>
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold"
                          :class="{
                            'bg-yellow-100 text-yellow-700': selectedOrder?.status === 'pending',
                            'bg-orange-100 text-orange-700': selectedOrder?.status === 'preparing' && selectedOrder?.cancellation_requested,
                            'bg-blue-100 text-blue-700': selectedOrder?.status === 'preparing' && !selectedOrder?.cancellation_requested,
                            'bg-green-100 text-green-700': selectedOrder?.status === 'delivered',
                            'bg-red-100 text-red-700': selectedOrder?.status === 'cancelled'
                          }">
                        {{ selectedOrder?.status === 'preparing' && selectedOrder?.cancellation_requested ? 'Preparing (Cancellation Requested)' : selectedOrder?.status?.charAt(0).toUpperCase() + selectedOrder?.status?.slice(1) }}
                    </span>
                    <i v-if="selectedOrder?.status === 'preparing' && selectedOrder?.cancellation_requested" class="fas fa-question-circle text-orange-500 ml-2" title="Cancellation Requested"></i>
                </div>
                <div class="mb-4 space-y-1">
                    <div><span class="font-medium">Customer:</span> <span class="text-gray-700">{{ selectedOrder?.user?.name || 'N/A' }}</span></div>
                    <div><span class="font-medium">Staff:</span> <span class="text-gray-700">{{ selectedOrder?.staff?.name || 'Unassigned' }}</span></div>
                    <div><span class="font-medium">Cancellation Reason:</span> <span class="text-gray-700">{{ selectedOrder?.cancellation_reason }}</span></div>
                </div>
                <div class="flex gap-2 mt-4">
                    <button @click="approveCancellation(selectedOrder.id)" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Approve</button>
                    <button @click="rejectCancellation(selectedOrder.id)" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Reject</button>
                    <button @click="closeModal" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Close</button>
                </div>
            </div>
        </Modal>
    </div>
</template>
