<script setup>
import SuperAdminLayout from './Layout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    permanent_location: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const passwordChecks = computed(() => ({
    length: form.password.length >= 8,
    uppercase: /[A-Z]/.test(form.password),
    lowercase: /[a-z]/.test(form.password),
    number: /\d/.test(form.password),
    special: /[@$!%*?&]/.test(form.password),
}));

const isPasswordValid = computed(() => {
    return Object.values(passwordChecks.value).every(check => check);
});

const submit = () => {
    form.post(route('superadmin.admins.store'));
};
</script>

<template>
    <SuperAdminLayout>
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <Link :href="route('superadmin.admins')"
                      class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-4 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Admin Management
                </Link>
                <h2 class="text-2xl font-bold text-gray-800">Create New Admin</h2>
                <p class="text-gray-600 mt-1">Add a new administrator to the system</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-500': form.errors.name }"
                        />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-500': form.errors.email }"
                        />
                        <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input
                            id="phone"
                            v-model="form.phone"
                            type="tel"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-500': form.errors.phone }"
                        />
                        <div v-if="form.errors.phone" class="text-red-600 text-sm mt-1">
                            {{ form.errors.phone }}
                        </div>
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="permanent_location" class="block text-sm font-medium text-gray-700 mb-2">
                            Permanent Location
                        </label>
                        <input
                            id="permanent_location"
                            v-model="form.permanent_location"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            :class="{ 'border-red-500': form.errors.permanent_location }"
                        />
                        <div v-if="form.errors.permanent_location" class="text-red-600 text-sm mt-1">
                            {{ form.errors.permanent_location }}
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': form.errors.password }"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400"></i>
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">
                            {{ form.errors.password }}
                        </div>

                        <!-- Password Requirements -->
                        <div v-if="form.password && !isPasswordValid" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
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
                                    One special character (@$!%*?&)
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                required
                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': form.errors.password_confirmation }"
                            />
                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400"></i>
                            </button>
                        </div>
                        <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm mt-1">
                            {{ form.errors.password_confirmation }}
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <Link :href="route('superadmin.admins')"
                              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing || !isPasswordValid"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Admin</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Admin Privileges</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>New admins will have access to:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>Meal and category management</li>
                                <li>Order management and tracking</li>
                                <li>User support and alerts</li>
                                <li>System analytics and reports</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>
