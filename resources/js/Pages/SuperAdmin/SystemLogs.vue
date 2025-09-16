<script setup>
import SuperAdminLayout from './Layout.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    logs: Array
});

const selectedLevel = ref('all');
const searchTerm = ref('');

const filteredLogs = computed(() => {
    let filtered = props.logs || [];

    if (selectedLevel.value !== 'all') {
        filtered = filtered.filter(log => log.level === selectedLevel.value);
    }

    if (searchTerm.value) {
        filtered = filtered.filter(log =>
            log.message.toLowerCase().includes(searchTerm.value.toLowerCase())
        );
    }

    return filtered;
});
</script>

<template>
    <SuperAdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-responsive-lg font-bold text-gray-800">System Logs</h2>
                <div class="flex gap-2">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-download mr-2"></i>Export Logs
                    </button>
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-sync mr-2"></i>Refresh
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="level-filter" class="block text-sm font-medium text-gray-700 mb-2">
                            Filter by Level
                        </label>
                        <select id="level-filter" v-model="selectedLevel"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Levels</option>
                            <option value="error">Error</option>
                            <option value="warning">Warning</option>
                            <option value="info">Info</option>
                            <option value="debug">Debug</option>
                        </select>
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            Search Messages
                        </label>
                        <input id="search" v-model="searchTerm" type="text"
                               placeholder="Search log messages..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Log Statistics -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-red-600">
                                {{ props.logs?.filter(log => log.level === 'error').length || 0 }}
                            </div>
                            <div class="text-sm text-gray-600">Errors</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ props.logs?.filter(log => log.level === 'warning').length || 0 }}
                            </div>
                            <div class="text-sm text-gray-600">Warnings</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ props.logs?.filter(log => log.level === 'info').length || 0 }}
                            </div>
                            <div class="text-sm text-gray-600">Info</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-bug text-gray-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-600">
                                {{ props.logs?.filter(log => log.level === 'debug').length || 0 }}
                            </div>
                            <div class="text-sm text-gray-600">Debug</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">System Logs</h3>
                    <p class="text-sm text-gray-600 mt-1">Real-time system activity and error logs</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Level
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Message
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(log, index) in filteredLogs" :key="index" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <StatusBadge
                                        :status="log.level.toUpperCase()"
                                        :variant="log.level"
                                        size="sm"
                                    />
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-md">
                                        {{ log.message }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ new Date(log.time).toLocaleString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="filteredLogs.length === 0" class="text-center py-12">
                    <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No logs found</h3>
                    <p class="text-gray-500">No system logs match your current filters.</p>
                </div>
            </div>

            <!-- Log Management -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-cogs text-gray-600 mr-2"></i>
                        Log Management
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                            <i class="fas fa-trash text-red-600 text-2xl mb-2"></i>
                            <div class="font-medium">Clear Old Logs</div>
                            <div class="text-sm text-gray-600">Remove logs older than 30 days</div>
                        </button>

                        <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                            <i class="fas fa-download text-blue-600 text-2xl mb-2"></i>
                            <div class="font-medium">Export All Logs</div>
                            <div class="text-sm text-gray-600">Download complete log file</div>
                        </button>

                        <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                            <i class="fas fa-cog text-green-600 text-2xl mb-2"></i>
                            <div class="font-medium">Log Settings</div>
                            <div class="text-sm text-gray-600">Configure logging levels</div>
                        </button>

                        <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                            <i class="fas fa-bell text-yellow-600 text-2xl mb-2"></i>
                            <div class="font-medium">Alert Settings</div>
                            <div class="text-sm text-gray-600">Set up log alerts</div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Log Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Log Information</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>System logs are automatically generated and stored for debugging and monitoring purposes.
                            Logs are rotated daily and old logs are automatically archived.
                            Critical errors are monitored and can trigger alerts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>
