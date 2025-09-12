<script setup>
import AdminLayout from './Layout.vue';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    tickets: Object, // paginated
});

const showModal = ref(false);
const selectedTicket = ref(null);
const response = ref('');
const responseError = ref('');
const isSubmitting = ref(false);
const successMessage = ref('');

function openModal(ticket) {
    selectedTicket.value = ticket;
    response.value = ticket.admin_response || '';
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    selectedTicket.value = null;
}

function fetchTickets() {
    router.reload({ only: ['tickets'] });
}

function submitResponse() {
    if (!selectedTicket.value) return;
    isSubmitting.value = true;
    responseError.value = '';
    axios.patch(route('support-tickets.respond', selectedTicket.value.id), {
        admin_response: response.value,
        status: 'closed',
    })
    .then(res => {
        selectedTicket.value.admin_response = response.value;
        selectedTicket.value.status = 'closed';
        showModal.value = false;
        successMessage.value = 'Response sent successfully!';
        fetchTickets();
        setTimeout(() => { successMessage.value = ''; }, 4000);
    })
    .catch(err => {
        responseError.value = err.response?.data?.message || 'Failed to submit response.';
    })
    .finally(() => {
        isSubmitting.value = false;
    });
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-6xl mx-auto p-responsive">
            <h1 class="text-responsive-lg font-bold text-primary-dark mb-6">Support Tickets</h1>
            <div v-if="successMessage" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ successMessage }}
            </div>
            <div class="table-responsive bg-white rounded shadow">
                <table class="min-w-full text-xs sm:text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 sm:px-4 py-2 text-left">ID</th>
                            <th class="px-2 sm:px-4 py-2 text-left hidden sm:table-cell">Name</th>
                            <th class="px-2 sm:px-4 py-2 text-left hidden md:table-cell">Email</th>
                            <th class="px-2 sm:px-4 py-2 text-left">Subject</th>
                            <th class="px-2 sm:px-4 py-2 text-left hidden lg:table-cell">User Type</th>
                            <th class="px-2 sm:px-4 py-2 text-left">Status</th>
                            <th class="px-2 sm:px-4 py-2 text-left hidden lg:table-cell">Created At</th>
                            <th class="px-2 sm:px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ticket in tickets.data" :key="ticket.id" class="border-b hover:bg-blue-50">
                            <td class="px-2 sm:px-4 py-2 font-semibold">{{ ticket.id }}</td>
                            <td class="px-2 sm:px-4 py-2 hidden sm:table-cell">{{ ticket.name }}</td>
                            <td class="px-2 sm:px-4 py-2 hidden md:table-cell">{{ ticket.email }}</td>
                            <td class="px-2 sm:px-4 py-2">
                                <div class="flex flex-col gap-1">
                                    <div class="font-medium">{{ ticket.subject }}</div>
                                    <div class="sm:hidden text-xs text-gray-500">
                                        <div>{{ ticket.name }}</div>
                                        <div v-if="ticket.email">{{ ticket.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 sm:px-4 py-2 hidden lg:table-cell">
                                <span v-if="ticket.is_registered" class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Registered</span>
                                <span v-else class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">Unregistered</span>
                            </td>
                            <td class="px-2 sm:px-4 py-2">
                                <div class="flex flex-col gap-1">
                                    <span :class="ticket.status === 'open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'" class="px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ ticket.status.charAt(0).toUpperCase() + ticket.status.slice(1) }}
                                    </span>
                                    <div class="lg:hidden text-xs text-gray-500">
                                        <div v-if="ticket.is_registered" class="text-green-600">Registered</div>
                                        <div v-else class="text-yellow-600">Unregistered</div>
                                        <div>{{ new Date(ticket.created_at).toLocaleDateString() }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 sm:px-4 py-2 hidden lg:table-cell">{{ new Date(ticket.created_at).toLocaleString() }}</td>
                            <td class="px-2 sm:px-4 py-2">
                                <button @click="openModal(ticket)" class="px-2 sm:px-3 py-1 bg-primary text-white rounded hover:bg-primary-dark text-xs sm:text-sm">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="flex justify-center mt-6 gap-2">
                <button :disabled="!tickets.prev_page_url" @click="$inertia.visit(tickets.prev_page_url)" class="px-3 py-1 rounded border" :class="tickets.prev_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Prev</button>
                <span class="px-2">Page {{ tickets.current_page }} of {{ tickets.last_page }}</span>
                <button :disabled="!tickets.next_page_url" @click="$inertia.visit(tickets.next_page_url)" class="px-3 py-1 rounded border" :class="tickets.next_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Next</button>
            </div>
            <!-- Ticket Details Modal -->
            <Modal :show="showModal" @close="closeModal">
                <div class="p-6 max-w-lg mx-auto">
                    <h2 class="text-lg font-bold mb-2">Ticket #{{ selectedTicket?.id }}</h2>
                    <div class="mb-2"><span class="font-medium">Name:</span> {{ selectedTicket?.name }}</div>
                    <div class="mb-2"><span class="font-medium">Email:</span> {{ selectedTicket?.email }}</div>
                    <div class="mb-2"><span class="font-medium">Subject:</span> {{ selectedTicket?.subject }}</div>
                    <div class="mb-2"><span class="font-medium">Message:</span> {{ selectedTicket?.message }}</div>
                    <div class="mb-2"><span class="font-medium">User Type:</span>
                        <span v-if="selectedTicket?.is_registered" class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Registered</span>
                        <span v-else class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">Unregistered</span>
                    </div>
                    <div class="mb-2"><span class="font-medium">Status:</span>
                        <span :class="selectedTicket?.status === 'open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'" class="px-2 py-1 rounded-full text-xs font-semibold">
                            {{ selectedTicket?.status?.charAt(0).toUpperCase() + selectedTicket?.status?.slice(1) }}
                        </span>
                    </div>
                    <div class="mb-2"><span class="font-medium">Created At:</span> {{ new Date(selectedTicket?.created_at).toLocaleString() }}</div>
                    <!-- Admin Response Form -->
                    <div v-if="selectedTicket">
                        <div v-if="selectedTicket.admin_response" class="mb-2 p-2 bg-green-100 border border-green-300 rounded">
                            <span class="font-semibold">Your Response:</span> {{ selectedTicket.admin_response }}
                        </div>
                        <form @submit.prevent="submitResponse" class="mt-4">
                            <label class="block font-medium mb-1">Respond to Ticket</label>
                            <textarea v-model="response" class="w-full border px-3 py-2 rounded" rows="3"></textarea>
                            <div v-if="responseError" class="text-red-600 text-sm mt-1">{{ responseError }}</div>
                            <button type="submit" :disabled="isSubmitting" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                {{ isSubmitting ? 'Sending...' : 'Send Response & Close Ticket' }}
                            </button>
                        </form>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button @click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
                    </div>
                </div>
            </Modal>
        </div>
    </AdminLayout>
</template>
