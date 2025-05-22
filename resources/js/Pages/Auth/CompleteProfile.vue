<template>
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-xl shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Complete Your Profile</h2>
    <form @submit.prevent="submit">
    <div class="mb-4">
        <label class="block font-medium">Phone</label>
        <input type="text" v-model="form.phone" class="w-full border px-3 py-2 rounded" />
        <div v-if="form.errors.phone" class="text-red-600 text-sm">{{ form.errors.phone }}</div>
    </div>
    <div class="mb-4">
        <label class="block font-medium">Permanent Location</label>
        <input type="text" v-model="form.permanent_location" class="w-full border px-3 py-2 rounded" />
        <div v-if="form.errors.permanent_location" class="text-red-600 text-sm">{{ form.errors.permanent_location }}</div>
    </div>
        <div class="mb-4">
        <label class="block font-medium">Password</label>
        <input type="password" v-model="form.password" class="w-full border px-3 py-2 rounded" />
        <div v-if="form.errors.password" class="text-red-600 text-sm">{{ form.errors.password }}</div>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Confirm Password</label>
        <input type="password" v-model="form.password_confirmation" class="w-full border px-3 py-2 rounded" />
        <div v-if="form.errors.password_confirmation" class="text-red-600 text-sm">{{ form.errors.password_confirmation }}</div>
    </div>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
        Save
        </button>
    </form>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({ user: Object })

const form = useForm({
    phone: props.user.phone || '',
    permanent_location: props.user.permanent_location || '',
    password: '',
    password_confirmation: ''
})

function submit() {
    form.post(route('complete-profile.update'))
}
</script>
