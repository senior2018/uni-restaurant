<template>
    <GuestLayout>
    <Head title="Account Locked" />

    <div class="mb-4 text-red-600">
        Your account has been locked due to multiple failed login attempts.
        Enter your email below to receive a 6-digit OTP to unlock your account and reset your password.
    </div>

    <form @submit.prevent="submit">
        <div>
        <InputLabel for="email" value="Email" />
        <TextInput
            id="email"
            v-model="form.email"
            type="email"
            class="mt-1 block w-full"
            required
            autofocus
        />
        <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="flex items-center justify-end mt-4">
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Unlock Account
        </PrimaryButton>
        </div>
    </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('account.locked.submit'), {
        preserveScroll: true,
    });
};
</script>
