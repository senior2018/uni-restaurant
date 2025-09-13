<template>
    <Head title="Log in" />
    <ResponsiveNavbar :can-login="false" :can-register="canRegister" />
    <div class="flex justify-center pt-32 pb-12 px-4 sm:px-6 lg:px-8" style="background-color: #ECFDF5; min-height: 100vh;">
        <div class="w-full max-w-[48rem] bg-white p-10 rounded-2xl shadow-lg border border-green-100 space-y-8">
            <!-- Logo and Title -->
            <div class="text-center">
                <span class="text-2xl font-bold text-green-600 flex items-center justify-center">
                    <img src="/storage/image/logo-final.svg?v=2" alt="Logo" class="h-12 w-12 mr-3" />
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
                        
                        <!-- Show registration link if user doesn't exist -->
                        <div v-if="form.errors.email && form.errors.email.includes('No account found')" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                <span class="text-sm text-blue-800">Don't have an account yet?</span>
                            </div>
                            <Link
                                :href="route('register')"
                                class="mt-2 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800"
                            >
                                <i class="fas fa-user-plus mr-1"></i>
                                Create your account now
                            </Link>
                        </div>
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
import ResponsiveNavbar from '@/Components/ResponsiveNavbar.vue';
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
