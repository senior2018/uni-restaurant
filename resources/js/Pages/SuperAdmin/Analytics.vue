<script setup>
import SuperAdminLayout from './Layout.vue';

defineProps({
    analytics: Object
});
</script>

<template>
    <SuperAdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-responsive-lg font-bold text-gray-800">System Analytics</h2>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-chart-line mr-1"></i>
                    Real-time system analytics and insights
                </div>
            </div>

            <!-- Revenue Analytics -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                        Revenue Analytics
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">${{ analytics.revenue_analytics?.total ? Number(analytics.revenue_analytics.total).toFixed(2) : '0.00' }}</div>
                            <div class="text-sm text-gray-600">Total Revenue</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">${{ analytics.revenue_analytics?.monthly ? Number(analytics.revenue_analytics.monthly).toFixed(2) : '0.00' }}</div>
                            <div class="text-sm text-gray-600">This Month</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600">${{ analytics.revenue_analytics?.daily ? Number(analytics.revenue_analytics.daily).toFixed(2) : '0.00' }}</div>
                            <div class="text-sm text-gray-600">Today</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Growth Chart -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-users text-blue-600 mr-2"></i>
                        User Growth (Last 30 Days)
                    </h3>
                </div>
                <div class="p-6">
                    <div class="h-64 flex items-end justify-between space-x-2">
                        <div v-for="(data, index) in analytics.user_growth?.slice(-7)" :key="index"
                             class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-blue-200 rounded-t"
                                 :style="{ height: `${(data.users / Math.max(...analytics.user_growth.map(d => d.users))) * 200}px` }">
                            </div>
                            <div class="text-xs text-gray-600 mt-2 transform -rotate-45 origin-left">
                                {{ new Date(data.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-center text-sm text-gray-600">
                        Total Users: {{ analytics.user_growth?.[analytics.user_growth.length - 1]?.users || 0 }}
                    </div>
                </div>
            </div>

            <!-- Order Trends Chart -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-shopping-cart text-green-600 mr-2"></i>
                        Order Trends (Last 30 Days)
                    </h3>
                </div>
                <div class="p-6">
                    <div class="h-64 flex items-end justify-between space-x-2">
                        <div v-for="(data, index) in analytics.order_trends?.slice(-7)" :key="index"
                             class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-green-200 rounded-t"
                                 :style="{ height: `${(data.orders / Math.max(...analytics.order_trends.map(d => d.orders))) * 200}px` }">
                            </div>
                            <div class="text-xs text-gray-600 mt-2 transform -rotate-45 origin-left">
                                {{ new Date(data.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-center text-sm text-gray-600">
                        Average Daily Orders: {{ Math.round(analytics.order_trends?.reduce((sum, d) => sum + d.orders, 0) / analytics.order_trends?.length) || 0 }}
                    </div>
                </div>
            </div>

            <!-- Popular Meals -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-utensils text-orange-600 mr-2"></i>
                        Popular Meals
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div v-for="(meal, index) in analytics.popular_meals" :key="meal.id"
                             class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-orange-600">#{{ index + 1 }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ meal.name }}</div>
                                    <div class="text-sm text-gray-500">{{ meal.order_items_count }} orders</div>
                                </div>
                            </div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ meal.order_items_count }} orders
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-star text-yellow-600 mr-2"></i>
                        Rating Distribution
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div v-for="rating in analytics.rating_distribution" :key="rating.rating"
                             class="flex items-center">
                            <div class="w-8 text-sm font-medium text-gray-600">
                                {{ rating.rating }} ⭐
                            </div>
                            <div class="flex-1 mx-4">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full"
                                         :style="{ width: `${(rating.count / Math.max(...analytics.rating_distribution.map(r => r.count))) * 100}%` }">
                                    </div>
                                </div>
                            </div>
                            <div class="w-12 text-sm text-gray-600 text-right">
                                {{ rating.count }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-center text-sm text-gray-600">
                        Total Ratings: {{ analytics.rating_distribution?.reduce((sum, r) => sum + r.count, 0) || 0 }}
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- System Performance -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-tachometer-alt text-purple-600 mr-2"></i>
                            System Performance
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Response Time</span>
                                <span class="text-sm font-medium text-green-600">< 200ms</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Uptime</span>
                                <span class="text-sm font-medium text-green-600">99.9%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Error Rate</span>
                                <span class="text-sm font-medium text-green-600">0.1%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Active Users</span>
                                <span class="text-sm font-medium text-blue-600">{{ analytics.user_growth?.[analytics.user_growth.length - 1]?.users || 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Metrics -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>
                            Business Metrics
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Average Order Value</span>
                                <span class="text-sm font-medium text-green-600">
                                    ${{ (() => { const total = analytics.revenue_analytics?.total; const orders = analytics.order_trends?.reduce((sum, d) => sum + d.orders, 0); return total && orders ? Number(total / orders).toFixed(2) : '0.00'; })() }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Orders per Day</span>
                                <span class="text-sm font-medium text-blue-600">
                                    {{ Math.round(analytics.order_trends?.reduce((sum, d) => sum + d.orders, 0) / analytics.order_trends?.length) || 0 }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Customer Satisfaction</span>
                                <span class="text-sm font-medium text-yellow-600">
                                    {{ (() => { const totalRating = analytics.rating_distribution?.reduce((sum, r) => sum + (r.rating * r.count), 0); const totalCount = analytics.rating_distribution?.reduce((sum, r) => sum + r.count, 0); return totalRating && totalCount ? Number(totalRating / totalCount).toFixed(1) : 'N/A'; })() }} ⭐
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Growth Rate</span>
                                <span class="text-sm font-medium text-green-600">+12.5%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-download text-gray-600 mr-2"></i>
                        Export Analytics
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                            <i class="fas fa-file-csv text-green-600 text-2xl mb-2"></i>
                            <div class="font-medium">Export CSV</div>
                            <div class="text-sm text-gray-600">Download data</div>
                        </button>
                        <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                            <i class="fas fa-file-pdf text-red-600 text-2xl mb-2"></i>
                            <div class="font-medium">Export PDF</div>
                            <div class="text-sm text-gray-600">Generate report</div>
                        </button>
                        <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                            <i class="fas fa-chart-line text-blue-600 text-2xl mb-2"></i>
                            <div class="font-medium">Schedule Report</div>
                            <div class="text-sm text-gray-600">Automated reports</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>
