<script setup>
import AdminLayout from './Layout.vue';
import Modal from '@/Components/Modal.vue';
import { ref, nextTick } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    alerts: Object, // paginated
    user: Object,
    role: String, // 'admin'
});

const filterResolved = ref('all');
const filterUser = ref('');
const showModal = ref(false);
const selectedAlert = ref(null);
const staffResponse = ref('');
const bulkMode = ref(false);
const selectedAlerts = ref([]);
const deleting = ref(false);
const feedback = ref("");

function applyFilters() {
    router.get(route('admin.alerts.index'), {
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
    router.patch(route('admin.alerts.respond', selectedAlert.value.id), {
        staff_response: staffResponse.value
    }, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {},
    });
}
function resolveAlert() {
    router.patch(route('admin.alerts.resolve', selectedAlert.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            router.reload({ only: ['unresolvedAlertCount', 'alerts'] });
        },
        onError: () => {},
    });
}
function deleteAlert(alert) {
    if (confirm('Are you sure you want to delete this alert?')) {
        router.delete(route('admin.alerts.destroy', alert.id), {
            preserveScroll: true,
            onSuccess: () => { expandedAlertId.value = null; },
            onError: () => {},
        });
    }
}
function toggleBulkMode() {
    bulkMode.value = !bulkMode.value;
    if (!bulkMode.value) selectedAlerts.value = [];
}
function toggleSelectAlert(alertId) {
    if (selectedAlerts.value.includes(alertId)) {
        selectedAlerts.value = selectedAlerts.value.filter(id => id !== alertId);
    } else {
        selectedAlerts.value.push(alertId);
    }
}
function selectAllAlerts() {
    selectedAlerts.value = props.alerts.data.map(a => a.id);
}
function deselectAllAlerts() {
    selectedAlerts.value = [];
}
async function deleteSelectedAlerts() {
    if (selectedAlerts.value.length === 0) return;
    if (confirm('Are you sure you want to delete the selected alerts?')) {
        deleting.value = true;
        router.delete(route('admin.alerts.bulkDestroy'), {
            data: { ids: selectedAlerts.value },
            preserveScroll: true,
            onSuccess: () => {
                selectedAlerts.value = [];
                bulkMode.value = false;
                feedback.value = 'Selected alerts deleted.';
                deleting.value = false;
                nextTick(() => setTimeout(() => feedback.value = '', 3000));
            },
            onError: () => { deleting.value = false; },
        });
    }
}
</script>

<template>
    <AdminLayout :user="user">
        <div class="max-w-6xl mx-auto p-responsive">
            <h1 class="text-responsive-lg font-bold text-primary-dark mb-6">Alerts Management</h1>
            <div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-4 lg:justify-between">
                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select v-model="filterResolved" @change="applyFilters" class="border rounded px-2 py-1 w-full">
                            <option value="all">All</option>
                            <option value="0">Unresolved</option>
                            <option value="1">Resolved</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-medium mb-1">User ID</label>
                        <input v-model="filterUser" @keyup.enter="applyFilters" type="text" class="border rounded px-2 py-1 w-full" placeholder="User ID" />
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button v-if="!bulkMode" @click="toggleBulkMode" class="btn-responsive bg-red-600 text-white rounded hover:bg-red-700">Clear</button>
                    <div v-else class="flex flex-col sm:flex-row items-center gap-2">
                        <div class="flex gap-2">
                            <button @click="selectAllAlerts" class="px-2 py-1 bg-gray-200 rounded text-sm">Select All</button>
                            <button @click="deselectAllAlerts" class="px-2 py-1 bg-gray-200 rounded text-sm">Deselect All</button>
                        </div>
                        <button :disabled="selectedAlerts.length === 0 || deleting" @click="deleteSelectedAlerts" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 flex items-center gap-2 text-sm">
                            <span v-if="deleting" class="loader border-white border-2 border-t-transparent rounded-full w-4 h-4 animate-spin"></span>
                            Delete Selected
                        </button>
                        <button @click="toggleBulkMode" class="px-2 py-1 bg-gray-200 rounded text-sm">Cancel</button>
                    </div>
                </div>
            </div>
            <div v-if="feedback" class="fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow z-50">{{ feedback }}</div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th v-if="bulkMode" class="px-2 py-2"></th>
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
                            <td v-if="bulkMode" class="px-2 py-2">
                                <input type="checkbox" :checked="selectedAlerts.includes(alert.id)" @change="toggleSelectAlert(alert.id)" />
                            </td>
                            <td class="px-4 py-2 font-semibold">{{ alert.id }}</td>
                            <td class="px-4 py-2">#{{ alert.order_id }}</td>
                            <td class="px-4 py-2">{{ alert.user?.name || alert.user_id }}</td>
                            <td class="px-4 py-2">{{ alert.reason }}</td>
                            <td class="px-4 py-2">
                                <span v-if="alert.resolved" class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Resolved</span>
                                <span v-else class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Unresolved</span>
                            </td>
                            <td class="px-4 py-2">
                                <button @click="openModal(alert)" class="px-3 py-1 bg-primary text-white rounded hover:bg-primary-dark">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="flex justify-center mt-6 gap-2">
                <button :disabled="!alerts.prev_page_url" @click="router.get(alerts.prev_page_url, {}, { preserveState: true, replace: true })" class="px-3 py-1 rounded border" :class="alerts.prev_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Prev</button>
                <span class="px-2">Page {{ alerts.current_page }} of {{ alerts.last_page }}</span>
                <button :disabled="!alerts.next_page_url" @click="router.get(alerts.next_page_url, {}, { preserveState: true, replace: true })" class="px-3 py-1 rounded border" :class="alerts.next_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Next</button>
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
                    <div v-if="!selectedAlert?.resolved" class="mt-4 flex flex-col items-end">
                        <input v-model="staffResponse" type="text" class="flex-1 border rounded px-2 py-1 mb-2 w-full" placeholder="Write a response..." />
                        <div class="flex gap-2">
                            <button @click="submitResponse" class="px-3 py-1 text-xs bg-primary text-white rounded hover:bg-primary-dark">Send</button>
                            <button v-if="role === 'admin'" @click="resolveAlert" class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">Mark Resolved</button>
                            <button v-if="role === 'admin'" @click="deleteAlert(selectedAlert)" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                            <button @click="closeModal" class="px-3 py-1 text-xs bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Close</button>
                        </div>
                    </div>
                </div>
            </Modal>
        </div>
    </AdminLayout>
</template>

<style scoped>
.loader {
    border-width: 2px;
    border-style: solid;
    border-radius: 9999px;
    border-top-color: transparent;
    animation: spin 0.7s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

