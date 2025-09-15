<script setup>
import AdminLayout from '../Layout.vue';
import Card from '@/Components/UI/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ReportPreviewModal from '@/Components/UI/ReportPreviewModal.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const selectedPeriod = ref('monthly');
const selectedReport = ref('sales');
const startDate = ref('');
const endDate = ref('');
const selectedYear = ref(new Date().getFullYear());
const selectedMonth = ref(new Date().getMonth() + 1); // 1-12
const selectedWeek = ref(1); // 1-52
const selectedDay = ref(new Date().getDate()); // 1-31
const selectedFormat = ref('excel');
const isGenerating = ref(false);
const error = ref('');
const success = ref('');
const showPreviewModal = ref(false);
const previewData = ref(null);

const reportTypes = [
    {
        id: 'sales',
        name: 'Sales Report',
        description: 'Daily, weekly, and monthly sales analysis with revenue breakdown',
        icon: 'fas fa-chart-line',
        color: 'text-green-600',
        bgColor: 'bg-green-50',
    },
    {
        id: 'feedback',
        name: 'Customer Feedback Analysis',
        description: 'Rating distribution, comments analysis, and customer satisfaction metrics',
        icon: 'fas fa-star',
        color: 'text-yellow-600',
        bgColor: 'bg-yellow-50',
    },
    {
        id: 'inventory',
        name: 'Inventory & Availability',
        description: 'Meal availability tracking, popular items, and category breakdown',
        icon: 'fas fa-boxes',
        color: 'text-blue-600',
        bgColor: 'bg-blue-50',
    },
    {
        id: 'analytics',
        name: 'Comprehensive Analytics',
        description: 'Complete system overview with all metrics and insights',
        icon: 'fas fa-chart-pie',
        color: 'text-purple-600',
        bgColor: 'bg-purple-50',
    },
];

const periods = [
    { value: 'daily', label: 'Daily' },
    { value: 'weekly', label: 'Weekly' },
    { value: 'monthly', label: 'Monthly' },
    { value: 'yearly', label: 'Yearly' },
    { value: 'custom', label: 'Custom Date Range' },
];

const generateReport = (format = 'json') => {
    isGenerating.value = true;
    error.value = '';
    success.value = '';

    const params = {
        period: selectedPeriod.value,
        format: format,
        year: selectedYear.value,
    };

    // Add specific filters based on period
    if (selectedPeriod.value === 'monthly' || selectedPeriod.value === 'weekly' || selectedPeriod.value === 'daily') {
        params.month = selectedMonth.value;
    }
    if (selectedPeriod.value === 'weekly') {
        params.week = selectedWeek.value;
    }
    if (selectedPeriod.value === 'daily') {
        params.day = selectedDay.value;
    }

    if (selectedPeriod.value === 'custom' && startDate.value && endDate.value) {
        params.start_date = startDate.value;
        params.end_date = endDate.value;
    }

    const routeName = `admin.reports.${selectedReport.value}`;

    if (format === 'csv' || format === 'excel' || format === 'pdf') {
        try {
            // For file downloads, we need to make a direct request
            const url = new URL(route(routeName), window.location.origin);
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

            // Create a temporary link to trigger download
            const link = document.createElement('a');
            link.href = url.toString();

            // Set appropriate file extension
            const extension = format === 'excel' ? 'xlsx' : format;
            link.download = `${selectedReport.value}_report_${selectedPeriod.value}_${selectedYear.value}_${new Date().toISOString().split('T')[0]}.${extension}`;

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            const formatName = format.toUpperCase();
            success.value = `${formatName} report downloaded successfully!`;
            setTimeout(() => success.value = '', 5000);
        } catch (err) {
            error.value = 'Failed to download report. Please try again.';
            console.error('Download error:', err);
        } finally {
            isGenerating.value = false;
        }
    } else {
        // For JSON data (preview), use Inertia
        router.get(route(routeName), params, {
            onSuccess: (page) => {
                if (format === 'json') {
                    // The preview will be handled by the Preview page
                    // No need to show modal here as we're navigating to Preview page
                } else {
                    success.value = 'Report generated successfully!';
                    setTimeout(() => success.value = '', 5000);
                }
            },
            onError: (errors) => {
                error.value = 'Failed to generate report. Please try again.';
                console.error('Report generation error:', errors);
            },
            onFinish: () => {
                isGenerating.value = false;
            }
        });
    }
};

const showCustomDateRange = computed(() => selectedPeriod.value === 'custom');

// Dynamic filter options based on selected period
const monthOptions = computed(() => {
    const months = [
        { value: 1, label: 'January' },
        { value: 2, label: 'February' },
        { value: 3, label: 'March' },
        { value: 4, label: 'April' },
        { value: 5, label: 'May' },
        { value: 6, label: 'June' },
        { value: 7, label: 'July' },
        { value: 8, label: 'August' },
        { value: 9, label: 'September' },
        { value: 10, label: 'October' },
        { value: 11, label: 'November' },
        { value: 12, label: 'December' }
    ];
    return months;
});

