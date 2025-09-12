<script setup>
import AdminLayout from './Layout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
    stats: Object,
    pendingAlerts: Array
});
</script>

<template>
    <AdminLayout>
        <div class="space-y-6 sm:space-y-8">
            <h2 class="text-responsive-lg font-bold text-gray-800">Administration Overview</h2>

            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-6">
                <div class="card-responsive">
                    <h3 class="text-base sm:text-lg font-semibold mb-2">System Users</h3>
                    <p class="text-2xl sm:text-3xl text-purple-600">{{ stats.total_users }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Registered users</p>
                </div>
                <div class="bg-blue-100 card-responsive">
                    <h3 class="text-base sm:text-lg font-semibold mb-2">Pending Orders</h3>
                    <p class="text-2xl sm:text-3xl text-blue-700">{{ stats.pending_orders }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Awaiting processing</p>
                </div>
                <div class="bg-orange-100 card-responsive">
                    <h3 class="text-base sm:text-lg font-semibold mb-2">Preparing Orders</h3>
                    <p class="text-2xl sm:text-3xl text-orange-700">{{ stats.preparing_orders }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Being prepared</p>
                </div>
                <div class="bg-green-100 card-responsive">
                    <h3 class="text-base sm:text-lg font-semibold mb-2">Delivered Orders</h3>
                    <p class="text-2xl sm:text-3xl text-green-700">{{ stats.delivered_orders }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Completed</p>
                </div>
                <div class="bg-red-100 card-responsive">
                    <h3 class="text-base sm:text-lg font-semibold mb-2">Cancelled Orders</h3>
                    <p class="text-2xl sm:text-3xl text-red-700">{{ stats.cancelled_orders }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Cancelled by user or staff</p>
                </div>
                <div class="bg-yellow-100 card-responsive flex flex-col items-center relative">
                    <i class="fas fa-ban text-xl sm:text-2xl text-yellow-600 mb-2"></i>
                    <div class="text-xs sm:text-sm text-gray-500">Pending Cancellation Requests</div>
                    <div class="text-xl sm:text-2xl font-bold text-yellow-800">{{ stats.pending_cancellation_count || 0 }}</div>
                    <span v-if="stats.unseen_cancellation_count > 0"
                          class="absolute top-2 right-2 sm:right-4 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                        {{ stats.unseen_cancellation_count }}
                    </span>
                </div>
            </div>

            <!-- Analytics and Business Insights Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                <!-- Order Analytics Chart -->
                <div class="card-responsive">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        Order Analytics
                    </h3>
                    <div class="space-y-4">
                        <!-- Order Status Distribution -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-600 mb-2">Order Status Distribution</h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Pending</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-yellow-500 h-2 rounded-full" :style="`width: ${(stats.pending_orders / (stats.pending_orders + stats.preparing_orders + stats.delivered_orders + stats.cancelled_orders) * 100) || 0}%`"></div>
                                        </div>
                                        <span class="text-sm font-medium w-8">{{ stats.pending_orders }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Preparing</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-orange-500 h-2 rounded-full" :style="`width: ${(stats.preparing_orders / (stats.pending_orders + stats.preparing_orders + stats.delivered_orders + stats.cancelled_orders) * 100) || 0}%`"></div>
                                        </div>
                                        <span class="text-sm font-medium w-8">{{ stats.preparing_orders }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Delivered</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" :style="`width: ${(stats.delivered_orders / (stats.pending_orders + stats.preparing_orders + stats.delivered_orders + stats.cancelled_orders) * 100) || 0}%`"></div>
                                        </div>
                                        <span class="text-sm font-medium w-8">{{ stats.delivered_orders }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Cancelled</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-red-500 h-2 rounded-full" :style="`width: ${(stats.cancelled_orders / (stats.pending_orders + stats.preparing_orders + stats.delivered_orders + stats.cancelled_orders) * 100) || 0}%`"></div>
                                        </div>
                                        <span class="text-sm font-medium w-8">{{ stats.cancelled_orders }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Performance Metrics -->
                <div class="card-responsive">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-tachometer-alt text-green-600 mr-2"></i>
                        Business Performance
                    </h3>
                    <div class="space-y-4">
                        <!-- Success Rate -->
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-green-800">Order Success Rate</h4>
                                    <p class="text-xs text-green-600">Delivered vs Total Orders</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-green-700">
                                        {{ ((stats.delivered_orders / (stats.pending_orders + stats.preparing_orders + stats.delivered_orders + stats.cancelled_orders)) * 100 || 0).toFixed(1) }}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cancellation Rate -->
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-red-800">Cancellation Rate</h4>
                                    <p class="text-xs text-red-600">Cancelled vs Total Orders</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-red-700">
                                        {{ ((stats.cancelled_orders / (stats.pending_orders + stats.preparing_orders + stats.delivered_orders + stats.cancelled_orders)) * 100 || 0).toFixed(1) }}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Users -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Active Users</h4>
                                    <p class="text-xs text-blue-600">Total registered users</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-blue-700">{{ stats.total_users }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Process Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Order Processing Pipeline -->
                <div class="card-responsive">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-cogs text-purple-600 mr-2"></i>
                        Order Processing Pipeline
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-800">Pending Orders</div>
                                <div class="text-xs text-gray-500">Awaiting staff assignment</div>
                            </div>
                            <div class="ml-auto text-lg font-bold text-yellow-600">{{ stats.pending_orders }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-utensils text-orange-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-800">Preparing Orders</div>
                                <div class="text-xs text-gray-500">Currently being prepared</div>
                            </div>
                            <div class="ml-auto text-lg font-bold text-orange-600">{{ stats.preparing_orders }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-800">Delivered Orders</div>
                                <div class="text-xs text-gray-500">Successfully completed</div>
                            </div>
                            <div class="ml-auto text-lg font-bold text-green-600">{{ stats.delivered_orders }}</div>
                        </div>
                    </div>
                </div>

                <!-- System Health -->
                <div class="card-responsive">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-heartbeat text-red-600 mr-2"></i>
                        System Health
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Pending Cancellations</span>
                            <div class="flex items-center gap-2">
                                <div :class="stats.pending_cancellation_count > 0 ? 'bg-red-500' : 'bg-green-500'" class="w-3 h-3 rounded-full"></div>
                                <span class="text-sm font-medium">{{ stats.pending_cancellation_count || 0 }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Unseen Cancellations</span>
                            <div class="flex items-center gap-2">
                                <div :class="stats.unseen_cancellation_count > 0 ? 'bg-orange-500' : 'bg-green-500'" class="w-3 h-3 rounded-full"></div>
                                <span class="text-sm font-medium">{{ stats.unseen_cancellation_count || 0 }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">System Status</span>
                            <div class="flex items-center gap-2">
                                <div class="bg-green-500 w-3 h-3 rounded-full"></div>
                                <span class="text-sm font-medium text-green-600">Healthy</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card-responsive">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                        <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <Link :href="route('admin.orders.index')" class="block w-full p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-list text-blue-600"></i>
                                <span class="text-sm font-medium text-blue-800">Manage Orders</span>
                            </div>
                        </Link>
                        <Link :href="route('meals.index')" class="block w-full p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-utensils text-green-600"></i>
                                <span class="text-sm font-medium text-green-800">Manage Meals</span>
                            </div>
                        </Link>
                        <Link :href="route('admin.pendingCancellations')" class="block w-full p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-ban text-orange-600"></i>
                                <span class="text-sm font-medium text-orange-800">Review Cancellations</span>
                            </div>
                        </Link>
                        <Link :href="route('admin.alerts.index')" class="block w-full p-3 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                                <span class="text-sm font-medium text-red-800">View Alerts</span>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.dashboard-cards { margin-top: 2rem; }
.dashboard-card { min-height: 120px; cursor: pointer; }
</style>
