<script setup>
import StaffLayout from './Layout.vue';
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Modal from '../../Components/Modal.vue';
import axios from 'axios';

const props = defineProps({
    user: Object,
    orders: Object, // paginated
    filters: Object
});

const statusOptions = [
    { value: 'all', label: 'All' },
    { value: 'pending', label: 'Pending' },
    { value: 'preparing', label: 'Preparing' },
    { value: 'cancellation_requested', label: 'Cancellation Requested' },
    { value: 'delivered', label: 'Delivered' },
];

const filterStatus = ref(props.filters.status || 'all');
const search = ref(props.filters.search || '');
const showModal = ref(false);
const selectedOrder = ref(null);

function applyFilters() {
    let status = filterStatus.value;
    let filters = {
        status: status === 'cancellation_requested' ? 'preparing' : status,
        cancellation_requested: status === 'cancellation_requested' ? 1 : undefined,
        search: search.value
    };
    router.get(route('staff.myOrders'), filters, { preserveState: true, replace: true });
}

function openModal(order) {
    selectedOrder.value = order;
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    selectedOrder.value = null;
}

function updateOrderStatus(orderId, newStatus) {
    axios.post(route('staff.orders.updateStatus', orderId), { status: newStatus })
        .then(() => window.location.reload())
        .catch(err => alert(err.response?.data?.message || 'Failed to update order status.'));
}

function approveCancellation(orderId) {
    axios.post(route('staff.orders.approveCancellation', orderId))
        .then(() => window.location.reload())
        .catch(err => alert(err.response?.data?.message || 'Failed to approve cancellation.'));
}
function rejectCancellation(orderId) {
    axios.post(route('staff.orders.rejectCancellation', orderId))
        .then(() => window.location.reload())
        .catch(err => alert(err.response?.data?.message || 'Failed to reject cancellation.'));
}

const getStatusFilterName = (status) => {
    const statusNames = {
        'all': 'All',
        'pending': 'Pending',
        'preparing': 'Preparing',
        'cancellation_requested': 'Cancellation Requested',
        'delivered': 'Delivered',
    };
    return statusNames[status] || status;
};
const hasActiveFilters = computed(() => {
    return search.value || filterStatus.value !== 'all';
});
const clearAllFilters = () => {
    search.value = '';
    filterStatus.value = 'all';
    applyFilters();
};
</script>

