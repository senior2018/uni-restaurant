<script setup>
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import ReportPreviewModal from '@/Components/UI/ReportPreviewModal.vue';

const props = defineProps({
    reportData: Object,
    reportType: String,
    period: String,
    year: Number,
});

// Automatically show the modal when the page loads
onMounted(() => {
    // The modal will be shown by the parent component
    // This page just provides the data
});

// Function to close and go back
const closePreview = () => {
    router.visit(route('admin.reports.index'), {
        method: 'get',
        preserveState: true,
        preserveScroll: true,
    });
};

// Function to download the report
const downloadReport = (format = 'csv') => {
    const params = {
        period: props.period,
        format: format,
    };

    if (props.year) {
        params.year = props.year;
    }

    const routeName = `admin.reports.${props.reportType}`;

    // Create download link
    const url = new URL(route(routeName), window.location.origin);
    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

    const link = document.createElement('a');
    link.href = url.toString();

    // Set appropriate file extension
    const extension = format === 'excel' ? 'xlsx' : format;
    link.download = `${props.reportType}_report_${props.period}_${new Date().toISOString().split('T')[0]}.${extension}`;

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Report Preview</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">
                            {{ reportType.charAt(0).toUpperCase() + reportType.slice(1) }} Report - {{ period.charAt(0).toUpperCase() + period.slice(1) }}
                            <span v-if="year"> ({{ year }})</span>
                        </p>
                    </div>
                    <button
                        @click="closePreview"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Reports
                    </button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Show the preview modal -->
            <ReportPreviewModal
                :show="true"
                :report-data="reportData"
                :report-type="reportType"
                :period="period"
                :year="year"
                @close="closePreview"
                @download="downloadReport"
            />
        </div>
    </div>
</template>
