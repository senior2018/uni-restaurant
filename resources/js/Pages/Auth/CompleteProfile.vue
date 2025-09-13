<template>
    <Head title="Complete Your Profile" />
    <ResponsiveNavbar :can-login="false" :can-register="false" />
    <div class="flex justify-center pt-32 pb-12 px-4 sm:px-6 lg:px-8" style="background-color: #ECFDF5; min-height: 100vh;">
        <div class="w-full max-w-[48rem] bg-white p-10 rounded-2xl shadow-lg border border-green-100 space-y-8">
            <!-- Logo and Title -->
            <div class="text-center">
                <span class="text-2xl font-bold text-green-600 flex items-center justify-center">
                    <img src="/storage/image/logo-final.svg?v=2" alt="Logo" class="h-12 w-12 mr-3" />
                    Our Restaurant
                </span>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Complete Your Profile</h2>
            </div>

            <!-- Warning Message -->
            <div v-if="$page.props.flash.warning" class="p-4 bg-yellow-100 text-yellow-700 rounded-lg flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ $page.props.flash.warning }}
            </div>

            <!-- Description -->
            <div class="text-center text-gray-600">
                <p>Welcome! To get started, please complete your profile by providing your phone number, location, and setting up a password for your account.</p>
            </div>

            <!-- Profile Completion Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Phone Number -->
                <div>
                    <InputLabel for="phone" value="Phone Number" />
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-green-600"></i>
                        </div>
                        <TextInput
                            id="phone"
                            type="tel"
                            class="pl-10 block w-full"
                            v-model="form.phone"
                            required
                            autofocus
                            autocomplete="tel"
                            placeholder="Enter your phone number"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <!-- Permanent Location -->
                <div>
                    <InputLabel for="permanent_location" value="Permanent Location" />
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-map-marker-alt text-green-600"></i>
                        </div>
                        <TextInput
                            id="permanent_location"
                            type="text"
                            class="pl-10 block w-full"
                            v-model="form.permanent_location"
                            required
                            autocomplete="address-line1"
                            placeholder="Enter your permanent location"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.permanent_location" />
                </div>

                <!-- Password -->
                <div>
                    <InputLabel for="password" value="Password" />
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-green-600"></i>
                        </div>
                        <TextInput
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            class="pl-10 pr-12 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="Create a secure password"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-green-600 transition-colors"
                        >
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
    </div>
                    <InputError class="mt-2" :message="form.errors.password" />

                    <!-- Password Requirements Warning (only show when password doesn't meet requirements) -->
                    <div v-if="form.password && !isPasswordValid" class="mt-2 text-xs text-red-600 bg-red-50 p-3 rounded-lg border border-red-200">
                        <p class="font-medium mb-2 text-red-800">Password must contain:</p>
                        <ul class="space-y-1">
                            <li class="flex items-center">
                                <i :class="passwordChecks.length ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500'"></i>
                                <span class="ml-2">At least 8 characters</span>
                            </li>
                            <li class="flex items-center">
                                <i :class="passwordChecks.uppercase ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500'"></i>
                                <span class="ml-2">One uppercase letter (A-Z)</span>
                            </li>
                            <li class="flex items-center">
                                <i :class="passwordChecks.lowercase ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500'"></i>
                                <span class="ml-2">One lowercase letter (a-z)</span>
                            </li>
                            <li class="flex items-center">
                                <i :class="passwordChecks.number ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500'"></i>
                                <span class="ml-2">One number (0-9)</span>
                            </li>
                            <li class="flex items-center">
                                <i :class="passwordChecks.special ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500'"></i>
                                <span class="ml-2">One special character (!@#$%^&*)</span>
                            </li>
                        </ul>
                    </div>
    </div>

                <!-- Confirm Password -->
                <div>
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-green-600"></i>
    </div>
                        <TextInput
                            id="password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            class="pl-10 pr-12 block w-full"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Confirm your password"
                        />
                        <button
                            type="button"
                            @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-green-600 transition-colors"
                        >
                            <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-center">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="w-full justify-center py-3 text-sm font-medium"
                    >
                        <i class="fas fa-save mr-2"></i>
                        {{ form.processing ? 'Saving...' : 'Complete Profile' }}
                    </PrimaryButton>
                </div>
    </form>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import ResponsiveNavbar from '@/Components/ResponsiveNavbar.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({ user: Object });

const form = useForm({
    phone: props.user.phone || '',
    permanent_location: props.user.permanent_location || '',
    password: '',
    password_confirmation: ''
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

// Password strength validation
const passwordChecks = computed(() => {
    const password = form.password;
    return {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[\W_]/.test(password)
    };
});

// Check if password meets all requirements
const isPasswordValid = computed(() => {
    const checks = passwordChecks.value;
    return checks.length && checks.uppercase && checks.lowercase && checks.number && checks.special;
});

function submit() {
    form.post(route('complete-profile.update'));
}
</script>
