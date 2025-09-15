<script setup>
import { computed } from 'vue';
import MetricCard from './MetricCard.vue';
import StatusBadge from './StatusBadge.vue';
import Card from './Card.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    reportData: {
        type: Object,
        default: () => ({}),
    },
    reportType: {
        type: String,
        default: 'sales',
    },
    period: {
        type: String,
        default: 'monthly',
    },
    year: {
        type: [Number, String],
        default: null,
    },
});

const emit = defineEmits(['close']);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-TZ', {
        style: 'currency',
        currency: 'TZS',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const formatDateTime = (dateString) => {
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getReportTitle = computed(() => {
    const typeMap = {
        sales: 'Sales Report',
        feedback: 'Customer Feedback Report',
        inventory: 'Inventory Report',
        analytics: 'Comprehensive Analytics Report',
    };
    return typeMap[props.reportType] || 'Report';
});

const getPeriodLabel = computed(() => {
    const periodMap = {
        daily: 'Daily',
        weekly: 'Weekly',
        monthly: 'Monthly',
        yearly: 'Yearly',
        custom: 'Custom Range',
    };
    return periodMap[props.period] || 'Period';
});
</script>

<template>
    <!-- Custom wide modal for report preview -->
    <Transition name="fade">
        <div v-if="show" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg z-10 w-full max-w-[95vw] max-h-[95vh] overflow-hidden">
                <div class="p-6 max-h-[95vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ getReportTitle }} - {{ getPeriodLabel }}
                        <span v-if="year" class="text-lg font-normal text-gray-600 dark:text-gray-300">({{ year }})</span>
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">
                        Generated on {{ new Date().toLocaleDateString() }} at {{ new Date().toLocaleTimeString() }}
                    </p>
                </div>
                <button
                    @click="$emit('close')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Sales Report -->
            <div v-if="reportType === 'sales' && reportData.summary" class="space-y-6">
                <!-- Summary Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <MetricCard
                        title="Total Revenue"
                        :value="formatCurrency(reportData.summary.total_revenue)"
                        icon="fas fa-dollar-sign"
                        color="green"
                    />
                    <MetricCard
                        title="Total Orders"
                        :value="reportData.summary.total_orders.toString()"
                        icon="fas fa-receipt"
                        color="blue"
                    />
                    <MetricCard
                        title="Total Items Sold"
                        :value="(reportData.summary.total_items_sold || 0).toString()"
                        icon="fas fa-boxes"
                        color="indigo"
                    />
                    <MetricCard
                        title="Average Order Value"
                        :value="formatCurrency(reportData.summary.average_order_value)"
                        icon="fas fa-chart-line"
                        color="purple"
                    />
                    <MetricCard
                        title="Cancelled Orders"
                        :value="(reportData.summary.cancelled_orders || 0).toString()"
                        icon="fas fa-times-circle"
                        color="red"
                    />
                    <MetricCard
                        title="Refunded Amount"
                        :value="formatCurrency(reportData.summary.refunded_amount || 0)"
                        icon="fas fa-undo"
                        color="orange"
                    />
                </div>

                <!-- Daily Breakdown -->
                <Card v-if="reportData.daily_breakdown" title="Daily Breakdown">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Orders
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Items Sold
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Revenue
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Avg Order Value
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Top Meal
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Cancelled
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="day in reportData.daily_breakdown" :key="day.date">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ formatDate(day.date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ day.total_orders }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ day.total_items_sold || 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ formatCurrency(day.total_revenue) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ formatCurrency(day.average_order_value) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ day.top_meal || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <span v-if="day.cancelled_orders > 0" class="text-red-600 dark:text-red-400">
                                            {{ day.cancelled_orders }}
                                        </span>
                                        <span v-else class="text-green-600 dark:text-green-400">0</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </Card>

                <!-- Top Selling Meals -->
                <Card v-if="reportData.top_meals" title="Top Selling Meals">
                    <div class="space-y-3">
                        <div
                            v-for="(meal, index) in reportData.top_meals"
                            :key="meal.name"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                        >
                            <div class="flex items-center">
                                <span class="w-8 h-8 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                    {{ index + 1 }}
                                </span>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ meal.name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ meal.quantity }} sold</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(meal.revenue) }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ meal.percentage }}% of total</p>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Payment Methods Breakdown -->
                <Card v-if="reportData.payment_methods" title="Payment Methods Breakdown">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div
                            v-for="method in reportData.payment_methods"
                            :key="method.method"
                            class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ method.method }}</h3>
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ method.percentage }}%</span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Orders:</span> {{ method.count }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Amount:</span> {{ formatCurrency(method.amount) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Staff Performance -->
                <Card v-if="reportData.staff_performance" title="Staff Performance (Top 5)">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Staff Member
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Orders Processed
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Sales
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="staff in reportData.staff_performance" :key="staff.staff_name">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ staff.staff_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ staff.orders_processed }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ formatCurrency(staff.total_sales) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </Card>

                <!-- Peak Hours -->
                <Card v-if="reportData.peak_hours" title="Peak Sales Hours (Top 5)">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="hour in reportData.peak_hours"
                            :key="hour.hour"
                            class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ hour.hour }}</h3>
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ hour.orders }} orders</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-medium">Revenue:</span> {{ formatCurrency(hour.revenue) }}
                            </p>
                        </div>
                    </div>
                </Card>

                <!-- Category Breakdown -->
                <Card v-if="reportData.category_breakdown" title="Category Performance">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Orders
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Items Sold
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Revenue
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="category in reportData.category_breakdown" :key="category.category">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ category.category }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ category.orders }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ category.items_sold }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ formatCurrency(category.revenue) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </Card>
            </div>

            <!-- Feedback Report -->
            <div v-if="reportType === 'feedback' && reportData.summary" class="space-y-6">
                <!-- Summary Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <MetricCard
                        title="Total Ratings"
                        :value="reportData.summary.total_ratings.toString()"
                        icon="fas fa-star"
                        color="yellow"
                    />
                    <MetricCard
                        title="Average Rating"
                        :value="reportData.summary.average_rating.toFixed(1)"
                        icon="fas fa-chart-bar"
                        color="blue"
                    />
                    <MetricCard
                        title="Positive Feedback"
                        :value="reportData.summary.positive_feedback.toString()"
                        icon="fas fa-thumbs-up"
                        color="green"
                    />
                    <MetricCard
                        title="Negative Feedback"
                        :value="reportData.summary.negative_feedback.toString()"
                        icon="fas fa-thumbs-down"
                        color="red"
                    />
                </div>

                <!-- Rating Distribution -->
                <Card v-if="reportData.distribution" title="Rating Distribution">
                    <div class="space-y-3">
                        <div
                            v-for="rating in reportData.distribution"
                            :key="rating.rating"
                            class="flex items-center justify-between"
                        >
                            <div class="flex items-center">
                                <span class="w-8 text-center">{{ rating.rating }}★</span>
                                <div class="ml-4 flex-1">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div
                                            class="bg-yellow-400 h-2 rounded-full"
                                            :style="{ width: rating.percentage + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ rating.count }} ({{ rating.percentage }}%)
                                </span>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Recent Comments -->
                <Card v-if="reportData.recent_comments" title="Recent Customer Comments">
                    <div class="space-y-4">
                        <div
                            v-for="comment in reportData.recent_comments"
                            :key="comment.id"
                            class="border-l-4 border-green-500 pl-4 py-2"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400 mr-2">
                                        <i
                                            v-for="star in 5"
                                            :key="star"
                                            :class="star <= comment.rating ? 'fas fa-star' : 'far fa-star'"
                                        ></i>
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ comment.user }}</span>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ formatDateTime(comment.date) }}
                                </span>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300">{{ comment.comment }}</p>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Inventory Report -->
            <div v-if="reportType === 'inventory' && reportData.summary" class="space-y-6">
                <!-- Summary Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <MetricCard
                        title="Total Meals"
                        :value="reportData.summary.total_meals.toString()"
                        icon="fas fa-utensils"
                        color="blue"
                    />
                    <MetricCard
                        title="Available Meals"
                        :value="reportData.summary.available_meals.toString()"
                        icon="fas fa-check-circle"
                        color="green"
                    />
                    <MetricCard
                        title="Unavailable Meals"
                        :value="reportData.summary.unavailable_meals.toString()"
                        icon="fas fa-times-circle"
                        color="red"
                    />
                    <MetricCard
                        title="Availability Rate"
                        :value="reportData.summary.availability_percentage.toFixed(1) + '%'"
                        icon="fas fa-percentage"
                        color="purple"
                    />
                </div>

                <!-- Category Breakdown -->
                <Card v-if="reportData.category_breakdown" title="Category Breakdown">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Meals</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Available</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unavailable</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Availability %</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="category in reportData.category_breakdown" :key="category.category">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ category.category }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ category.total_meals }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <span class="text-green-600 dark:text-green-400">{{ category.available_meals }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        <span class="text-red-600 dark:text-red-400">{{ category.unavailable_meals }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ category.total_meals > 0 ? ((category.available_meals / category.total_meals) * 100).toFixed(1) : 0 }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </Card>

                <!-- Popular Meals -->
                <Card v-if="reportData.popular_meals" title="Popular Meals (Top 20)">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="meal in reportData.popular_meals"
                            :key="meal.id"
                            class="p-4 border rounded-lg"
                            :class="{
                                'border-green-200 bg-green-50 dark:bg-green-900/20': meal.is_available,
                                'border-red-200 bg-red-50 dark:bg-red-900/20': !meal.is_available
                            }"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ meal.name }}</h3>
                                <StatusBadge
                                    :status="meal.is_available ? 'Available' : 'Unavailable'"
                                    :variant="meal.is_available ? 'success' : 'danger'"
                                />
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ meal.category }}</p>
                            <div class="text-sm space-y-1">
                                <p class="text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Price:</span> {{ formatCurrency(meal.price) }}
                                </p>
                                <p class="text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Orders:</span> {{ meal.order_count }}
                                </p>
                                <p class="text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Revenue:</span> {{ formatCurrency(meal.revenue) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Analytics Report -->
            <div v-if="reportType === 'analytics' && reportData" class="space-y-6">
                <!-- Sales Analytics -->
                <Card v-if="reportData.sales" title="Sales Analytics">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <MetricCard
                            title="Total Revenue"
                            :value="formatCurrency(reportData.sales.summary?.total_revenue || 0)"
                            icon="fas fa-dollar-sign"
                            color="green"
                        />
                        <MetricCard
                            title="Total Orders"
                            :value="(reportData.sales.summary?.total_orders || 0).toString()"
                            icon="fas fa-receipt"
                            color="blue"
                        />
                        <MetricCard
                            title="Average Order Value"
                            :value="formatCurrency(reportData.sales.summary?.average_order_value || 0)"
                            icon="fas fa-chart-line"
                            color="purple"
                        />
                    </div>
                </Card>

                <!-- Customer Feedback Analytics -->
                <Card v-if="reportData.feedback" title="Customer Feedback Analytics">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <MetricCard
                            title="Total Ratings"
                            :value="(reportData.feedback.summary?.total_ratings || 0).toString()"
                            icon="fas fa-star"
                            color="yellow"
                        />
                        <MetricCard
                            title="Average Rating"
                            :value="(reportData.feedback.summary?.average_rating || 0).toFixed(1)"
                            icon="fas fa-chart-bar"
                            color="blue"
                        />
                        <MetricCard
                            title="Positive Feedback"
                            :value="(reportData.feedback.summary?.positive_feedback || 0).toString()"
                            icon="fas fa-thumbs-up"
                            color="green"
                        />
                        <MetricCard
                            title="Negative Feedback"
                            :value="(reportData.feedback.summary?.negative_feedback || 0).toString()"
                            icon="fas fa-thumbs-down"
                            color="red"
                        />
                    </div>
                </Card>

                <!-- Inventory Analytics -->
                <Card v-if="reportData.inventory" title="Inventory Analytics">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <MetricCard
                            title="Total Meals"
                            :value="(reportData.inventory.summary?.total_meals || 0).toString()"
                            icon="fas fa-utensils"
                            color="blue"
                        />
                        <MetricCard
                            title="Available Meals"
                            :value="(reportData.inventory.summary?.available_meals || 0).toString()"
                            icon="fas fa-check-circle"
                            color="green"
                        />
                        <MetricCard
                            title="Availability Rate"
                            :value="(reportData.inventory.summary?.availability_percentage || 0).toFixed(1) + '%'"
                            icon="fas fa-percentage"
                            color="purple"
                        />
                    </div>
                </Card>

                <!-- User Analytics -->
                <Card v-if="reportData.users" title="User Analytics">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <MetricCard
                            title="Total Users"
                            :value="(reportData.users.summary?.total_users || 0).toString()"
                            icon="fas fa-users"
                            color="blue"
                        />
                        <MetricCard
                            title="New Users This Month"
                            :value="(reportData.users.summary?.new_users_this_month || 0).toString()"
                            icon="fas fa-user-plus"
                            color="green"
                        />
                        <MetricCard
                            title="Active Users"
                            :value="(reportData.users.summary?.active_users || 0).toString()"
                            icon="fas fa-user-check"
                            color="purple"
                        />
                    </div>
                </Card>

                <!-- Order Analytics -->
                <Card v-if="reportData.orders" title="Order Analytics">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <MetricCard
                            title="Total Orders"
                            :value="(reportData.orders.summary?.total_orders || 0).toString()"
                            icon="fas fa-shopping-cart"
                            color="blue"
                        />
                        <MetricCard
                            title="Pending Orders"
                            :value="(reportData.orders.summary?.pending_orders || 0).toString()"
                            icon="fas fa-clock"
                            color="yellow"
                        />
                        <MetricCard
                            title="Completed Orders"
                            :value="(reportData.orders.summary?.completed_orders || 0).toString()"
                            icon="fas fa-check-circle"
                            color="green"
                        />
                        <MetricCard
                            title="Cancelled Orders"
                            :value="(reportData.orders.summary?.cancelled_orders || 0).toString()"
                            icon="fas fa-times-circle"
                            color="red"
                        />
                    </div>
                </Card>
            </div>

            <!-- No Data Message -->
            <div v-if="!reportData || Object.keys(reportData).length === 0" class="text-center py-12">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8">
                    <i class="fas fa-chart-bar text-6xl text-gray-400 mb-6"></i>
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-3">No Data Available</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        No data found for the selected period. This could mean:
                    </p>
                    <ul class="text-left text-gray-600 dark:text-gray-300 space-y-2 max-w-md mx-auto">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            No orders were placed in this time period
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            All orders are still pending (not delivered)
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Try selecting a different time range
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                    Close
                </button>
                <button
                    @click="$emit('download')"
                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                    <i class="fas fa-download mr-2"></i>
                    Download CSV
                </button>
            </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
