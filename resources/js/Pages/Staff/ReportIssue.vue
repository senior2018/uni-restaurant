<script setup>
import { ref, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import StaffLayout from './Layout.vue';
import axios from 'axios';
import FlashMessage from '@/Components/UI/FlashMessage.vue';

const page = usePage();
const user = page.props.user;
const form = ref({
    name: user?.name || '',
    email: user?.email || '',
    subject: '',
    message: '',
});
const errors = ref({});
const success = ref(page.props.flash?.success || '');
const tickets = ref([]);

onMounted(() => {
    axios.get(route('staff.support-tickets.mine'))
        .then(res => { tickets.value = res.data.tickets; })
        .catch(() => { tickets.value = []; });
});

function submit() {
    errors.value = {};
    router.post(route('staff.report-issue.store'), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            success.value = 'Your report has been submitted to the admin.';
            form.value = { subject: '', message: '', name: user?.name || '', email: user?.email || '' };
        },
        onError: (e) => { errors.value = e; },
    });
}
</script>

<template>
    <StaffLayout :user="user">
    <div class="max-w-2xl mx-auto mt-6 sm:mt-10 p-4 sm:p-6 bg-white rounded-xl shadow-md">
        <h2 class="text-xl sm:text-2xl font-semibold mb-4">Report an Issue to Admin</h2>
        <form @submit.prevent="submit" class="space-y-4">
            <input type="hidden" v-model="form.name" />
            <input type="hidden" v-model="form.email" />
            <div>
                <label class="block font-medium text-sm sm:text-base">Subject</label>
                <input v-model="form.subject" type="text" class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3 rounded-md text-sm sm:text-base focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                <div v-if="errors.subject" class="text-red-600 text-xs sm:text-sm">{{ errors.subject }}</div>
            </div>
            <div>
                <label class="block font-medium text-sm sm:text-base">Message</label>
                <textarea v-model="form.message" class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3 rounded-md text-sm sm:text-base focus:ring-2 focus:ring-green-500 focus:border-green-500" rows="5"></textarea>
                <div v-if="errors.message" class="text-red-600 text-xs sm:text-sm">{{ errors.message }}</div>
            </div>
            <button type="submit" class="w-full sm:w-auto px-4 py-2 sm:px-6 sm:py-3 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm sm:text-base font-medium transition-colors duration-200">Send</button>
        </form>
        <!-- Previous Reports Section -->
        <div v-if="tickets.length" class="mt-6 sm:mt-8">
            <h3 class="text-base sm:text-lg font-semibold mb-2">Your Previous Reports</h3>
            <div v-for="ticket in tickets" :key="ticket.id" class="border rounded p-3 sm:p-4 mb-3 bg-gray-50">
                <div class="font-medium text-sm sm:text-base">Subject: {{ ticket.subject }}</div>
                <div class="text-xs sm:text-sm text-gray-600">Status: <span :class="ticket.status === 'open' ? 'text-yellow-700' : 'text-green-700'">{{ ticket.status }}</span></div>
                <div class="mt-2 text-sm sm:text-base">{{ ticket.message }}</div>
                <div v-if="ticket.admin_response" class="mt-2 p-2 bg-green-100 border border-green-300 rounded">
                    <span class="font-semibold text-sm sm:text-base">Admin Response:</span> <span class="text-sm sm:text-base">{{ ticket.admin_response }}</span>
                </div>
            </div>
        </div>
        <FlashMessage
            v-if="success"
            type="success"
            :message="success"
            @close="success = null"
        />
    </div>
</StaffLayout>
</template>
