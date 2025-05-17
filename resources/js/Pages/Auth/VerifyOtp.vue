<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

// Try to get props via Inertia first
let props;
try {

    props = defineProps({
        email: String,
        context: String,
        user_id: Number,
        status: String
    });
} catch (e) {
    props = {};
}

// Fallback to query parameters if needed
const urlParams = new URLSearchParams(window.location.search);
const email = props.email ?? urlParams.get('email') ?? '';
const context = props.context ?? urlParams.get('context') ?? 'register';
const user_id = props.user_id ?? null;

// 👇 Normalize context
const normalizeContext = (ctx) => {
    switch (ctx) {
        case 'register':
        case 'login_unverified':
            return 'email';
        case 'password_reset':
            return 'forgot_password';
        case 'locked':
        case 'locked_account':
            return 'locked_account';
        default:
            return ctx;
    }
};

const normalizedContext = normalizeContext(context);

// Setup form
const form = useForm({
    email,
    otp: '',
    context: normalizedContext,
});

const otpResent = ref(false);

const submit = () => {
    form.post(route('otp.verify'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('otp');
        },
    });
};

const resendOtp = () => {
    form.post(route('verify.otp.resend'), {
        preserveScroll: true,
        onSuccess: () => {
            otpResent.value = true;
            setTimeout(() => {
                otpResent.value = false;
            }, 10000);
        },
        onError: () => {
            alert('Failed to resend OTP. Try again later.');
        },
    });
};

const contextMessages = {
    register: {
        heading: 'Verify Your Email',
        message: email => `Thanks for signing up! A 6-digit OTP has been sent to <strong>${email}</strong>. Please enter it to complete your registration.`,
    },
    login_unverified: {
        heading: 'Account Not Verified',
        message: email => `Your account <strong>${email}</strong> is not yet verified. Please enter the 6-digit OTP sent to your email.`,
    },
    forgot_password: {
        heading: 'Reset Your Password',
        message: email => `We've sent a 6-digit OTP to <strong>${email}</strong>. Enter it below to reset your password.`,
    },
    locked_account: {
        heading: 'Account Locked',
        message: email => `Your account <strong>${email}</strong> is locked. Enter the OTP we emailed you to unlock and reset your password.`,
    },
};
</script>



<template>
    <GuestLayout>
        <Head title="Verify OTP" />

        <!-- Context-based heading and instruction -->
        <div class="mb-4 text-sm text-gray-600">
            <h2 class="font-semibold text-lg mb-2">
                {{ contextMessages[normalizedContext]?.heading || 'Verify OTP' }}
            </h2>
            <span v-html="contextMessages[normalizedContext]?.message(props.email) || `Enter the OTP sent to <strong>${props.email}</strong>`" />
        </div>

        <!-- Green success message after OTP resend -->
        <div v-if="otpResent" class="mb-4 text-sm font-medium text-green-600">
            A new OTP has been sent to the email address you provided.
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="otp" value="Verification Code" />
                <TextInput
                    id="otp"
                    type="text"
                    inputmode="numeric"
                    pattern="\d{6}"
                    class="mt-1 block w-full text-center text-xl tracking-[0.5em]"
                    v-model="form.otp"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.otp" />
            </div>

            <div class="flex items-center justify-between">
                <PrimaryButton :disabled="form.processing">Verify OTP</PrimaryButton>

                <button
                    type="button"
                    @click="resendOtp"
                    class="text-sm text-green-600 hover:text-green-800 ml-4"
                >
                    Didn't get the code? Resend
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
