<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">List of Admins</h1>
        <ul class="space-y-2">
            <li v-for="admin in admins" :key="admin.id" class="p-4 bg-white rounded shadow">
                <strong>{{ admin.name }}</strong> ({{ admin.email }}) – Joined {{ admin.created_at }}

            </li>
        </ul>
    </div>
</template>

<script setup>
defineProps({
    admins: Array
});
</script>
