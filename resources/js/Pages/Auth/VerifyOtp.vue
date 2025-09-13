<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ResponsiveNavbar from '@/Components/ResponsiveNavbar.vue';
import { ref } from 'vue';

const props = defineProps({
    email: String,
    context: String,
    user_id: Number,
    status: String
});

    // console.log(props);

// Fallback to query parameters if needed
const urlParams = new URLSearchParams(window.location.search);
const email = props.email ?? urlParams.get('email') ?? '';
const context = props.context ?? urlParams.get('context') ?? 'register';
// console.log(context);

const user_id = props.user_id ?? null;

// 👇 Normalize context
const normalizeContext = (ctx) => {
    switch (ctx) {
        case 'register':
        case 'login_unverified':
            return 'email';
        case 'forgot_password':
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

// console.log(normalizedContext);

// Setup form
const form = useForm({
    email,
    otp: '',
    context: normalizedContext,
});

// console.log(form);


const otpResent = ref(false);

const submit = () => {
    form.post(route('otp.verify'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('otp');
        },
    });

    // console.log(form);

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
        message: email => `Welcome! To activate your account, please verify your email address. We've sent a <strong>verification code</strong> to <strong>${email}</strong>.
        Please check your inbox (and spam folder). If you haven’t received the email, we’ll gladly send you a new one.
        <br><br>
        Enter the code below to complete your registration.`,
    },
    login_unverified: {
        heading: 'Account Not Verified',
        message: email => `Welcome back! To proceed, please verify your account. We've sent a <strong>6-digit OTP</strong> to <strong>${email}</strong>.
        <br><br>
        Enter the code below to complete your verification and log in.`,
    },
    forgot_password: {
        heading: 'Reset Your Password',
        message: email => `We’ve sent a <strong>6-digit verification code</strong> to <strong>${email}</strong>.
        <br><br>
        Enter the code below to reset your password and regain access to your account.`,
    },
    locked_account: {
        heading: 'Account Temporarily Locked',
        message: email => `<strong>Account Temporarily Locked.</strong> For your security, the account associated with <strong>${email}</strong> has been temporarily locked.
        <br><br>
        To regain access and set a new password, we’ve sent a one-time verification code to your email. Please enter it below to proceed.`,
    },
};
</script>



<template>
    <Head title="Verify OTP" />
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
                    {{ contextMessages[normalizedContext]?.heading || 'Verify Your Email' }}
                </h2>
            </div>

            <!-- Description -->
            <div class="text-center text-gray-600">
                <div v-html="contextMessages[normalizedContext]?.message(props.email) || `Welcome! To activate your account, please verify your email address. We've sent a <strong>verification code</strong> to <strong>${email}</strong>.
                    Please check your inbox (and spam folder). If you haven't received the email, we'll gladly send you a new one.
                    <br><br>
                    Enter the code below to complete your registration.`" />
            </div>

            <!-- Success Message -->
            <div v-if="otpResent" class="p-4 bg-green-100 text-green-700 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                A new OTP has been sent to the email address you provided.
            </div>

            <!-- OTP Verification Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="otp" value="Verification Code" />
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-green-600"></i>
                        </div>
                        <TextInput
                            id="otp"
                            type="text"
                            inputmode="numeric"
                            pattern="\d{6}"
                            class="pl-10 block w-full text-center text-xl tracking-[0.5em]"
                            v-model="form.otp"
                            required
                            autofocus
                            placeholder="000000"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.otp" />
                </div>

                <div class="flex items-center justify-between">
                    <button
                        type="button"
                        @click="resendOtp"
                        class="text-sm text-green-600 hover:text-green-800 font-medium"
                    >
                        <i class="fas fa-redo mr-1"></i>
                        Didn't get the code? Resend
                    </button>
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="px-6 py-2"
                    >
                        <i class="fas fa-check mr-2"></i>
                        {{ form.processing ? 'Verifying...' : 'Verify OTP' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
