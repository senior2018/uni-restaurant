<script setup>
import StaffLayout from './Layout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '../../Components/Modal.vue';
import axios from 'axios';

const props = defineProps({
    user: Object,
    orders: Object, // paginated
    filters: Object
});

const search = ref(props.filters.search || '');
const showModal = ref(false);
const selectedOrder = ref(null);

function applyFilters() {
    router.get(route('staff.unassignedOrders'), {
        search: search.value
    }, { preserveState: true, replace: true });
}

function openModal(order) {
    selectedOrder.value = order;
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    selectedOrder.value = null;
}

function claimOrder(orderId) {
    axios.post(route('staff.orders.claim', orderId))
        .then(() => window.location.reload())
        .catch(err => alert(err.response?.data?.message || 'Failed to claim order.'));
}
</script>

<template>
    <StaffLayout :user="user">
        <div class="space-y-8">
            <h2 class="text-2xl font-bold text-yellow-700 mb-4 flex items-center gap-2">
                <i class="fas fa-inbox"></i> Unassigned Orders
            </h2>
            <!-- Filters/Search -->
            <div class="flex flex-wrap gap-4 items-end mb-4">
                <div>
                    <label class="block text-sm font-medium">Search</label>
                    <input type="text" v-model="search" @keyup.enter="applyFilters" placeholder="Order ID or Customer" class="border rounded px-2 py-1" />
                </div>
                <button @click="applyFilters" class="px-4 py-2 bg-yellow-600 text-white rounded">Apply</button>
            </div>
            <!-- Order List -->
            <div v-if="orders.data.length === 0" class="text-gray-500">No unassigned orders.</div>
            <div v-else>
                <div v-for="order in orders.data" :key="order.id" class="p-4 border rounded-lg flex justify-between items-center hover:bg-yellow-50 transition-colors mb-2">
                    <div>
                        <span class="font-medium">Order #{{ order.id }}</span>
                        <span class="text-sm text-gray-500 ml-2">{{ order.created_at }}</span>
                        <span class="ml-4 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ order.status }}</span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="openModal(order)" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">View Details</button>
                        <button @click="claimOrder(order.id)" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Claim</button>
                    </div>
                </div>
                <!-- Pagination -->
                <div class="flex justify-center mt-6 gap-2">
                    <button :disabled="!orders.prev_page_url" @click="router.get(orders.prev_page_url, {}, { preserveState: true, replace: true })" class="px-3 py-1 rounded border" :class="orders.prev_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Prev</button>
                    <span class="px-2">Page {{ orders.current_page }} of {{ orders.last_page }}</span>
                    <button :disabled="!orders.next_page_url" @click="router.get(orders.next_page_url, {}, { preserveState: true, replace: true })" class="px-3 py-1 rounded border" :class="orders.next_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Next</button>
                </div>
            </div>
            <!-- Order Details Modal -->
            <Modal :show="showModal" @close="closeModal">
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="font-bold">Order #{{ selectedOrder?.id }}</span>
                        <span class="text-xs text-gray-500">{{ selectedOrder?.created_at }}</span>
                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ selectedOrder?.status }}</span>
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
