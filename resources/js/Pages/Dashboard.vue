<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
    recentOrders: Array,
    pendingAlerts: Array
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-gray-50">
        <!-- Navigation Bar -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Left Section -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <span class="text-xl font-semibold text-gray-800">
                                <i v-if="user.role === 'admin'" class="fas fa-user-shield text-purple-600 mr-2"></i>
                                <i v-if="user.role === 'staff'" class="fas fa-concierge-bell text-blue-600 mr-2"></i>
                                <i v-if="user.role === 'customer'" class="fas fa-user text-green-600 mr-2"></i>
                                {{ user.name }}'s Dashboard
                            </span>
                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="flex items-center space-x-6">
                        <div class="hidden sm:flex space-x-4">
                            <Link
                                :href="route('profile.edit')"
                                class="px-3 py-2 text-gray-600 hover:text-green-600 transition-colors"
                            >
                                Profile
                            </Link>
                            <Link
                                :href="route('logout')"
                                method="post"
                                class="px-3 py-2 text-red-600 hover:text-red-800 transition-colors"
                            >
                                Logout
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Admin Dashboard -->
                <div v-if="user.role === 'admin'" class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800">Administration Overview</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-2">System Users</h3>
                            <p class="text-3xl text-purple-600">0</p>
                            <p class="text-sm text-gray-500 mt-1">Registered users</p>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-2">Active Orders</h3>
                            <p class="text-3xl text-blue-600">0</p>
                            <p class="text-sm text-gray-500 mt-1">In progress</p>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-2">Pending Alerts</h3>
                            <p class="text-3xl text-red-600">{{ pendingAlerts.length }}</p>
                            <p class="text-sm text-gray-500 mt-1">Require attention</p>
                        </div>
                    </div>
                </div>

                <!-- Staff Dashboard -->
                <div v-if="user.role === 'staff'" class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800">Order Management</h2>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Recent Orders</h3>
                            <span class="text-sm text-gray-500">Last 24 hours</span>
                        </div>

                        <div class="space-y-4">
                            <div v-for="order in recentOrders" :key="order.id"
                                class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-medium">Order #{{ order.id }}</span>
                                        <span class="text-sm text-gray-500 ml-2">{{ order.created_at }}</span>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-sm"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                            'bg-green-100 text-green-800': order.status === 'completed'
                                        }">
                                        {{ order.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Dashboard -->
                <div v-if="user.role === 'customer'" class="space-y-6">
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
                        <div class="bg-white p-6 rounded-lg shadow-md space-y-4">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>

                            <!-- <Link :href="route('menu')"
                                class="block w-full p-3 text-center bg-green-100 text-green-700 rounded-lg
                                        hover:bg-green-200 transition-colors">
                                Place New Order
                            </Link>

                            <Link :href="route('alerts.create')"
                                class="block w-full p-3 text-center bg-red-100 text-red-700 rounded-lg
                                        hover:bg-red-200 transition-colors">
                                Report an Issue
                            </Link> -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
