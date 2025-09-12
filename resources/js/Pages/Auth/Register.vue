<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    permanent_location: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const redirectToGoogle = () => {
    window.location.href = route('google.redirect');
};
</script>

<template>
    <Head title="Create Account" />
    <nav class="bg-white shadow-sm border-b border-green-100 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <Link :href="route('home')" class="text-2xl font-bold text-green-600 hover:text-green-800 flex items-center gap-2">
                        <svg class="h-8 w-8 mr-2" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="logoGrad3" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <circle cx="16" cy="16" r="15" fill="url(#logoGrad3)" stroke="#ffffff" stroke-width="1"/>
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
                    <Link v-if="canLogin"
                        :href="route('login')"
                        class="px-4 py-2 text-green-600 hover:text-green-800 transition">
                        Login
                    </Link>
                </div>
            </div>
        </div>
    </nav>
    <div class="max-w-[56rem] w-full mx-auto mt-10 px-6 py-8 bg-white shadow-xl rounded-2xl space-y-6 border border-gray-200">
        <h2 class="text-3xl font-extrabold text-center text-green-800">Create Your Account</h2>

            <!-- Google Login Button -->
            <PrimaryButton
                type="button"
                class="w-full justify-center py-3 text-sm font-medium bg-red-600 hover:bg-red-700 transition-colors text-white"
                @click.prevent="redirectToGoogle"
            >
                <i class="fab fa-google mr-2"></i>
                Continue with Google
            </PrimaryButton>

            <!-- Divider -->
            <div class="relative flex items-center justify-center text-sm text-gray-500">
                <span class="absolute left-0 w-full border-t border-gray-200"></span>
                <span class="bg-white px-2 z-10">OR</span>
            </div>

            <!-- Registration Form -->
            <form @submit.prevent="submit" class="space-y-5">
                <!-- Name -->
                <div>
                    <InputLabel for="name" value="Full Name" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full rounded-lg"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email -->
                <div>
                    <InputLabel for="email" value="University Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full rounded-lg"
                        v-model="form.email"
                        required
                        autocomplete="email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Phone -->
                <div>
                    <InputLabel for="phone" value="Phone Number" />
                    <TextInput
                        id="phone"
                        type="tel"
                        class="mt-1 block w-full rounded-lg"
                        v-model="form.phone"
                        required
                        placeholder="+255700000000"
                        autocomplete="tel"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <!-- Location -->
                <div>
                    <InputLabel for="permanent_location" value="Campus Location" />
                    <TextInput
                        id="permanent_location"
                        type="text"
                        class="mt-1 block w-full rounded-lg"
                        v-model="form.permanent_location"
                        required
                        placeholder="e.g., Hostel Block C, Room 12"
                        autocomplete="address-line1"
                    />
                    <InputError class="mt-2" :message="form.errors.permanent_location" />
                </div>

                <!-- Passwords -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full rounded-lg"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full rounded-lg"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <!-- Submit & Link -->
                <div class="flex items-center justify-between pt-4">
                    <Link
                        :href="route('login')"
                        class="text-sm text-green-700 hover:text-green-900 font-medium transition"
                    >
                        Already have an account?
                    </Link>

                    <PrimaryButton
                        class="bg-green-700 hover:bg-green-800 focus:ring-green-500 rounded-lg px-5 py-2"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        Create Account
                    </PrimaryButton>
                </div>
            </form>
        </div>
</template>