<template>
    <StaffLayout :user="user">
        <div class="space-y-6 sm:space-y-8">
            <h2 class="text-xl sm:text-2xl font-bold text-blue-700 mb-4 flex items-center gap-2">
                <i class="fas fa-tasks"></i> My Orders
            </h2>
            <!-- Filters/Search -->
            <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4 items-end mb-4">
                <div class="w-full sm:w-auto">
                    <label class="block text-xs sm:text-sm font-medium">Status</label>
                    <select v-model="filterStatus" @change="applyFilters" class="border rounded px-2 py-1 text-sm sm:text-base w-full sm:w-auto">
                        <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-xs sm:text-sm font-medium">Search</label>
                    <input type="text" v-model="search" @keyup.enter="applyFilters" placeholder="Order ID or Customer" class="border rounded px-2 py-1 text-sm sm:text-base w-full sm:w-auto" />
                </div>
                <!-- Removed Apply button; filters now apply automatically on change/enter -->
            </div>
            <!-- Active Filters Display -->
            <div v-if="hasActiveFilters" class="mb-4 flex flex-wrap gap-2">
                <span class="text-sm text-gray-600">Active filters:</span>
                <span v-if="search" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Search: "{{ search }}"
                    <button @click="search = ''; applyFilters()" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                </span>
                <span v-if="filterStatus !== 'all'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ getStatusFilterName(filterStatus) }}
                    <button @click="filterStatus = 'all'; applyFilters()" class="ml-1 text-green-600 hover:text-green-800">×</button>
                </span>
                <button @click="clearAllFilters" class="px-4 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2 shadow ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Clear Filters
                </button>
            </div>
            <!-- Order List -->
            <div v-if="orders.data.length === 0" class="text-gray-500 text-sm sm:text-base">No orders found.</div>
            <div v-else>
                <div v-for="order in orders.data" :key="order.id" class="p-3 sm:p-4 border rounded-lg flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 hover:bg-blue-50 transition-colors mb-2">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                        <span class="font-medium text-sm sm:text-base">Order #{{ order.id }}</span>
                        <span class="text-xs sm:text-sm text-gray-500">{{ order.created_at }}</span>
                        <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold"
                              :class="{
                                'bg-orange-100 text-orange-700': order.status === 'preparing' && order.cancellation_requested,
                                'bg-yellow-100 text-yellow-700': order.status === 'pending',
                                'bg-blue-100 text-blue-700': order.status === 'preparing' && !order.cancellation_requested,
                                'bg-green-100 text-green-700': order.status === 'delivered',
                                'bg-red-100 text-red-700': order.status === 'cancelled',
                              }">
                            {{ order.status === 'preparing' && order.cancellation_requested ? 'Preparing (Cancellation Requested)' : order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <button @click="openModal(order)" class="btn-responsive bg-gray-200 rounded hover:bg-gray-300">View Details</button>
                        <button v-if="order.status === 'pending'" @click="updateOrderStatus(order.id, 'preparing')" class="btn-responsive bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">Start Preparing</button>
                        <button v-if="order.status === 'preparing'" @click="updateOrderStatus(order.id, 'delivered')" class="btn-responsive bg-green-600 text-white rounded hover:bg-green-700 transition">Mark as Delivered</button>
                    </div>
                </div>
                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row justify-center items-center mt-6 gap-2">
                    <button :disabled="!orders.prev_page_url" @click="router.get(orders.prev_page_url, {}, { preserveState: true, replace: true })" class="btn-responsive rounded border" :class="orders.prev_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Prev</button>
                    <span class="px-2 text-sm sm:text-base">Page {{ orders.current_page }} of {{ orders.last_page }}</span>
                    <button :disabled="!orders.next_page_url" @click="router.get(orders.next_page_url, {}, { preserveState: true, replace: true })" class="btn-responsive rounded border" :class="orders.next_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Next</button>
                </div>
            </div>
            <!-- Order Details Modal -->
            <Modal :show="showModal" @close="closeModal">
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="font-bold">Order #{{ selectedOrder?.id }}</span>
                        <span class="text-xs text-gray-500">{{ selectedOrder?.created_at }}</span>
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold"
                              :class="{
                                'bg-orange-100 text-orange-700': selectedOrder?.status === 'preparing' && selectedOrder?.cancellation_requested,
                                'bg-yellow-100 text-yellow-700': selectedOrder?.status === 'pending',
                                'bg-blue-100 text-blue-700': selectedOrder?.status === 'preparing' && !selectedOrder?.cancellation_requested,
                                'bg-green-100 text-green-700': selectedOrder?.status === 'delivered',
                                'bg-red-100 text-red-700': selectedOrder?.status === 'cancelled',
                              }">
                            {{ selectedOrder?.status === 'preparing' && selectedOrder?.cancellation_requested ? 'Preparing (Cancellation Requested)' : selectedOrder?.status?.charAt(0).toUpperCase() + selectedOrder?.status?.slice(1) }}
                        </span>
                    </div>
                    <div v-if="selectedOrder?.cancellation_requested" class="mb-4 p-4 bg-red-50 border border-red-300 rounded">
                        <div class="font-semibold text-red-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i> Cancellation Requested
                        </div>
                        <div class="text-gray-700 mb-2"><span class="font-medium">Reason:</span> {{ selectedOrder.cancellation_reason }}</div>
                        <div class="flex gap-2">
                            <button @click="approveCancellation(selectedOrder.id)" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Approve</button>
                            <button @click="rejectCancellation(selectedOrder.id)" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Reject</button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="font-medium">Customer: <span class="text-gray-700">{{ selectedOrder?.user?.name || 'N/A' }}</span></div>
                        <div class="text-sm text-gray-500">Delivery: {{ selectedOrder?.delivery_location }}</div>
                        <div class="text-sm text-gray-500">Payment: {{ selectedOrder?.payment_method }}</div>
                        <div class="text-sm text-gray-500">Total: {{ selectedOrder?.total_price }}</div>
                    </div>
                    <div class="overflow-x-auto mt-4">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-left">Meal</th>
                                    <th class="px-4 py-2 text-left">Quantity</th>
                                    <th class="px-4 py-2 text-left">Unit Price</th>
                                    <th class="px-4 py-2 text-left">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in selectedOrder?.items" :key="item.id">
                                    <td class="px-4 py-2">{{ item.meal?.name || 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ item.quantity }}</td>
                                    <td class="px-4 py-2">{{ item.price }}</td>
                                    <td class="px-4 py-2">{{ (item.price * item.quantity).toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button @click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
                    </div>
                </div>
            </Modal>
        </div>
    </StaffLayout>
</template>
