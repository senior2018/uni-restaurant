<script setup>
import CustomerLayout from './Layout.vue';
import MetricCard from '@/Components/UI/MetricCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
        <div class="space-y-6 sm:space-y-8">
            <h1 class="text-responsive-lg font-bold text-gray-800 mb-4">Welcome, {{ user.name }}!</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <MetricCard
                    title="Total Orders"
                    :value="totalOrders"
                    subtitle="All your orders"
                    icon="fas fa-receipt"
                    variant="default"
                />
                <MetricCard
                    title="Pending"
                    :value="pendingOrders"
                    subtitle="Orders in progress"
                    icon="fas fa-hourglass-half"
                    variant="warning"
                />
                <MetricCard
                    title="Completed"
                    :value="completedOrders"
                    subtitle="Successfully delivered"
                    icon="fas fa-check-circle"
                    variant="success"
                />
                <MetricCard
                    title="Cancelled"
                    :value="cancelledOrders"
                    subtitle="Cancelled orders"
                    icon="fas fa-times-circle"
                    variant="danger"
                />
            </div>
            <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4 mb-6 sm:mb-8">
                <Link :href="route('menu.public')">
                    <PrimaryButton
                        variant="primary"
                        size="lg"
                        icon="fas fa-utensils"
                        full-width
                    >
                        Menu
                    </PrimaryButton>
                </Link>
                <Link :href="route('cart')">
                    <PrimaryButton
                        variant="secondary"
                        size="lg"
                        icon="fas fa-plate-wheat"
                        full-width
                    >
                        My Plate
                    </PrimaryButton>
                </Link>
                <Link :href="route('customer.orders')">
                    <PrimaryButton
                        variant="info"
                        size="lg"
                        icon="fas fa-receipt"
                        full-width
                    >
                        My Orders
                    </PrimaryButton>
                </Link>
            </div>
            <div class="card-responsive bg-white">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4">Recent Orders</h2>
                <div v-if="recentOrders.length === 0" class="text-gray-500">No recent orders yet.</div>
                <div v-else>
                    <!-- Desktop Table -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="table-responsive">
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
                                            order.status === 'preparing' && order.cancellation_requested ? 'bg-orange-100 text-orange-700' :
                                            order.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                            order.status === 'completed' ? 'bg-green-100 text-green-700' :
                                            order.status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'
                                        ]">
                                            {{ order.status === 'preparing' && order.cancellation_requested ? 'Preparing (Cancellation Requested)' : order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">{{ new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(order.total_price) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="sm:hidden space-y-3">
                        <div v-for="order in recentOrders" :key="order.id" class="p-4 border rounded-lg bg-gray-50">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-medium">Order #{{ order.id }}</span>
                                <span :class="[
                                    'px-2 py-1 rounded-full text-xs font-semibold',
                                    order.status === 'preparing' && order.cancellation_requested ? 'bg-orange-100 text-orange-700' :
                                    order.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                    order.status === 'completed' ? 'bg-green-100 text-green-700' :
                                    order.status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'
                                ]">
                                    {{ order.status === 'preparing' && order.cancellation_requested ? 'Preparing (Cancellation Requested)' : order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 mb-1">{{ new Date(order.created_at).toLocaleString() }}</div>
                            <div class="text-sm font-medium">{{ new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(order.total_price) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
