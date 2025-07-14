<script setup>
import CustomerLayout from './Layout.vue';
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

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

function cancelOrder(orderId) {
    if (!confirm('Are you sure you want to cancel this order?')) return;
    router.post(route('customer.orders.cancel', orderId), {}, {
        preserveScroll: true,
        onSuccess: () => {}
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
        <div class="min-h-screen bg-gray-50 p-6 max-w-4xl mx-auto">
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
                            <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="statusBadgeClass(order.status)">
                                {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
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
                                <div v-if="order.status === 'pending'" class="mt-4 flex gap-4 flex-wrap">
                                    <button @click="startEdit(order)" class="px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors">
                                        Edit
                                    </button>
                                    <button @click="cancelOrder(order.id)" class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition-colors">
                                        Cancel Order
                                    </button>
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
