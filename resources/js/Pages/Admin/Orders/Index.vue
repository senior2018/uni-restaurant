<script setup>
import { ref, computed, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '../../../Components/Modal.vue';
import Layout from '../Layout.vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
const page = usePage();
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
const showSimilarModal = ref(false);
const similarOrders = ref([]);
const baseOrder = ref(null);
const similarSort = ref('similarity_desc');
// Add a ref for the hasSimilar filter
const hasSimilar = ref(false);
const timeSort = ref('desc'); // 'desc' = Newest First, 'asc' = Oldest First

const pendingCancellationOrders = computed(() => props.orders.data.filter(o => o.cancellation_requested));
const unseenCancellationCount = computed(() => props.orders.data.filter(o => o.cancellation_requested && !o.cancellation_request_seen).length);

const sortedSimilarOrders = computed(() => {
    if (!similarOrders.value) return [];
    let arr = [...similarOrders.value];
    if (similarSort.value === 'similarity_desc') {
        arr.sort((a, b) => b.similarity_score - a.similarity_score);
    } else if (similarSort.value === 'similarity_asc') {
        arr.sort((a, b) => a.similarity_score - b.similarity_score);
    } else if (similarSort.value === 'date_desc') {
        arr.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    } else if (similarSort.value === 'date_asc') {
        arr.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    }
    return arr;
});

function applyFilters() {
    let status = filterStatus.value;
    let filters = {
        status: status === 'cancellation_requested' ? 'preparing' : status,
        staff_id: filterStaff.value,
        customer_id: filterCustomer.value,
        search: search.value,
        cancellation_requested: status === 'cancellation_requested' ? 1 : undefined,
        has_similar: hasSimilar.value ? 1 : undefined,
        sort: timeSort.value,
    };
    router.get(route('admin.orders.index'), filters, { preserveState: true, replace: true });
}

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'preparing', label: 'Preparing' },
    { value: 'delivered', label: 'Delivered' },
    { value: 'cancelled', label: 'Cancelled' },
];
const statusUpdate = ref('');
const statusLoading = ref(false);

