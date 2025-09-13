<template>
    <Head title="Account Locked" />
    <ResponsiveNavbar :can-login="true" :can-register="true" />
    <div class="flex justify-center pt-32 pb-12 px-4 sm:px-6 lg:px-8" style="background-color: #ECFDF5; min-height: 100vh;">
        <div class="w-full max-w-[48rem] bg-white p-10 rounded-2xl shadow-lg border border-green-100 space-y-8">
            <!-- Logo and Title -->
            <div class="text-center">
                <span class="text-2xl font-bold text-green-600 flex items-center justify-center">
                    <img src="/storage/image/logo-final.svg?v=2" alt="Logo" class="h-12 w-12 mr-3" />
                    Our Restaurant
                </span>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Account Temporarily Locked</h2>
            </div>

            <!-- Warning Message -->
            <div class="p-4 bg-red-100 text-red-700 rounded-lg flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Your account has been temporarily locked due to multiple failed login attempts.
            </div>

            <!-- Description -->
            <div class="text-center text-gray-600">
                <p>For your security, we've temporarily locked your account. Enter your email address below to receive a 6-digit OTP that will allow you to unlock your account and reset your password.</p>
            </div>

            <!-- Unlock Account Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="email" value="Email Address" />
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-green-600"></i>
                        </div>
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="pl-10 block w-full"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Enter your email address"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="flex items-center justify-between">
                    <Link :href="route('login')" class="text-sm text-green-600 hover:text-green-800 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Login
                    </Link>
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="px-6 py-2"
                    >
                        <i class="fas fa-unlock mr-2"></i>
                        {{ form.processing ? 'Sending...' : 'Unlock Account' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ResponsiveNavbar from '@/Components/ResponsiveNavbar.vue';

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('account.locked.submit'), {
        preserveScroll: true,
    });
};
</script>
