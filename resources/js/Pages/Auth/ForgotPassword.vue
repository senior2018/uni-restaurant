<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ResponsiveNavbar from '@/Components/ResponsiveNavbar.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Forgot Password" />
    <ResponsiveNavbar :can-login="true" :can-register="false" />
    <div class="flex justify-center pt-32 pb-12 px-4 sm:px-6 lg:px-8" style="background-color: #ECFDF5; min-height: 100vh;">
        <div class="w-full max-w-[48rem] bg-white p-10 rounded-2xl shadow-lg border border-green-100 space-y-8">
            <!-- Logo and Title -->
            <div class="text-center">
                <span class="text-2xl font-bold text-green-600 flex items-center justify-center">
                    <img src="/storage/image/logo-final.svg" alt="Logo" class="h-12 w-12 mr-3" />
                    Our Restaurant
                </span>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Reset Your Password</h2>
            </div>

            <!-- Description -->
            <div class="text-center text-gray-600">
                <p>Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>
            </div>

            <!-- Status Message -->
            <div v-if="status" class="p-4 bg-green-100 text-green-700 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ status }}
            </div>

            <!-- Reset Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="email" value="Email" />
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-green-600"></i>
                        </div>
                        <TextInput
                            id="email"
                            type="email"
                            class="pl-10 block w-full"
                            v-model="form.email"
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
                        <i class="fas fa-paper-plane mr-2"></i>
                        {{ form.processing ? 'Sending...' : 'Send Reset Link' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