function openModal(order) {
    selectedOrder.value = order;
    staffAssignId.value = order.staff_id || '';
    statusUpdate.value = order.status;
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

function openSimilarModal(order) {
    similarOrders.value = order.similar_orders || [];
    baseOrder.value = order;
    showSimilarModal.value = true;
}
function closeSimilarModal() {
    showSimilarModal.value = false;
    similarOrders.value = [];
    baseOrder.value = null;
}

function updateOrderStatus(orderId) {
    statusLoading.value = true;
    router.post(route('admin.orders.updateStatus', orderId), { status: statusUpdate.value }, {
        preserveScroll: true,
        onSuccess: () => statusLoading.value = false,
        onError: () => statusLoading.value = false
    });
}

// Helper functions for filter pills
const getStatusFilterName = (status) => {
    const statusNames = {
        'pending': 'Pending',
        'preparing': 'Preparing',
        'delivered': 'Delivered',
        'cancelled': 'Cancelled',
        'cancellation_requested': 'Cancellation Requested',
    };
    return statusNames[status] || status;
};
const getStaffName = (staffId) => {
    if (staffId === 'unassigned') return 'Unassigned';
    const s = props.staff.find(st => String(st.id) === String(staffId));
    return s ? s.name : staffId;
};
const getCustomerName = (customerId) => {
    const c = props.customers.find(cu => String(cu.id) === String(customerId));
    return c ? c.name : customerId;
};
const hasActiveFilters = computed(() => {
    return search.value || filterStatus.value !== 'all' || (filterStaff.value && filterStaff.value !== '') || (filterCustomer.value && filterCustomer.value !== '') || hasSimilar.value || timeSort.value !== 'desc';
});
const clearAllFilters = () => {
    search.value = '';
    filterStatus.value = 'all';
    filterStaff.value = '';
    filterCustomer.value = '';
    hasSimilar.value = false;
    timeSort.value = 'desc';
    applyFilters();
};
</script>

<template>
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Order Management</h1>
        <div v-if="page.props.flash?.warning" class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ page.props.flash.warning }}
        </div>
        <div v-if="page.props.flash?.success" class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-800 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ page.props.flash.success }}
        </div>
        <div v-if="page.props.flash?.error" class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-800 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ page.props.flash.error }}
        </div>
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-end md:space-x-6 gap-4">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select v-model="filterStatus" @change="applyFilters" class="border rounded px-2 py-1 w-full">
                            <option value="all">All</option>
                            <option value="pending">Pending</option>
                            <option value="preparing">Preparing</option>
                            <option value="cancellation_requested">Cancellation Requested</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Staff</label>
                        <select v-model="filterStaff" @change="applyFilters" class="border rounded px-2 py-1 w-full">
                            <option value="">All</option>
                            <option value="unassigned">Unassigned</option>
                            <option v-for="s in staff" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Customer</label>
                        <select v-model="filterCustomer" @change="applyFilters" class="border rounded px-2 py-1 w-full">
                            <option value="">All</option>
                            <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Search</label>
                        <input type="text" v-model="search" @keyup.enter="applyFilters" placeholder="Order ID, Name..." class="border rounded px-2 py-1 w-full" />
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:items-end md:space-x-4 gap-2 mt-4 md:mt-0">
                    <div class="flex items-center">
                        <input type="checkbox" id="hasSimilar" v-model="hasSimilar" @change="applyFilters" class="mr-2" />
                        <label for="hasSimilar" class="text-sm font-medium">Has Similar Orders</label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Sort by Time</label>
                        <select v-model="timeSort" @change="applyFilters" class="border rounded px-2 py-1 w-full min-w-[140px]">
                            <option value="desc">Newest First</option>
                            <option value="asc">Oldest First</option>
                        </select>
                    </div>
                    <div class="flex-1 flex justify-end md:justify-end mt-2 md:mt-0">
                        <!-- Removed Apply button; filters now apply automatically on change/enter -->
                    </div>
                </div>
            </div>
            <!-- Active Filters Display -->
            <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
                <span class="text-sm text-gray-600">Active filters:</span>
                <span v-if="search" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Search: "{{ search }}"
                    <button @click="search = ''; applyFilters()" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                </span>
                <span v-if="filterStatus !== 'all'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ getStatusFilterName(filterStatus) }}
                    <button @click="filterStatus = 'all'; applyFilters()" class="ml-1 text-green-600 hover:text-green-800">×</button>
                </span>
                <span v-if="filterStaff && filterStaff !== ''" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Staff: {{ getStaffName(filterStaff) }}
                    <button @click="filterStaff = ''; applyFilters()" class="ml-1 text-purple-600 hover:text-purple-800">×</button>
                </span>
                <span v-if="filterCustomer && filterCustomer !== ''" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                    Customer: {{ getCustomerName(filterCustomer) }}
                    <button @click="filterCustomer = ''; applyFilters()" class="ml-1 text-pink-600 hover:text-pink-800">×</button>
                </span>
                <span v-if="hasSimilar" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Has Similar Orders
                    <button @click="hasSimilar = false; applyFilters()" class="ml-1 text-yellow-600 hover:text-yellow-800">×</button>
                </span>
                <span v-if="timeSort !== 'desc'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    Sort: Oldest First
                    <button @click="timeSort = 'desc'; applyFilters()" class="ml-1 text-gray-600 hover:text-gray-800">×</button>
                </span>
                <button @click="clearAllFilters" class="px-4 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2 shadow ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Clear Filters
                </button>
            </div>
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
                    <tr v-for="order in orders.data" :key="order.id" :class="['border-b', 'hover:bg-blue-50 transition']">
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
                            }">
                                {{ order.status === 'preparing' && order.cancellation_requested ? 'Preparing (Cancellation Requested)' : order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                            </span>
                            <i v-if="order.status === 'preparing' && order.cancellation_requested" class="fas fa-question-circle text-orange-500 ml-2" title="Cancellation Requested"></i>
                            <button v-if="order.status === 'pending' && !order.staff_id && order.similar_orders && order.similar_orders.length" @click="openSimilarModal(order)" class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-200 text-blue-800 hover:bg-blue-300 transition">
                                {{ order.similar_orders.length }} similar order{{ order.similar_orders.length > 1 ? 's' : '' }}
                            </button>
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
                    <select v-model="staffAssignId" class="border rounded px-2 py-1 min-w-120">
                        <option value="">Unassigned</option>
                        <option v-for="s in staff" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <button @click="assignStaff(selectedOrder.id, staffAssignId)" :disabled="assignLoading" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ assignLoading ? 'Assigning...' : 'Assign' }}
                    </button>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Change Status</label>
                    <select v-model="statusUpdate" class="border rounded px-2 py-1 min-w-120">
                        <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                    <button @click="updateOrderStatus(selectedOrder.id)" :disabled="statusLoading" class="ml-2 px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                        {{ statusLoading ? 'Updating...' : 'Update Status' }}
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
        <!-- Similar Orders Modal -->
        <Modal :show="showSimilarModal" @close="closeSimilarModal">
            <div class="p-6 max-w-2xl w-full mx-auto">
                <h3 class="text-lg font-bold mb-4">Similar Orders to #{{ baseOrder?.id }}</h3>
                <div class="flex items-center gap-4 mb-4">
                    <label class="text-sm font-medium">Sort by:</label>
                    <select v-model="similarSort" class="border rounded px-2 py-1 text-sm">
                        <option value="similarity_desc">Similarity (High to Low)</option>
                        <option value="similarity_asc">Similarity (Low to High)</option>
                        <option value="date_desc">Newest First</option>
                        <option value="date_asc">Oldest First</option>
                    </select>
                </div>
                <div v-if="sortedSimilarOrders.length === 0" class="text-gray-500">No similar orders found.</div>
                <div v-else class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    <div v-for="order in sortedSimilarOrders" :key="order.id" class="border rounded p-4 bg-blue-50">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-2 gap-2">
                            <div>
                                <span class="font-medium">Order #{{ order.id }}</span>
                                <span class="text-sm text-gray-500 ml-2">{{ order.created_at }}</span>
                                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-200 text-blue-800">{{ order.similarity_score }} similar meal{{ order.similarity_score > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700 mb-2">
                            <div>Customer: {{ order.user?.name || 'N/A' }}</div>
                            <div>Delivery: {{ order.delivery_location }}</div>
                            <div>Payment: {{ order.payment_method }}</div>
                            <div>Total: {{ order.total_price }}</div>
                        </div>
                        <div class="overflow-x-auto mt-2">
                            <table class="min-w-full text-xs border">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-2 py-1 text-left">Meal</th>
                                        <th class="px-2 py-1 text-left">Qty</th>
                                        <th class="px-2 py-1 text-left">Unit Price</th>
                                        <th class="px-2 py-1 text-left">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="meal in order.meals" :key="meal.id" class="border-t">
                                        <td class="px-2 py-1 whitespace-nowrap">{{ meal.name }}</td>
                                        <td class="px-2 py-1">{{ meal.quantity }}</td>
                                        <td class="px-2 py-1">{{ meal.price }}</td>
                                        <td class="px-2 py-1">{{ (meal.price * meal.quantity).toFixed(2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button @click="closeSimilarModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
                </div>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
.min-w-120 { min-width: 120px; }
</style>
