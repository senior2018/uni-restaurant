<script setup>
import CustomerLayout from './Layout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    user: Object,
    orders: Array,
});

const totalOrders = computed(() => props.orders.length);
const pendingOrders = computed(() => props.orders.filter(o => o.status === 'pending').length);
const completedOrders = computed(() => props.orders.filter(o => o.status === 'completed').length);
const cancelledOrders = computed(() => props.orders.filter(o => o.status === 'cancelled').length);
const recentOrders = computed(() => props.orders.slice(0, 5));
</script>

<template>
    <CustomerLayout :user="user">
        <div class="min-h-screen bg-gray-50 p-6 max-w-5xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Welcome, {{ user.name }}!</h1>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow flex flex-col items-center">
                    <i class="fas fa-receipt text-2xl text-green-600 mb-2"></i>
                    <div class="text-sm text-gray-500">Total Orders</div>
                    <div class="text-2xl font-bold text-gray-800">{{ totalOrders }}</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow flex flex-col items-center">
                    <i class="fas fa-hourglass-half text-2xl text-yellow-500 mb-2"></i>
                    <div class="text-sm text-gray-500">Pending</div>
                    <div class="text-2xl font-bold text-gray-800">{{ pendingOrders }}</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow flex flex-col items-center">
                    <i class="fas fa-check-circle text-2xl text-green-500 mb-2"></i>
                    <div class="text-sm text-gray-500">Completed</div>
                    <div class="text-2xl font-bold text-gray-800">{{ completedOrders }}</div>
                                </div>
                <div class="bg-white p-6 rounded-xl shadow flex flex-col items-center">
                    <i class="fas fa-times-circle text-2xl text-red-500 mb-2"></i>
                    <div class="text-sm text-gray-500">Cancelled</div>
                    <div class="text-2xl font-bold text-gray-800">{{ cancelledOrders }}</div>
                            </div>
                        </div>
            <div class="flex flex-wrap gap-4 mb-8">
                <Link :href="route('menu.public')" class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-utensils"></i> Menu
                </Link>
                <Link :href="route('cart')" class="px-6 py-3 bg-green-100 text-green-700 rounded-lg font-semibold hover:bg-green-200 transition-colors flex items-center gap-2">
                    <i class="fas fa-plate-wheat"></i> My Plate
                </Link>
                <Link :href="route('customer.orders')" class="px-6 py-3 bg-blue-100 text-blue-700 rounded-lg font-semibold hover:bg-blue-200 transition-colors flex items-center gap-2">
                    <i class="fas fa-receipt"></i> My Orders
                </Link>
                    </div>
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Orders</h2>
                <div v-if="recentOrders.length === 0" class="text-gray-500">No recent orders yet.</div>
                <div v-else>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Order #</th>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in recentOrders" :key="order.id">
                                <td class="px-4 py-2">{{ order.id }}</td>
                                <td class="px-4 py-2">{{ new Date(order.created_at).toLocaleString() }}</td>
                                <td class="px-4 py-2">
                                    <span :class="[
                                        'px-3 py-1 rounded-full text-xs font-semibold',
                                        order.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                        order.status === 'completed' ? 'bg-green-100 text-green-700' :
                                        order.status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'
                                    ]">
                                        {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">{{ new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(order.total_price) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
