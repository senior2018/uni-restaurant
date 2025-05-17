<!-- <template>
    <AuthLayout>
      <Head title="Verify OTP" />

      <div class="mb-4 text-sm text-gray-600">
        We've sent an OTP to {{ email }}. Please enter the 6-digit code.
      </div>

      <form @submit.prevent="submit">
        <InputLabel for="otp" value="Verification Code" />
        <TextInput
          id="otp"
          v-model="form.otp"
          type="text"
          inputmode="numeric"
          pattern="\d{6}"
          class="mt-1 block w-full"
          required
          autofocus
        />
        <InputError class="mt-2" :message="form.errors.otp" />

        <div class="flex items-center justify-end mt-4">
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Verify OTP
          </PrimaryButton>
        </div>
      </form>
    </AuthLayout>
  </template>

  <script setup>
  import AuthLayout from '@/Layouts/AuthLayout.vue';
  import { Head, useForm } from '@inertiajs/vue3';

  const props = defineProps({
    email: String,
    status: String,
  });

  const form = useForm({
    email: props.email,
    otp: '',
  });

  const submit = () => {
    form.post(route('password.verify.post'), {
      preserveScroll: true,
    });
  };
  </script> -->
