<script setup>
import CustomerLayout from './Layout.vue';

defineProps({
    user: Object,
    recentOrders: Array
});
</script>

<template>
    <CustomerLayout :user="user">
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">Your Account</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Orders -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Recent Orders</h3>
                    <div class="space-y-4">
                        <div v-for="order in recentOrders" :key="order.id"
                            class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium">Order #{{ order.id }}</p>
                                    <p class="text-sm text-gray-500">{{ order.created_at }}</p>
                                </div>
                                <span class="text-sm"
                                    :class="{
                                        'text-yellow-600': order.status === 'pending',
                                        'text-green-600': order.status === 'delivered'
                                    }">
                                    {{ order.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <!-- <div class="bg-white p-6 rounded-lg shadow-md space-y-4">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <Link :href="route('menu')"
                        class="block w-full p-3 text-center bg-green-100 text-green-700 rounded-lg
                            hover:bg-green-200 transition-colors">
                        Place New Order
                    </Link>
                </div> -->
            </div>
        </div>
    </CustomerLayout>
</template>
