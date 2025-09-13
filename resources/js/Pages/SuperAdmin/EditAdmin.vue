<script setup>
import SuperAdminLayout from './Layout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    admin: Object,
    errors: Object
});

const form = useForm({
    name: props.admin?.name || '',
    email: props.admin?.email || '',
    phone: props.admin?.phone || '',
    permanent_location: props.admin?.permanent_location || '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const passwordChecks = ref({
    length: false,
    uppercase: false,
    lowercase: false,
    number: false,
    special: false,
});

const isPasswordValid = ref(true);

const validatePassword = () => {
    const password = form.password;
    passwordChecks.value = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password),
    };

    isPasswordValid.value = Object.values(passwordChecks.value).every(check => check);
};

const submit = () => {
    form.put(route('superadmin.admins.update', props.admin.id));
};
</script>

<template>
    <SuperAdminLayout title="Edit Admin">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">Edit Admin</h1>
                            <Link
                                :href="route('superadmin.admins')"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Admins
                            </Link>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Full Name
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.name }"
                                    required
                                />
                                <div v-if="errors.name" class="mt-1 text-sm text-red-600">
                                    {{ errors.name }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email Address
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.email }"
                                    required
                                />
                                <div v-if="errors.email" class="mt-1 text-sm text-red-600">
                                    {{ errors.email }}
                                </div>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Phone Number
                                </label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.phone }"
                                    required
                                />
                                <div v-if="errors.phone" class="mt-1 text-sm text-red-600">
                                    {{ errors.phone }}
                                </div>
                            </div>

                            <!-- Permanent Location -->
                            <div>
                                <label for="permanent_location" class="block text-sm font-medium text-gray-700">
                                    Permanent Location
                                </label>
                                <input
                                    id="permanent_location"
                                    v-model="form.permanent_location"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.permanent_location }"
                                    required
                                />
                                <div v-if="errors.permanent_location" class="mt-1 text-sm text-red-600">
                                    {{ errors.permanent_location }}
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    New Password (leave blank to keep current)
                                </label>
                                <div class="mt-1 relative">
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        @input="validatePassword"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pr-10"
                                        :class="{ 'border-red-500': errors.password }"
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    >
                                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400 hover:text-gray-600"></i>
                                    </button>
                                </div>
                                <div v-if="errors.password" class="mt-1 text-sm text-red-600">
                                    {{ errors.password }}
                                </div>

                                <!-- Password Requirements (only show when password is entered and invalid) -->
                                <div v-if="form.password && !isPasswordValid" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-md">
                                    <p class="text-sm font-medium text-red-800 mb-2">Password must contain:</p>
                                    <ul class="text-sm text-red-700 space-y-1">
                                        <li :class="{ 'text-green-600': passwordChecks.length }">
                                            <i :class="passwordChecks.length ? 'fas fa-check' : 'fas fa-times'" class="mr-2"></i>
                                            At least 8 characters
                                        </li>
                                        <li :class="{ 'text-green-600': passwordChecks.uppercase }">
                                            <i :class="passwordChecks.uppercase ? 'fas fa-check' : 'fas fa-times'" class="mr-2"></i>
                                            One uppercase letter
                                        </li>
                                        <li :class="{ 'text-green-600': passwordChecks.lowercase }">
                                            <i :class="passwordChecks.lowercase ? 'fas fa-check' : 'fas fa-times'" class="mr-2"></i>
                                            One lowercase letter
                                        </li>
                                        <li :class="{ 'text-green-600': passwordChecks.number }">
                                            <i :class="passwordChecks.number ? 'fas fa-check' : 'fas fa-times'" class="mr-2"></i>
                                            One number
                                        </li>
                                        <li :class="{ 'text-green-600': passwordChecks.special }">
                                            <i :class="passwordChecks.special ? 'fas fa-check' : 'fas fa-times'" class="mr-2"></i>
                                            One special character
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div v-if="form.password">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    Confirm New Password
                                </label>
                                <div class="mt-1 relative">
                                    <input
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        :type="showConfirmPassword ? 'text' : 'password'"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pr-10"
                                        :class="{ 'border-red-500': errors.password_confirmation }"
                                    />
                                    <button
                                        type="button"
                                        @click="showConfirmPassword = !showConfirmPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    >
                                        <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400 hover:text-gray-600"></i>
                                    </button>
                                </div>
                                <div v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">
                                    {{ errors.password_confirmation }}
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link
                                    :href="route('superadmin.admins')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing || (form.password && !isPasswordValid)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fas fa-save mr-2"></i>
                                    {{ form.processing ? 'Updating...' : 'Update Admin' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>
