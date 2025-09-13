<script setup>
import SuperAdminLayout from './Layout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
    stats: Object,
    recentAdmins: Array,
    recentOrders: Array,
    systemHealth: Object,
    performanceMetrics: Object
});
</script>

<template>
    <SuperAdminLayout>
        <div class="space-y-6 sm:space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-responsive-lg font-bold text-gray-800">Super Admin Dashboard</h2>
                <div class="flex gap-2">
                    <Link :href="route('superadmin.admins.create')"
                          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Create Admin
                    </Link>
                </div>
            </div>

            <!-- System Health Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-heartbeat text-red-600 mr-2"></i>
                    System Health Status
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Database Status -->
                    <div class="text-center p-4 rounded-lg" :class="systemHealth.database_status?.status === 'healthy' ? 'bg-green-50' : 'bg-red-50'">
                        <i class="fas fa-database text-2xl mb-2" :class="systemHealth.database_status?.status === 'healthy' ? 'text-green-600' : 'text-red-600'"></i>
                        <div class="font-semibold" :class="systemHealth.database_status?.status === 'healthy' ? 'text-green-800' : 'text-red-800'">Database</div>
                        <div class="text-sm" :class="systemHealth.database_status?.status === 'healthy' ? 'text-green-600' : 'text-red-600'">
                            {{ systemHealth.database_status?.status || 'Unknown' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ systemHealth.database_status?.response_time || 'N/A' }}
                        </div>
                    </div>

                    <!-- Storage Status -->
                    <div class="text-center p-4 rounded-lg" :class="systemHealth.storage_usage?.status === 'healthy' ? 'bg-green-50' : systemHealth.storage_usage?.status === 'warning' ? 'bg-yellow-50' : 'bg-red-50'">
                        <i class="fas fa-hdd text-2xl mb-2" :class="systemHealth.storage_usage?.status === 'healthy' ? 'text-green-600' : systemHealth.storage_usage?.status === 'warning' ? 'text-yellow-600' : 'text-red-600'"></i>
                        <div class="font-semibold" :class="systemHealth.storage_usage?.status === 'healthy' ? 'text-green-800' : systemHealth.storage_usage?.status === 'warning' ? 'text-yellow-800' : 'text-red-800'">Storage</div>
                        <div class="text-sm" :class="systemHealth.storage_usage?.status === 'healthy' ? 'text-green-600' : systemHealth.storage_usage?.status === 'warning' ? 'text-yellow-600' : 'text-red-600'">
                            {{ systemHealth.storage_usage?.used || 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ systemHealth.storage_usage?.percentage || 0 }}% used
                        </div>
                    </div>

                    <!-- Memory Status -->
                    <div class="text-center p-4 rounded-lg" :class="systemHealth.memory_usage?.status === 'healthy' ? 'bg-green-50' : systemHealth.memory_usage?.status === 'warning' ? 'bg-yellow-50' : 'bg-red-50'">
                        <i class="fas fa-memory text-2xl mb-2" :class="systemHealth.memory_usage?.status === 'healthy' ? 'text-green-600' : systemHealth.memory_usage?.status === 'warning' ? 'text-yellow-600' : 'text-red-600'"></i>
                        <div class="font-semibold" :class="systemHealth.memory_usage?.status === 'healthy' ? 'text-green-800' : systemHealth.memory_usage?.status === 'warning' ? 'text-yellow-800' : 'text-red-800'">Memory</div>
                        <div class="text-sm" :class="systemHealth.memory_usage?.status === 'healthy' ? 'text-green-600' : systemHealth.memory_usage?.status === 'warning' ? 'text-yellow-600' : 'text-red-600'">
                            {{ systemHealth.memory_usage?.current || 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ systemHealth.memory_usage?.percentage || 0 }}% used
                        </div>
                    </div>

                    <!-- Cache Status -->
                    <div class="text-center p-4 rounded-lg" :class="systemHealth.cache_status?.status === 'healthy' ? 'bg-green-50' : 'bg-red-50'">
                        <i class="fas fa-bolt text-2xl mb-2" :class="systemHealth.cache_status?.status === 'healthy' ? 'text-green-600' : 'text-red-600'"></i>
                        <div class="font-semibold" :class="systemHealth.cache_status?.status === 'healthy' ? 'text-green-800' : 'text-red-800'">Cache</div>
                        <div class="text-sm" :class="systemHealth.cache_status?.status === 'healthy' ? 'text-green-600' : 'text-red-600'">
                            {{ systemHealth.cache_status?.status || 'Unknown' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ systemHealth.cache_status?.response_time || 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Additional System Info -->
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                    <div class="text-center">
                        <div class="text-sm text-gray-600">Last Backup</div>
                        <div class="font-semibold text-gray-800">{{ systemHealth.last_backup || 'Never' }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-600">System Load</div>
                        <div class="font-semibold text-gray-800">{{ systemHealth.system_uptime?.status || 'Unknown' }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-600">Free Space</div>
                        <div class="font-semibold text-gray-800">{{ systemHealth.storage_usage?.free || 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Main Statistics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="card-responsive bg-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold mb-2">Total Users</h3>
                            <p class="text-2xl sm:text-3xl text-blue-600">{{ stats.total_users }}</p>
                            <p class="text-xs sm:text-sm text-gray-500 mt-1">All registered users</p>
                        </div>
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                </div>

                <div class="card-responsive bg-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold mb-2">Admins</h3>
                            <p class="text-2xl sm:text-3xl text-purple-600">{{ stats.total_admins }}</p>
                            <p class="text-xs sm:text-sm text-gray-500 mt-1">System administrators</p>
                        </div>
                        <i class="fas fa-user-shield text-purple-600 text-2xl"></i>
                    </div>
                </div>

                <div class="card-responsive bg-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold mb-2">Active Orders</h3>
                            <p class="text-2xl sm:text-3xl text-green-600">{{ stats.active_orders }}</p>
                            <p class="text-xs sm:text-sm text-gray-500 mt-1">In progress</p>
                        </div>
                        <i class="fas fa-shopping-cart text-green-600 text-2xl"></i>
                    </div>
                </div>

                <div class="card-responsive bg-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base sm:text-lg font-semibold mb-2">Revenue</h3>
                            <p class="text-2xl sm:text-3xl text-yellow-600">${{ stats.total_revenue || 0 }}</p>
                            <p class="text-xs sm:text-sm text-gray-500 mt-1">Total earnings</p>
                        </div>
                        <i class="fas fa-dollar-sign text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Detailed Statistics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- User Breakdown -->
                <div class="card-responsive bg-white">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                        User Breakdown
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Customers</span>
                            <span class="font-semibold text-blue-600">{{ stats.total_customers }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Staff</span>
                            <span class="font-semibold text-green-600">{{ stats.total_staff }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Admins</span>
                            <span class="font-semibold text-purple-600">{{ stats.total_admins }}</span>
                        </div>
                    </div>
                </div>

                <!-- System Metrics -->
                <div class="card-responsive bg-white">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-tachometer-alt text-green-600 mr-2"></i>
                        System Metrics
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Total Meals</span>
                            <span class="font-semibold">{{ stats.total_meals }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Available Meals</span>
                            <span class="font-semibold text-green-600">{{ stats.available_meals }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Average Rating</span>
                            <span class="font-semibold text-yellow-600">{{ stats.average_rating ? Number(stats.average_rating).toFixed(1) : 'N/A' }} ⭐</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-6 flex items-center">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Performance Metrics
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Key Performance Indicators -->
                    <div>
                        <h4 class="text-md font-semibold mb-4 text-gray-800">Key Performance Indicators</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <div>
                                    <div class="text-sm text-gray-600">Order Completion Rate</div>
                                    <div class="text-2xl font-bold text-blue-600">{{ performanceMetrics?.order_completion_rate || 0 }}%</div>
                                </div>
                                <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <div>
                                    <div class="text-sm text-gray-600">Customer Satisfaction</div>
                                    <div class="text-2xl font-bold text-green-600">{{ performanceMetrics?.customer_satisfaction?.satisfaction_rate || 0 }}%</div>
                                </div>
                                <i class="fas fa-star text-green-600 text-2xl"></i>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                <div>
                                    <div class="text-sm text-gray-600">Average Rating</div>
                                    <div class="text-2xl font-bold text-purple-600">{{ performanceMetrics?.customer_satisfaction?.average_rating || 0 }}/5</div>
                                </div>
                                <i class="fas fa-thumbs-up text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue & Growth Trends -->
                    <div>
                        <h4 class="text-md font-semibold mb-4 text-gray-800">Revenue & Growth Trends</h4>
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="text-sm text-gray-600 mb-2">Today's Revenue</div>
                                <div class="text-xl font-bold text-green-600">${{ stats.revenue_today || 0 }}</div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="text-sm text-gray-600 mb-2">This Week's Revenue</div>
                                <div class="text-xl font-bold text-green-600">${{ stats.revenue_this_week || 0 }}</div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="text-sm text-gray-600 mb-2">Average Order Value</div>
                                <div class="text-xl font-bold text-blue-600">${{ stats.average_order_value ? Number(stats.average_order_value).toFixed(2) : '0.00' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popular Meals -->
                <div class="mt-6">
                    <h4 class="text-md font-semibold mb-4 text-gray-800">Top Performing Meals</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="meal in (performanceMetrics?.popular_meals || []).slice(0, 6)" :key="meal.id"
                             class="p-3 bg-gray-50 rounded-lg">
                            <div class="font-medium text-gray-800">{{ meal.name }}</div>
                            <div class="text-sm text-gray-600">{{ meal.order_items_count || 0 }} orders</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Admins -->
                <div class="card-responsive bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-users-cog text-purple-600 mr-2"></i>
                            Recent Admins
                        </h3>
                        <Link :href="route('superadmin.admins')"
                              class="text-sm text-blue-600 hover:text-blue-800">
                            View All
                        </Link>
                    </div>
                    <div class="space-y-3">
                        <div v-for="admin in recentAdmins" :key="admin.id"
                             class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium">{{ admin.name }}</div>
                                <div class="text-sm text-gray-600">{{ admin.email }}</div>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ new Date(admin.created_at).toLocaleDateString() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card-responsive bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-receipt text-green-600 mr-2"></i>
                            Recent Orders
                        </h3>
                        <Link :href="route('admin.orders.index')"
                              class="text-sm text-blue-600 hover:text-blue-800">
                            View All
                        </Link>
                    </div>
                    <div class="space-y-3">
                        <div v-for="order in recentOrders" :key="order.id"
                             class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium">Order #{{ order.id }}</div>
                                <div class="text-sm text-gray-600">${{ order.total_price }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-medium" :class="{
                                    'text-yellow-600': order.status === 'pending',
                                    'text-blue-600': order.status === 'confirmed',
                                    'text-orange-600': order.status === 'preparing',
                                    'text-green-600': order.status === 'ready',
                                    'text-purple-600': order.status === 'delivered'
                                }">
                                    {{ order.status }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ new Date(order.created_at).toLocaleDateString() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card-responsive bg-white">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                    Quick Actions
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Link :href="route('superadmin.admins.create')"
                          class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        <i class="fas fa-user-plus text-blue-600 text-2xl mb-2"></i>
                        <div class="font-medium">Create Admin</div>
                        <div class="text-sm text-gray-600">Add new administrator</div>
                    </Link>

                    <Link :href="route('superadmin.users')"
                          class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        <i class="fas fa-users text-green-600 text-2xl mb-2"></i>
                        <div class="font-medium">Manage Users</div>
                        <div class="text-sm text-gray-600">View all users</div>
                    </Link>

                    <Link :href="route('superadmin.analytics')"
                          class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        <i class="fas fa-chart-line text-purple-600 text-2xl mb-2"></i>
                        <div class="font-medium">View Analytics</div>
                        <div class="text-sm text-gray-600">System reports</div>
                    </Link>

                    <Link :href="route('superadmin.system.config')"
                          class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        <i class="fas fa-cogs text-orange-600 text-2xl mb-2"></i>
                        <div class="font-medium">System Config</div>
                        <div class="text-sm text-gray-600">Configuration</div>
                    </Link>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>
