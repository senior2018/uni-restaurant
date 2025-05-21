<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head :title="title" />
    <div class="min-h-screen bg-gray-50">
        <slot />
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            default: 'Dashboard'
        }
    }
}
</script>
