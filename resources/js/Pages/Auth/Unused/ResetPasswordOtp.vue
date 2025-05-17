<!-- <template>
    <AuthLayout>
      <Head title="Reset Password" />

      <div class="mb-4 text-sm text-gray-600">
        Please set a new password for your account.
      </div>

      <form @submit.prevent="submit">
        <div class="mt-4">
          <InputLabel for="password" value="New Password" />
          <TextInput
            id="password"
            v-model="form.password"
            type="password"
            class="mt-1 block w-full"
            required
            autocomplete="new-password"
          />
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
          <InputLabel for="password_confirmation" value="Confirm Password" />
          <TextInput
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            class="mt-1 block w-full"
            required
            autocomplete="new-password"
          />
          <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>

        <div class="flex items-center justify-end mt-4">
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Reset Password
          </PrimaryButton>
        </div>
      </form>
    </AuthLayout>
  </template>

  <script setup>
  import AuthLayout from '@/Layouts/AuthLayout.vue';
  import { Head, useForm } from '@inertiajs/vue3';

  const form = useForm({
    password: '',
    password_confirmation: '',
  });

  const submit = () => {
    form.post(route('password.update'), {
      preserveScroll: true,
      onSuccess: () => form.reset(),
    });
  };
  </script> -->