const weekOptions = computed(() => {
    const weeks = [];
    for (let i = 1; i <= 52; i++) {
        weeks.push({ value: i, label: `Week ${i}` });
    }
    return weeks;
});

const dayOptions = computed(() => {
    const days = [];
    const daysInMonth = new Date(selectedYear.value, selectedMonth.value, 0).getDate();
    for (let i = 1; i <= daysInMonth; i++) {
        days.push({ value: i, label: `Day ${i}` });
    }
    return days;
});

// Show specific filters based on period
const showYearFilter = computed(() => ['yearly', 'monthly', 'weekly', 'daily'].includes(selectedPeriod.value));
const showMonthFilter = computed(() => ['monthly', 'weekly', 'daily'].includes(selectedPeriod.value));
const showWeekFilter = computed(() => selectedPeriod.value === 'weekly');
const showDayFilter = computed(() => selectedPeriod.value === 'daily');

// Helper functions for format dropdown
const getFormatVariant = (format) => {
    switch (format) {
        case 'csv': return 'success';
        case 'excel': return 'info';
        case 'pdf': return 'danger';
        default: return 'primary';
    }
};

const getFormatIcon = (format) => {
    switch (format) {
        case 'csv': return 'fas fa-file-csv';
        case 'excel': return 'fas fa-file-excel';
        case 'pdf': return 'fas fa-file-pdf';
        default: return 'fas fa-download';
    }
};

const getFormatLabel = (format) => {
    switch (format) {
        case 'csv': return 'CSV';
        case 'excel': return 'Excel';
        case 'pdf': return 'PDF';
        default: return 'File';
    }
};
</script>

