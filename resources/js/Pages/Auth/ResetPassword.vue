<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ResponsiveNavbar from '@/Components/ResponsiveNavbar.vue';

const props = defineProps({
    email: String,
    user_id: Number,
    verified: String,
    context: String,
});

const form = useForm({
    user_id: props.user_id,
    password: '',
    password_confirmation: '',
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

const submit = () => {
    form.post(route('password.reset.otp'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head :title="context === 'locked_account' ? 'Unlock Account' : 'Reset Password'" />
    <ResponsiveNavbar :can-login="true" :can-register="true" />
    <div class="flex justify-center pt-32 pb-12 px-4 sm:px-6 lg:px-8" style="background-color: #ECFDF5; min-height: 100vh;">
        <div class="w-full max-w-[48rem] bg-white p-10 rounded-2xl shadow-lg border border-green-100 space-y-8">
            <!-- Logo and Title -->
            <div class="text-center">
                <span class="text-2xl font-bold text-green-600 flex items-center justify-center">
                    <img src="/storage/image/logo-final.svg?v=2" alt="Logo" class="h-12 w-12 mr-3" />
                    Our Restaurant
                </span>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">
                    {{ context === 'locked_account' ? 'Unlock Your Account' : 'Reset Your Password' }}
                </h2>
            </div>

            <!-- User Info -->
            <div class="text-center">
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-user-circle text-green-600 mr-2"></i>
                        <span class="text-sm font-medium text-green-800">Resetting password for:</span>
                    </div>
                    <div class="mt-1 text-lg font-semibold text-green-900">{{ email }}</div>
                </div>
            </div>

            <!-- Description -->
            <div class="text-center text-gray-600">
                <p v-if="context === 'locked_account'">
                    Your account has been unlocked! Please set a new secure password to complete the process.
                </p>
                <p v-else>
                    Please enter a new secure password for your account.
                </p>
            </div>

            <!-- Password Reset Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- New Password -->
                <div>
                    <InputLabel for="password" value="New Password" />
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
                            placeholder="Enter your new password"
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

                <!-- Confirm New Password -->
                <div>
                    <InputLabel for="password_confirmation" value="Confirm New Password" />
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
                            placeholder="Confirm your new password"
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
                        class="w-full justify-center py-3 text-sm font-medium bg-green-600 hover:bg-green-700 transition-colors"
                    >
                        <i :class="context === 'locked_account' ? 'fas fa-unlock' : 'fas fa-key'" class="mr-2"></i>
                        {{ form.processing ? 'Processing...' : (context === 'locked_account' ? 'Unlock Account' : 'Reset Password') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
