<template>
    <Head title="Log in" />
    <nav class="bg-white shadow-sm border-b border-green-100 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <Link :href="route('home')" class="text-2xl font-bold text-green-600 hover:text-green-800 flex items-center gap-2">
                        <svg class="h-8 w-8 mr-2" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <circle cx="16" cy="16" r="15" fill="url(#logoGrad)" stroke="#ffffff" stroke-width="1"/>
                            <g fill="white" stroke="white" stroke-width="0.5">
                                <line x1="8" y1="8" x2="8" y2="20" stroke-width="1"/>
                                <line x1="7" y1="8" x2="9" y2="8" stroke-width="1"/>
                                <line x1="7" y1="10" x2="9" y2="10" stroke-width="1"/>
                                <line x1="7" y1="12" x2="9" y2="12" stroke-width="1"/>
                                <line x1="24" y1="8" x2="24" y2="20" stroke-width="1"/>
                                <polygon points="24,8 26,10 24,12" fill="white"/>
                                <circle cx="16" cy="22" r="4" fill="none" stroke-width="1"/>
                                <circle cx="16" cy="22" r="2" fill="white"/>
                            </g>
                        </svg>
                        Our Restaurant
                    </Link>
                </div>
                <div class="flex items-center space-x-4">
                    <Link :href="route('contact')"
                        class="px-4 py-2 border-2 border-blue-600 text-blue-600 rounded hover:bg-blue-50 transition flex items-center gap-2">
                        <i class="fas fa-headset"></i> Contact Support
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
    <div class="flex justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-[48rem] bg-white p-10 rounded-2xl shadow-lg border border-green-100 space-y-8">
            <!-- Logo and Title -->
            <div class="text-center">
                <span class="text-3xl font-bold text-green-600 flex items-center justify-center">
                    <svg class="h-10 w-10 mr-3" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="logoGrad2" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <circle cx="16" cy="16" r="15" fill="url(#logoGrad2)" stroke="#ffffff" stroke-width="1"/>
                        <g fill="white" stroke="white" stroke-width="0.5">
                            <line x1="8" y1="8" x2="8" y2="20" stroke-width="1"/>
                            <line x1="7" y1="8" x2="9" y2="8" stroke-width="1"/>
                            <line x1="7" y1="10" x2="9" y2="10" stroke-width="1"/>
                            <line x1="7" y1="12" x2="9" y2="12" stroke-width="1"/>
                            <line x1="24" y1="8" x2="24" y2="20" stroke-width="1"/>
                            <polygon points="24,8 26,10 24,12" fill="white"/>
                            <circle cx="16" cy="22" r="4" fill="none" stroke-width="1"/>
                            <circle cx="16" cy="22" r="2" fill="white"/>
                        </g>
                    </svg>
                    Our Restaurant
                </span>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Sign in to your account</h2>
            </div>

            <!-- Status Message -->
            <div v-if="status" class="p-4 bg-green-100 text-green-700 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ status }}
            </div>

            <!-- Login Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-4">
                    <!-- Email Input -->
                    <div>
                        <InputLabel for="email" value="Email" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-green-600"></i>
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="block w-full pl-10 pr-3 py-3 border-green-300 focus:ring-green-500 focus:border-green-500"
                                placeholder="student@university.edu"
                                v-model="form.email"
                                required
                                autofocus
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Password Input -->
                    <div>
                        <InputLabel for="password" value="Password" />
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-green-600"></i>
                            </div>
                            <TextInput
                                id="password"
                                type="password"
                                class="block w-full pl-10 pr-3 py-3 border-green-300 focus:ring-green-500 focus:border-green-500"
                                placeholder="••••••••"
                                v-model="form.password"
                                required
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-green-600 hover:text-green-800 font-medium"
                        >
                            Forgot password?
                        </Link>
                    </div>
                </div>

                <!-- Submit Button -->
                <PrimaryButton
                    class="w-full justify-center py-3 text-sm font-medium bg-green-600 hover:bg-green-700 transition-colors"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </PrimaryButton>

                <!-- Register Prompt -->
                <div class="text-center text-sm text-gray-600">
                    Don't have an account?
                    <Link
                        :href="route('register')"
                        class="font-medium text-green-600 hover:text-green-800"
                    >
                        Create one now
                    </Link>
                </div>
            </form>

            <!-- Social Login Divider -->
            <div class="relative mt-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                </div>
            </div>

            <!-- Social Login Buttons (Outside Form) -->
            <div class="mt-6">
                <PrimaryButton
                    type="button"
                    class="w-full justify-center py-3 text-sm font-medium bg-red-600 hover:bg-red-700 transition-colors text-white"
                    @click.prevent="continueWithGoogle"
                >
                    <i class="fab fa-google mr-2"></i>
                    Google
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    canResetPassword: Boolean, // <-- add this
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const status = ref(null);

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}

function continueWithGoogle() {
    window.location.href = route('google.redirect');
}
</script>