<template>
    <AdminLayout>
        <div class="space-y-6 sm:space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Reports & Analytics</h1>
                    <p class="text-gray-600 mt-1">Generate comprehensive reports and download data</p>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <div v-if="success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ success }}
                </div>
            </div>
            <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ error }}
                </div>
            </div>

            <!-- Report Type Selection -->
            <Card title="Select Report Type" subtitle="Choose the type of report you want to generate">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="report in reportTypes"
                        :key="report.id"
                        @click="selectedReport = report.id"
                        :class="[
                            'p-4 border-2 rounded-lg cursor-pointer transition-all duration-200',
                            selectedReport === report.id
                                ? 'border-green-500 bg-green-50'
                                : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                        ]"
                    >
                        <div class="flex items-start gap-3">
                            <div :class="[report.bgColor, 'p-2 rounded-lg']">
                                <i :class="[report.icon, report.color, 'text-lg']"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">{{ report.name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ report.description }}</p>
                            </div>
                            <div v-if="selectedReport === report.id" class="text-green-600">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </Card>

            <!-- Report Configuration -->
            <Card title="Report Configuration" subtitle="Configure the time period and format for your report">
                <div class="space-y-6">
                    <!-- Time Period Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
                        <div class="relative">
                            <select
                                v-model="selectedPeriod"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none bg-white pr-8"
                            >
                                <option v-for="period in periods" :key="period.value" :value="period.value">
                                    {{ period.label }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Filters Based on Period -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Year Filter -->
                        <div v-if="showYearFilter">
                            <label for="selectedYear" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                            <div class="relative">
                                <select
                                    id="selectedYear"
                                    v-model="selectedYear"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none bg-white pr-8"
                                >
                                    <option v-for="year in Array.from({length: 10}, (_, i) => new Date().getFullYear() - i)" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Month Filter -->
                        <div v-if="showMonthFilter">
                            <label for="selectedMonth" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                            <div class="relative">
                                <select
                                    id="selectedMonth"
                                    v-model="selectedMonth"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none bg-white pr-8"
                                >
                                    <option v-for="month in monthOptions" :key="month.value" :value="month.value">
                                        {{ month.label }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Week Filter -->
                        <div v-if="showWeekFilter">
                            <label for="selectedWeek" class="block text-sm font-medium text-gray-700 mb-2">Week</label>
                            <div class="relative">
                                <select
                                    id="selectedWeek"
                                    v-model="selectedWeek"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none bg-white pr-8"
                                >
                                    <option v-for="week in weekOptions" :key="week.value" :value="week.value">
                                        {{ week.label }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Day Filter -->
                        <div v-if="showDayFilter">
                            <label for="selectedDay" class="block text-sm font-medium text-gray-700 mb-2">Day</label>
                            <div class="relative">
                                <select
                                    id="selectedDay"
                                    v-model="selectedDay"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none bg-white pr-8"
                                >
                                    <option v-for="day in dayOptions" :key="day.value" :value="day.value">
                                        {{ day.label }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Date Range -->
                    <div v-if="showCustomDateRange" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input
                                id="startDate"
                                v-model="startDate"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            />
                        </div>
                        <div>
                            <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input
                                id="endDate"
                                v-model="endDate"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-4">
                        <!-- Preview Button -->
                        <div>
                            <PrimaryButton
                                @click="generateReport('json')"
                                :disabled="isGenerating"
                                variant="primary"
                                size="lg"
                                icon="fas fa-eye"
                                class="w-full"
                            >
                                <span v-if="isGenerating">Generating Preview...</span>
                                <span v-else>Preview Report</span>
                            </PrimaryButton>
                        </div>

                        <!-- Download Options (only for Sales and Inventory reports) -->
                        <template v-if="selectedReport === 'sales' || selectedReport === 'inventory'">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Download Format</label>
                                <div class="relative">
                                    <select
                                        v-model="selectedFormat"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none bg-white pr-8"
                                    >
                                        <option value="csv">CSV - Raw Data</option>
                                        <option value="excel">Excel - Professional Report</option>
                                        <option value="pdf">PDF - Formatted Report</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <PrimaryButton
                                    @click="generateReport(selectedFormat)"
                                    :disabled="isGenerating"
                                    :variant="getFormatVariant(selectedFormat)"
                                    size="lg"
                                    :icon="getFormatIcon(selectedFormat)"
                                    class="w-full"
                                >
                                    <span v-if="isGenerating">Generating Download...</span>
                                    <span v-else>Download {{ getFormatLabel(selectedFormat) }}</span>
                                </PrimaryButton>
                            </div>
                        </template>

                        <!-- Info message for other reports -->
                        <div v-else class="text-sm text-gray-600 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle mr-3 mt-0.5 text-blue-500"></i>
                                <div>
                                    <p class="font-medium text-blue-800 dark:text-blue-200 mb-1">
                                        {{ selectedReport.charAt(0).toUpperCase() + selectedReport.slice(1) }} Reports
                                    </p>
                                    <p class="text-blue-700 dark:text-blue-300">
                                        These reports are available for preview only. Download options are available for Sales and Inventory reports.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Card>

            <!-- Quick Reports -->
            <Card title="Quick Reports" subtitle="Generate common reports with one click">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button
                        @click="selectedReport = 'sales'; selectedPeriod = 'daily'; selectedFormat = 'excel'; generateReport('excel')"
                        class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center"
                    >
                        <i class="fas fa-calendar-day text-green-600 text-2xl mb-2"></i>
                        <div class="font-medium">Today's Sales</div>
                        <div class="text-sm text-gray-600">Daily sales Excel</div>
                    </button>

                    <button
                        @click="selectedReport = 'sales'; selectedPeriod = 'weekly'; selectedFormat = 'excel'; generateReport('excel')"
                        class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center"
                    >
                        <i class="fas fa-calendar-week text-blue-600 text-2xl mb-2"></i>
                        <div class="font-medium">This Week's Sales</div>
                        <div class="text-sm text-gray-600">Week {{ selectedWeek }} Excel</div>
                    </button>

                    <button
                        @click="selectedReport = 'sales'; selectedPeriod = 'monthly'; selectedFormat = 'excel'; generateReport('excel')"
                        class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center"
                    >
                        <i class="fas fa-calendar-alt text-indigo-600 text-2xl mb-2"></i>
                        <div class="font-medium">This Month's Sales</div>
                        <div class="text-sm text-gray-600">{{ monthOptions.find(m => m.value === selectedMonth)?.label }} {{ selectedYear }} Excel</div>
                    </button>

                    <button
                        @click="selectedReport = 'inventory'; selectedFormat = 'excel'; generateReport('excel')"
                        class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center"
                    >
                        <i class="fas fa-boxes text-purple-600 text-2xl mb-2"></i>
                        <div class="font-medium">Inventory Status</div>
                        <div class="text-sm text-gray-600">Current Excel</div>
                    </button>
                </div>
            </Card>

            <!-- Report Information -->
            <Card title="Report Information" subtitle="Understanding your reports">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Available Report Types</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-chart-line text-green-600 mt-0.5"></i>
                                    <span><strong>Sales Report:</strong> Revenue, order counts, top-selling meals, and daily breakdowns</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-star text-yellow-600 mt-0.5"></i>
                                    <span><strong>Feedback Analysis:</strong> Rating distribution, customer comments, and satisfaction metrics</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-boxes text-blue-600 mt-0.5"></i>
                                    <span><strong>Inventory Report:</strong> Meal availability, popular items, and category performance</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-chart-pie text-purple-600 mt-0.5"></i>
                                    <span><strong>Analytics Report:</strong> Comprehensive overview of all system metrics</span>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Export Formats</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-eye text-gray-600 mt-0.5"></i>
                                    <span><strong>Preview:</strong> View report data in the browser (All reports)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-file-csv text-green-600 mt-0.5"></i>
                                    <span><strong>CSV:</strong> Download data for analysis (Sales & Inventory only)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-file-excel text-blue-600 mt-0.5"></i>
                                    <span><strong>Excel:</strong> Professional Excel reports with branding (Sales & Inventory only)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-file-pdf text-red-600 mt-0.5"></i>
                                    <span><strong>PDF:</strong> Formatted reports with restaurant branding (Sales & Inventory only)</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </Card>

            <!-- Report Preview Modal -->
            <ReportPreviewModal
                :show="showPreviewModal"
                :report-data="previewData"
                :report-type="selectedReport"
                :period="selectedPeriod"
                @close="showPreviewModal = false"
                @download="generateReport('csv')"
            />
        </div>
    </AdminLayout>
</template>
