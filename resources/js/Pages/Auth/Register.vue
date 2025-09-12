<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ResponsiveNavbar from '@/Components/ResponsiveNavbar.vue';
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
    <ResponsiveNavbar :can-login="canLogin" :can-register="false" />
    <div class="flex justify-center pt-32 pb-12 px-4 sm:px-6 lg:px-8" style="background-color: #ECFDF5; min-height: 100vh;">
        <div class="w-full max-w-[56rem] bg-white p-10 rounded-2xl shadow-lg border border-green-100 space-y-8">
        <!-- Logo and Title Section -->
        <div class="text-center space-y-4">
            <div class="flex items-center justify-center">
                <img src="/storage/image/logo-final.svg?v=2" alt="Our Restaurant Logo" class="h-12 w-12 mr-3" />
                <span class="text-2xl font-bold text-green-600">Our Restaurant</span>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-800">Create Your Account</h2>
        </div>

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
    </div>
</template>
