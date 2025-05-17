<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: String,
    user_id: Number,
    verified: String,
});
const form = useForm({
    user_id: props.user_id,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.reset.otp'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};


</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <form @submit.prevent="submit" class="max-w-md mx-auto mt-6">
            <div>
                <InputLabel value="Resetting password for:" />
                <div class="mt-1 p-2 bg-gray-100 rounded text-sm text-gray-700">
                    {{ email }}
                </div>
            </div>

            <div class="mb-4">
                <InputLabel for="password" value="New Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password" class="mt-1" />
            </div>

            <div class="mb-4">
                <InputLabel for="password_confirmation" value="Confirm New Password" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-1" />
            </div>

            <!-- Hidden input to send user_id -->
            <input type="hidden" :value="form.user_id" name="user_id" />

            <div class="flex justify-end">
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Reset Password
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
