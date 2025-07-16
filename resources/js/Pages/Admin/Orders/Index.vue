<script setup>
import { ref, computed, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '../../../Components/Modal.vue';
import Layout from '../Layout.vue';
import axios from 'axios';
defineOptions({ layout: Layout });

const props = defineProps({
    orders: Object, // paginated
    staff: Array,
    customers: Array,
    filters: Object
});

const filterStatus = ref(props.filters.status || 'all');
const filterStaff = ref(props.filters.staff_id || '');
const filterCustomer = ref(props.filters.customer_id || '');
const search = ref(props.filters.search || '');
const showModal = ref(false);
const selectedOrder = ref(null);
const assignStaffId = ref(null);
const assignLoading = ref(false);
const staffAssignId = ref(null);

const pendingCancellationOrders = computed(() => props.orders.data.filter(o => o.cancellation_requested));
const unseenCancellationCount = computed(() => props.orders.data.filter(o => o.cancellation_requested && !o.cancellation_request_seen).length);

function applyFilters() {
    router.get(route('admin.orders.index'), {
        status: filterStatus.value,
        staff_id: filterStaff.value,
        customer_id: filterCustomer.value,
        search: search.value
    }, { preserveState: true, replace: true });
}

function openModal(order) {
    selectedOrder.value = order;
    staffAssignId.value = order.staff_id || '';
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    selectedOrder.value = null;
}

function assignStaff(orderId, staffId) {
    assignLoading.value = true;
    router.post(route('admin.orders.assignStaff', orderId), { staff_id: staffId }, {
        preserveScroll: true,
        onSuccess: () => assignLoading.value = false,
        onError: () => assignLoading.value = false
    });
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

function showOnlyCancellationRequests() {
    router.get(route('admin.orders.index'), { ...props.filters, cancellation_only: true }, {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            // Mark all as seen
            axios.post(route('admin.orders.markCancellationSeen'));
        }
    });
}
</script>

<template>
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Order Management</h1>
        <!-- Stat Card for Pending Cancellation Requests -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-yellow-100 p-6 rounded-xl shadow flex flex-col items-center cursor-pointer hover:bg-yellow-200 relative"
                 @click="showOnlyCancellationRequests">
                <i class="fas fa-ban text-2xl text-yellow-600 mb-2"></i>
                <div class="text-sm text-gray-500">Pending Cancellation Requests</div>
                <div class="text-2xl font-bold text-yellow-800">{{ pendingCancellationOrders.length }}</div>
                <span v-if="unseenCancellationCount > 0"
                      class="absolute top-2 right-4 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                  {{ unseenCancellationCount }}
                </span>
            </div>
        </div>
        <!-- Filters -->
        <div class="flex flex-wrap gap-4 items-end mb-6">
            <div>
                <label class="block text-sm font-medium">Status</label>
                <select v-model="filterStatus" @change="applyFilters" class="border rounded px-2 py-1">
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="preparing">Preparing</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Staff</label>
                <select v-model="filterStaff" @change="applyFilters" class="border rounded px-2 py-1">
                    <option value="">All</option>
                    <option v-for="s in staff" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Customer</label>
                <select v-model="filterCustomer" @change="applyFilters" class="border rounded px-2 py-1">
                    <option value="">All</option>
                    <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Search</label>
                <input type="text" v-model="search" @keyup.enter="applyFilters" placeholder="Order ID, Name..." class="border rounded px-2 py-1" />
            </div>
            <button @click="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded">Apply</button>
        </div>
        <!-- Orders Table -->
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
                    <tr v-for="order in orders.data" :key="order.id" :class="[order.cancellation_requested ? 'border-2 border-yellow-400' : 'border-b', 'hover:bg-blue-50 transition']">
                        <td class="px-4 py-3 font-semibold">{{ order.id }}</td>
                        <td class="px-4 py-3">{{ order.user?.name || 'N/A' }}</td>
                        <td class="px-4 py-3">
                            {{ order.staff?.name || 'Unassigned' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="{
                                'bg-yellow-100 text-yellow-700': order.status === 'pending',
                                'bg-blue-100 text-blue-700': order.status === 'preparing',
                                'bg-green-100 text-green-700': order.status === 'delivered',
                                'bg-red-100 text-red-700': order.status === 'cancelled'
                            }">{{ order.status }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ order.created_at }}</td>
                        <td class="px-4 py-3">
                            <button @click="openModal(order)" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">View Details</button>
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
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold" :class="{
                        'bg-yellow-100 text-yellow-700': selectedOrder?.status === 'pending',
                        'bg-blue-100 text-blue-700': selectedOrder?.status === 'preparing',
                        'bg-green-100 text-green-700': selectedOrder?.status === 'delivered',
                        'bg-red-100 text-red-700': selectedOrder?.status === 'cancelled'
                    }">{{ selectedOrder?.status }}</span>
                </div>
                <div v-if="selectedOrder && selectedOrder.status !== 'cancelled'" class="mb-4">
                    <label class="block text-sm font-medium mb-1">Assign Staff</label>
                    <select v-model="staffAssignId" class="border rounded px-2 py-1 min-w-[120px]">
                        <option value="">Unassigned</option>
                        <option v-for="s in staff" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <button @click="assignStaff(selectedOrder.id, staffAssignId)" :disabled="assignLoading" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ assignLoading ? 'Assigning...' : 'Assign' }}
                    </button>
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
                    <div class="font-medium">Staff: <span class="text-gray-700">{{ selectedOrder?.staff?.name || 'Unassigned' }}</span></div>
                    <div class="text-sm text-gray-500">Delivery: {{ selectedOrder?.delivery_location }}</div>
                    <div class="text-sm text-gray-500">Payment: {{ selectedOrder?.payment_method }}</div>
                    <div class="text-sm text-gray-500">Total: {{ selectedOrder?.total_price }}</div>
                    <div v-if="selectedOrder?.status === 'cancelled' && selectedOrder?.cancelled_by" class="mt-2 text-sm text-red-700 font-semibold">
                        Cancelled by: {{ selectedOrder.cancelled_by.charAt(0).toUpperCase() + selectedOrder.cancelled_by.slice(1) }}
                        <div v-if="selectedOrder.cancellation_reason" class="text-xs text-gray-700 mt-1 font-normal">
                            Reason: {{ selectedOrder.cancellation_reason }}
                        </div>
                    </div>
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
</template>

<style scoped>
.min-w-[120px] { min-width: 120px; }
</style>
