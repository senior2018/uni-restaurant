<script setup>
import CustomerLayout from './Layout.vue';
import { ref, computed, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Modal from '../../Components/Modal.vue';
import StarRating from '../../Components/StarRating.vue';
import { onMounted, nextTick } from 'vue';

const props = defineProps({
    orders: Array,
    user: Object,
});

const page = usePage();
const statusFilter = ref('all');
const expandedOrders = ref([]);
const editingOrderId = ref(null);
const editLocation = ref('');
const editPayment = ref('cash');
const editItems = ref([]);

const statusOptions = [
    { value: 'all', label: 'All' },
    { value: 'pending', label: 'Pending' },
    { value: 'processing', label: 'Processing' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const filteredOrders = computed(() => {
    if (statusFilter.value === 'all') return props.orders;
    return props.orders.filter(order => order.status === statusFilter.value);
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('sw-TZ', {
        style: 'currency',
        currency: 'TZS',
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};

const statusBadgeClass = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-700';
        case 'processing': return 'bg-blue-100 text-blue-700';
        case 'completed': return 'bg-green-100 text-green-700';
        case 'cancelled': return 'bg-red-100 text-red-700';
        default: return 'bg-gray-100 text-gray-700';
    }
};

function toggleOrder(orderId) {
    if (expandedOrders.value.includes(orderId)) {
        expandedOrders.value = expandedOrders.value.filter(id => id !== orderId);
    } else {
        expandedOrders.value.push(orderId);
    }
}

function startEdit(order) {
    editingOrderId.value = order.id;
    editLocation.value = order.delivery_location;
    editPayment.value = order.payment_method;
    editItems.value = order.items.map(item => ({
        id: item.id,
        meal_id: item.meal_id,
        name: item.meal?.name || '',
        quantity: item.quantity,
        price: item.price
    }));
}

function cancelEdit() {
    editingOrderId.value = null;
}

function updateOrder(orderId) {
    router.post(route('customer.orders.update', orderId), {
        delivery_location: editLocation.value,
        payment_method: editPayment.value,
        items: editItems.value.map(item => ({
            id: item.id,
            meal_id: item.meal_id,
            quantity: Number(item.quantity),
            price: Number(item.price)
        }))
    }, {
        preserveScroll: true,
        onSuccess: () => {
            editingOrderId.value = null;
        }
    });
}

const showCancelModal = ref(false);
const cancelOrderId = ref(null);
const cancelReason = ref('');
const cancelOrderStatus = ref('pending');

function openCancelModal(order) {
    cancelOrderId.value = order.id;
    cancelOrderStatus.value = order.status;
    cancelReason.value = '';
    showCancelModal.value = true;
}
function closeCancelModal() {
    showCancelModal.value = false;
    cancelOrderId.value = null;
    cancelReason.value = '';
}
function submitCancelOrder() {
    if (!cancelReason.value.trim()) return;
    router.post(route('customer.orders.cancel', cancelOrderId.value), { reason: cancelReason.value }, {
        preserveScroll: true,
        onSuccess: () => closeCancelModal(),
        onError: () => {},
    });
}

function cancelCancellationRequest(orderId) {
    router.post(route('customer.orders.cancelRequest', orderId), {}, {
        preserveScroll: true,
        onSuccess: () => {},
        onError: () => {},
    });
}

// --- Rating Modal State ---
const showRatingModal = ref(false);
const ratingOrder = ref(null); // The order being rated
const orderRating = ref(0); // 1-5 stars
const orderComment = ref(""); // Single comment for the whole order
const ratedOrderIds = computed(() => props.orders.filter(o => o.rating).map(o => o.id));

function openRatingModal(order) {
    ratingOrder.value = order;
    orderRating.value = 0;
    orderComment.value = "";
    showRatingModal.value = true;
}
function closeRatingModal() {
    showRatingModal.value = false;
    ratingOrder.value = null;
    orderRating.value = 0;
    orderComment.value = "";
}
function submitOrderRating() {
    if (!orderRating.value) return;
    router.post(route('ratings.store'), {
        order_id: ratingOrder.value.id,
        rating: orderRating.value,
        comment: orderComment.value,
    }, {
        preserveScroll: true,
        onSuccess: () => closeRatingModal(),
    });
}
// Auto-open modal for most recent delivered, unrated order
onMounted(() => {
    nextTick(() => {
        const deliveredUnrated = props.orders.filter(o => o.status === 'delivered' && !o.rating);
        if (deliveredUnrated.length > 0) {
            openRatingModal(deliveredUnrated[0]);
        }
    });
});

// --- Alert Modal State ---
const showAlertModal = ref(false);
const alertOrder = ref(null);
const alertReason = ref('');

function openAlertModal(order) {
    alertOrder.value = order;
    alertReason.value = '';
    showAlertModal.value = true;
}
function closeAlertModal() {
    showAlertModal.value = false;
    alertOrder.value = null;
    alertReason.value = '';
}
async function submitAlert() {
    if (!alertReason.value.trim()) return;
    await router.post(route('alerts.store'), {
        order_id: alertOrder.value.id,
        reason: alertReason.value,
    }, {
        preserveScroll: true,
        onSuccess: () => closeAlertModal(),
        onError: () => {},
    });
}
</script>

<template>
    <CustomerLayout :user="user">
        <!-- Flash Messages -->
        <div v-if="page.props.flash?.success" class="max-w-2xl mx-auto mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <span><i class="fas fa-check-circle mr-2"></i>{{ page.props.flash.success }}</span>
            </div>
        </div>
        <div v-if="page.props.flash?.error" class="max-w-2xl mx-auto mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <span><i class="fas fa-exclamation-circle mr-2"></i>{{ page.props.flash.error }}</span>
            </div>
        </div>
        <div class="min-h-screen bg-gray-50 p-6 w-full px-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">My Orders</h1>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                <div class="flex items-center gap-2">
                    <label for="statusFilter" class="font-medium text-gray-700">Filter by Status:</label>
                    <select id="statusFilter" v-model="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                    </select>
                </div>
                <div v-if="filteredOrders.length > 0" class="text-gray-500 text-sm mt-2 md:mt-0">
                    Showing {{ filteredOrders.length }} of {{ orders.length }} orders
                </div>
            </div>
            <div v-if="filteredOrders.length === 0" class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found for this filter</h3>
                <button @click="$inertia.visit(route('menu.public'))" class="mt-4 px-6 py-2 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors">
                    Go to Menu
                </button>
            </div>
            <div v-else class="space-y-8">
                <div v-for="order in filteredOrders" :key="order.id" class="bg-white rounded-xl shadow p-6">
                    <!-- Order summary row -->
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-2">
                        <div class="flex items-center gap-4">
                            <span class="font-semibold text-gray-700">Order #{{ order.id }}</span>
                            <span class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-2 md:mt-0">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                  :class="{
                                    'bg-orange-100 text-orange-700': order.status === 'preparing' && order.cancellation_requested,
                                    'bg-yellow-100 text-yellow-700': order.status === 'pending',
                                    'bg-blue-100 text-blue-700': order.status === 'preparing' && !order.cancellation_requested,
                                    'bg-green-100 text-green-700': order.status === 'completed',
                                    'bg-red-100 text-red-700': order.status === 'cancelled',
                                    'bg-gray-100 text-gray-700': !['pending','preparing','completed','cancelled'].includes(order.status),
                                  }">
                                {{ order.status === 'preparing' && order.cancellation_requested ? 'Preparing (Cancellation Requested)' : order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                            </span>
                            <span class="font-bold text-green-700 ml-2">{{ formatPrice(order.total_price) }}</span>
                            <button @click="toggleOrder(order.id)" class="ml-4 text-green-600 hover:underline text-xs">
                                {{ expandedOrders.includes(order.id) ? 'Hide Details' : 'Show Details' }}
                            </button>
                        </div>
                    </div>
                    <!-- Details section -->
                    <transition name="fade" v-if="expandedOrders.includes(order.id)">
                        <div>
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
                                        <tr v-for="item in order.items" :key="item.id">
                                            <td class="px-4 py-2">{{ item.meal?.name || 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ item.quantity }}</td>
                                            <td class="px-4 py-2">{{ formatPrice(item.price) }}</td>
                                            <td class="px-4 py-2">{{ formatPrice(item.price * item.quantity) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="mt-2 text-sm text-gray-600">
                                    <span class="mr-4">Delivery: <span class="font-medium text-gray-800">{{ order.delivery_location }}</span></span>
                                    <span class="mr-4">Payment: <span class="font-medium text-gray-800">{{ order.payment_method.replace('_', ' ').toUpperCase() }}</span></span>
                                </div>
                                <div v-if="order.status === 'pending' || order.status === 'preparing'" class="mt-4 flex gap-4 flex-wrap">
                                    <button v-if="order.status === 'pending'" @click="startEdit(order)" class="px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors">
                                        Edit
                                    </button>
                                    <button v-if="order.status === 'pending'" @click="openCancelModal(order)" class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors">
                                        Cancel Order
                                    </button>
                                    <template v-if="order.status === 'preparing'">
                                        <button v-if="!order.cancellation_requested" @click="openCancelModal(order)" class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors">
                                            Request Cancellation
                                        </button>
                                        <div v-else class="flex items-center gap-2">
                                            <button disabled class="px-4 py-2 bg-yellow-400 text-white rounded-lg font-semibold">Request Submitted</button>
                                            <button @click="cancelCancellationRequest(order.id)" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400">Cancel Request</button>
                                        </div>
                                    </template>
                                </div>
                                <!-- Show status if cancelled or rejected -->
                                <div v-if="order.status === 'cancelled'" class="mt-4 text-red-700 font-semibold">Order Cancelled</div>
                                <div v-else-if="order.status === 'preparing' && !order.cancellation_requested && order.cancellation_reason && page.props.flash?.error" class="mt-4 text-red-700 font-semibold">Request Rejected</div>
                                <div v-if="order.status === 'delivered'" class="mt-4 flex gap-4 flex-wrap">
                                    <button v-if="!order.ratings || order.ratings.length < order.items.length" @click="openRatingModal(order)" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition-colors">
                                        Rate Order
                                    </button>
                                    <div v-else class="flex flex-col gap-2">
                                        <div v-for="rating in order.ratings" :key="rating.id" class="flex items-center gap-2">
                                            <span class="font-medium text-green-700">{{ rating.meal?.name || 'Meal' }}:</span>
                                            <span class="flex items-center">
                                                <i v-for="n in 5" :key="n" class="fas fa-star" :class="n <= rating.rating ? 'text-yellow-400' : 'text-gray-300'"></i>
                                            </span>
                                            <span v-if="rating.comment" class="text-gray-600 ml-2 italic">"{{ rating.comment }}"</span>
                                            <span v-if="rating.response_comment" class="ml-4 px-2 py-1 rounded bg-green-100 text-green-800 text-xs">Staff: {{ rating.response_comment }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 flex gap-4 flex-wrap">
                                    <button @click="openAlertModal(order)" class="px-4 py-2 bg-accent-yellow text-gray-900 rounded-lg font-semibold hover:bg-yellow-300 transition-colors">
                                        Report Issue
                                    </button>
                                </div>
                                <div v-if="order.alerts && order.alerts.length" class="mt-4 flex flex-col gap-2">
                                    <div v-for="alert in order.alerts" :key="alert.id" class="p-3 rounded border border-yellow-200 bg-yellow-50">
                                        <div class="font-medium text-yellow-800">Alert: {{ alert.reason }}</div>
                                        <div v-if="alert.resolved" class="text-green-700 text-xs mt-1">Resolved</div>
                                        <div v-else-if="alert.staff_response" class="text-blue-700 text-xs mt-1">Staff: {{ alert.staff_response }}</div>
                                        <div v-else class="text-yellow-700 text-xs mt-1">Pending</div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="editingOrderId === order.id" class="mt-4 bg-gray-50 p-4 rounded-lg border">
                                <form @submit.prevent="updateOrder(order.id)" class="space-y-4">
                                    <div>
                                        <label class="block font-medium mb-1">Delivery Location</label>
                                        <input v-model="editLocation" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" />
                                    </div>
                                    <div>
                                        <label class="block font-medium mb-1">Payment Method</label>
                                        <select v-model="editPayment" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                            <option value="cash">Cash</option>
                                            <option value="mobile_money">Mobile Money</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-medium mb-1">Order Items</label>
                                        <div v-for="(item, idx) in editItems" :key="item.id" class="flex items-center gap-4 mb-2">
                                            <span class="w-40">{{ item.name }}</span>
                                            <input type="number" min="0" v-model.number="item.quantity" class="w-20 px-2 py-1 border rounded" />
                                            <span class="text-gray-500">x {{ formatPrice(item.price) }}</span>
                                            <button type="button" @click="item.quantity = 0" class="text-red-500 hover:underline text-xs">Remove</button>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors">Save</button>
                                        <button type="button" @click="cancelEdit" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg font-semibold hover:bg-gray-400 transition-colors">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
        <!-- Cancel/Request Modal -->
        <Modal :show="showCancelModal" @close="closeCancelModal">
            <div class="p-6">
                <h2 class="text-lg font-bold mb-2">{{ cancelOrderStatus === 'pending' ? 'Cancel Order' : 'Request Cancellation' }}</h2>
                <p class="mb-2 text-gray-600">Please provide a reason for {{ cancelOrderStatus === 'pending' ? 'cancelling' : 'requesting cancellation of' }} this order:</p>
                <textarea v-model="cancelReason" class="w-full border rounded p-2 mb-4" rows="3" placeholder="Reason (required)"></textarea>
                <div class="flex justify-end gap-2">
                    <button @click="closeCancelModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
                    <button :disabled="!cancelReason.trim()" @click="submitCancelOrder" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        {{ cancelOrderStatus === 'pending' ? 'Cancel Order' : 'Request Cancellation' }}
                    </button>
                </div>
            </div>
        </Modal>
        <!-- Rating Modal -->
        <Modal :show="showRatingModal" @close="closeRatingModal">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-4">Rate Your Order</h2>
                <div class="flex flex-col items-center mb-4">
                    <StarRating v-model="orderRating" :max="5" />
                </div>
                <textarea v-model="orderComment" class="w-full border rounded p-2 mb-4" rows="3" placeholder="Leave a comment (optional)"></textarea>
                <div class="flex justify-end gap-2">
                    <button @click="closeRatingModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button @click="submitOrderRating" :disabled="!orderRating" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50">Submit</button>
                </div>
            </div>
        </Modal>
        <!-- Alert Modal -->
        <Modal :show="showAlertModal" @close="closeAlertModal">
            <div class="p-6 max-w-lg mx-auto">
                <h2 class="text-lg font-bold mb-4">Report an Issue</h2>
                <textarea v-model="alertReason" class="w-full border rounded p-2 mb-4" rows="3" placeholder="Describe the issue (required)"></textarea>
                <div class="flex justify-end gap-2">
                    <button @click="closeAlertModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
                    <button :disabled="!alertReason.trim()" @click="submitAlert" class="px-4 py-2 bg-accent-yellow text-gray-900 rounded hover:bg-yellow-300">
                        Submit Alert
                    </button>
                </div>
            </div>
        </Modal>
    </CustomerLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
