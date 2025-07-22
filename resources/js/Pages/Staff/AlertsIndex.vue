<script setup>
import StaffLayout from './Layout.vue';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    alerts: Object, // paginated
    user: Object,
    role: String, // 'staff'
});

const filterResolved = ref('all');
const filterUser = ref('');
const showModal = ref(false);
const selectedAlert = ref(null);
const staffResponse = ref('');

function applyFilters() {
    router.get(route('staff.alerts.index'), {
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
    router.patch(route('staff.alerts.respond', selectedAlert.value.id), {
        staff_response: staffResponse.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            router.reload({ only: ['unresolvedAlertCount', 'alerts'] });
        },
        onError: () => {},
    });
}
</script>

<template>
    <StaffLayout :user="user">
        <div class="max-w-6xl mx-auto p-6">
            <h1 class="text-2xl font-bold text-primary-dark mb-6">Alerts Management</h1>
            <div class="flex flex-wrap gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select v-model="filterResolved" @change="applyFilters" class="border rounded px-2 py-1">
                        <option value="all">All</option>
                        <option value="0">Unresolved</option>
                        <option value="1">Resolved</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">User ID</label>
                    <input v-model="filterUser" @keyup.enter="applyFilters" type="text" class="border rounded px-2 py-1" placeholder="User ID" />
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-sm">
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
                    <div v-if="!selectedAlert?.resolved" class="mt-4 flex gap-2">
                        <input v-model="staffResponse" type="text" class="flex-1 border rounded px-2 py-1" placeholder="Write a response..." />
                        <button @click="submitResponse" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark">Send</button>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button @click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
                    </div>
                </div>
            </Modal>
        </div>
    </StaffLayout>
</template>
