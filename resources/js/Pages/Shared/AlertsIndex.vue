<script setup>
import AdminLayout from '../Admin/Layout.vue';
import StaffLayout from '../Staff/Layout.vue';
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    alerts: Object, // paginated
    user: Object,
    role: String, // 'admin' or 'staff'
});

const filterResolved = ref('all');
const filterUser = ref('');
const showModal = ref(false);
const selectedAlert = ref(null);
const staffResponse = ref('');

const LayoutComponent = computed(() => props.role === 'admin' ? AdminLayout : StaffLayout);

function applyFilters() {
    router.get(route('alerts.index'), {
        resolved: filterResolved.value !== 'all' ? filterResolved.value : undefined,
        user_id: filterUser.value || undefined,
    }, { preserveState: true, replace: true });
}
function openModal(alert) {
    selectedAlert.value = alert;
    staffResponse.value = '';
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    selectedAlert.value = null;
    staffResponse.value = '';
}
function submitResponse() {
    if (!staffResponse.value.trim()) return;
    router.patch(route('alerts.respond', selectedAlert.value.id), {
        staff_response: staffResponse.value
    }, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {},
    });
}
function resolveAlert() {
    router.patch(route('alerts.resolve', selectedAlert.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {},
    });
}
</script>

<template>
    <component :is="LayoutComponent" :user="user">
        <div class="max-w-6xl mx-auto p-4 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-primary-dark mb-4 sm:mb-6">Alerts Management</h1>
            <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4 mb-4 sm:mb-6">
                <div class="w-full sm:w-auto">
                    <label class="block text-xs sm:text-sm font-medium mb-1">Status</label>
                    <select v-model="filterResolved" @change="applyFilters" class="border rounded px-2 py-1 text-sm sm:text-base w-full sm:w-auto">
                        <option value="all">All</option>
                        <option value="0">Unresolved</option>
                        <option value="1">Resolved</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-xs sm:text-sm font-medium mb-1">User ID</label>
                    <input v-model="filterUser" @keyup.enter="applyFilters" type="text" class="border rounded px-2 py-1 text-sm sm:text-base w-full sm:w-auto" placeholder="User ID" />
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <!-- Desktop Table -->
                <div class="hidden sm:block">
                    <table class="table-responsive">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Order</th>
                                <th class="px-4 py-2 text-left">User</th>
                                <th class="px-4 py-2 text-left">Reason</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="alert in alerts.data" :key="alert.id" class="border-b hover:bg-yellow-50">
                                <td class="px-4 py-2 font-semibold">{{ alert.id }}</td>
                                <td class="px-4 py-2">#{{ alert.order_id }}</td>
                                <td class="px-4 py-2">{{ alert.user?.name || alert.user_id }}</td>
                                <td class="px-4 py-2">{{ alert.reason }}</td>
                                <td class="px-4 py-2">
                                    <span v-if="alert.resolved" class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Resolved</span>
                                    <span v-else class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Unresolved</span>
                                </td>
                                <td class="px-4 py-2">
                                    <button @click="openModal(alert)" class="btn-responsive bg-primary text-white rounded hover:bg-primary-dark">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="sm:hidden space-y-3 p-4">
                    <div v-for="alert in alerts.data" :key="alert.id" class="p-4 border rounded-lg bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-medium text-sm">Alert #{{ alert.id }}</span>
                            <span v-if="alert.resolved" class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Resolved</span>
                            <span v-else class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Unresolved</span>
                        </div>
                        <div class="text-sm text-gray-600 mb-1">Order: #{{ alert.order_id }}</div>
                        <div class="text-sm text-gray-600 mb-1">User: {{ alert.user?.name || alert.user_id }}</div>
                        <div class="text-sm text-gray-600 mb-3">Reason: {{ alert.reason }}</div>
                        <button @click="openModal(alert)" class="btn-responsive bg-primary text-white rounded hover:bg-primary-dark">View Details</button>
                    </div>
                </div>
            </div>
            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row justify-center items-center mt-6 gap-2">
                <button :disabled="!alerts.prev_page_url" @click="router.get(alerts.prev_page_url, {}, { preserveState: true, replace: true })" class="btn-responsive rounded border" :class="alerts.prev_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Prev</button>
                <span class="px-2 text-sm sm:text-base">Page {{ alerts.current_page }} of {{ alerts.last_page }}</span>
                <button :disabled="!alerts.next_page_url" @click="router.get(alerts.next_page_url, {}, { preserveState: true, replace: true })" class="btn-responsive rounded border" :class="alerts.next_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Next</button>
            </div>
            <!-- Alert Details Modal -->
            <Modal :show="showModal" @close="closeModal">
                <div class="p-6 max-w-lg mx-auto">
                    <h2 class="text-lg font-bold mb-2">Alert #{{ selectedAlert?.id }}</h2>
                    <div class="mb-2"><span class="font-medium">Order:</span> #{{ selectedAlert?.order_id }}</div>
                    <div class="mb-2"><span class="font-medium">User:</span> {{ selectedAlert?.user?.name || selectedAlert?.user_id }}</div>
                    <div class="mb-2"><span class="font-medium">Reason:</span> {{ selectedAlert?.reason }}</div>
                    <div class="mb-2"><span class="font-medium">Status:</span>
                        <span v-if="selectedAlert?.resolved" class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Resolved</span>
                        <span v-else class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Unresolved</span>
                    </div>
                    <div v-if="selectedAlert?.staff_response" class="mb-2">
                        <span class="font-medium">Response:</span> {{ selectedAlert?.staff_response }}
                        <span v-if="selectedAlert?.responder_name" class="ml-2 text-xs text-gray-500">({{ selectedAlert?.responder_name }} - {{ selectedAlert?.responder_role }})</span>
                    </div>
                    <div v-if="!selectedAlert?.resolved" class="mt-4 flex gap-2">
                        <input v-model="staffResponse" type="text" class="flex-1 border rounded px-2 py-1" placeholder="Write a response..." />
                        <button @click="submitResponse" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark">Send</button>
                        <button v-if="role === 'admin'" @click="resolveAlert" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Mark Resolved</button>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button @click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
                    </div>
                </div>
            </Modal>
        </div>
    </component>
</template>
