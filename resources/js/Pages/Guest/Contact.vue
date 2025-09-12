<script setup>
import { ref } from 'vue';
import { router, usePage, Head, Link } from '@inertiajs/vue3';
import CustomerLayout from '../Customer/Layout.vue';
import axios from 'axios';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const page = usePage();
const user = page.props.auth?.user;
const isRegistered = !!user;
const form = ref({
    name: user?.name || '',
    email: user?.email || '',
    subject: '',
    message: '',
    is_registered: isRegistered,
});
const errors = ref({});
const success = ref(page.props.flash?.success || '');
const tickets = ref([]);
const expandedTicket = ref(null);
if (isRegistered) {
    axios.get(route('staff.support-tickets.mine'))
        .then(res => { tickets.value = res.data.tickets; })
        .catch(() => { tickets.value = []; });
}

function submit() {
    errors.value = {};
    router.post(route('contact.store'), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            success.value = 'Your message has been received. We will contact you soon.';
            form.value = { name: user?.name || '', email: user?.email || '', subject: '', message: '' };
        },
        onError: (e) => { errors.value = e; },
    });
}
</script>

<template>
    <CustomerLayout v-if="isRegistered" :user="user">
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-green-50 to-white py-12 px-2">
            <div class="w-full max-w-lg p-8 bg-white rounded-2xl shadow-2xl border border-blue-100">
                <div class="flex flex-col items-center mb-6">
                    <div class="bg-blue-100 text-blue-600 rounded-full p-4 mb-2 shadow">
                        <i class="fas fa-headset text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-1">Contact Support</h2>
                    <p class="text-gray-500 text-center mb-2">We're here to help! Please fill out the form below and our team will get back to you as soon as possible.</p>
                </div>
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block font-medium mb-1">Name</label>
                        <input v-model="form.name" type="text" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition" :readonly="isRegistered" />
                        <div v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name }}</div>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Email</label>
                        <input v-model="form.email" type="email" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition" :readonly="isRegistered" :required="!isRegistered" />
                        <div v-if="errors.email" class="text-red-600 text-sm mt-1">{{ errors.email }}</div>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Subject</label>
                        <input v-model="form.subject" type="text" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition" />
                        <div v-if="errors.subject" class="text-red-600 text-sm mt-1">{{ errors.subject }}</div>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Message</label>
                        <textarea v-model="form.message" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition" rows="5"></textarea>
                        <div v-if="errors.message" class="text-red-600 text-sm mt-1">{{ errors.message }}</div>
                    </div>
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg shadow-lg font-semibold text-lg transition">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </form>
                <div v-if="success" class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-center">
                    {{ success }}
                </div>
                <div v-if="tickets.length" class="mt-10">
                    <h3 class="text-lg font-semibold mb-2">Your Previous Support Issues</h3>
                    <div class="max-h-80 overflow-y-auto pr-1">
                        <div v-for="ticket in tickets" :key="ticket.id" class="border rounded mb-3 bg-gray-50">
                            <button type="button" class="w-full flex justify-between items-center px-4 py-3 focus:outline-none" @click="expandedTicket = expandedTicket === ticket.id ? null : ticket.id">
                                <div class="flex flex-col text-left">
                                    <span class="font-medium">Subject: {{ ticket.subject }}</span>
                                    <span class="text-xs text-gray-500">Status: <span :class="ticket.status === 'open' ? 'text-yellow-700' : 'text-green-700'">{{ ticket.status }}</span></span>
                                    <span class="text-xs text-gray-400">{{ new Date(ticket.created_at).toLocaleString() }}</span>
                                </div>
                                <span>
                                    <i :class="expandedTicket === ticket.id ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                                </span>
                            </button>
                            <div v-if="expandedTicket === ticket.id" class="px-4 pb-4">
                                <div class="mt-2 text-gray-700"><span class="font-semibold">Message:</span> {{ ticket.message }}</div>
                                <div v-if="ticket.admin_response" class="mt-2 p-2 bg-green-100 border border-green-300 rounded">
                                    <span class="font-semibold">Admin Response:</span> {{ ticket.admin_response }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 text-center text-gray-400 text-xs">
                    Need urgent help? Email <a href="mailto:support@unirestaurant.com" class="text-blue-600 underline">support@unirestaurant.com</a>
                </div>
            </div>
        </div>
    </CustomerLayout>
    <div v-else>
        <Head title="Contact Support" />
        <nav class="bg-white shadow-sm border-b border-blue-100 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-green-600 flex items-center">
                            <img src="/storage/image/logo.jpg" alt="Logo" class="h-8 w-8 mr-2" />
                            Our Restaurant
                        </span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link v-if="canLogin"
                            :href="route('login')"
                            class="px-4 py-2 text-green-600 hover:text-green-800 transition">
                            Login
                        </Link>
                        <Link v-if="canRegister"
                            :href="route('register')"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                            Register
                        </Link>
                    </div>
                </div>
            </div>
        </nav>
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-green-50 to-white py-12 px-2">
            <div class="w-full max-w-lg p-8 bg-white rounded-2xl shadow-2xl border border-blue-100">
                <div class="flex flex-col items-center mb-6">
                    <div class="bg-blue-100 text-blue-600 rounded-full p-4 mb-2 shadow">
                        <i class="fas fa-headset text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-1">Contact Support</h2>
                    <p class="text-gray-500 text-center mb-2">We're here to help! Please fill out the form below and our team will get back to you as soon as possible.</p>
                </div>
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block font-medium mb-1">Name</label>
                        <input v-model="form.name" type="text" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition" :readonly="isRegistered" />
                        <div v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name }}</div>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Email</label>
                        <input v-model="form.email" type="email" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition" :readonly="isRegistered" :required="!isRegistered" />
                        <div v-if="errors.email" class="text-red-600 text-sm mt-1">{{ errors.email }}</div>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Subject</label>
                        <input v-model="form.subject" type="text" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition" />
                        <div v-if="errors.subject" class="text-red-600 text-sm mt-1">{{ errors.subject }}</div>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Message</label>
                        <textarea v-model="form.message" class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition" rows="5"></textarea>
                        <div v-if="errors.message" class="text-red-600 text-sm mt-1">{{ errors.message }}</div>
                    </div>
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg shadow-lg font-semibold text-lg transition">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </form>
                <div v-if="success" class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-center">
                    {{ success }}
                </div>
                <div class="mt-8 text-center text-gray-400 text-xs">
                    Need urgent help? Email <a href="mailto:support@unirestaurant.com" class="text-blue-600 underline">support@unirestaurant.com</a>
                </div>
            </div>
        </div>
    </div>
</template>
