<!-- For unauthenticated users (login/register pages) -->
<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Our Reustarant" />

    <div class="min-h-screen bg-gray-50">
        <!-- Simple guest navigation -->
        <nav class="bg-white shadow">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <Link :href="route('home')" class="text-xl font-semibold text-gray-800">
                            Reustarant
                        </Link>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link :href="route('login')" class="text-gray-600 hover:text-green-600">
                            Login
                        </Link>
                        <Link :href="route('register')" class="text-gray-600 hover:text-green-600">
                            Register
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content slot -->
        <main>
            <slot />
        </main>
    </div>
</template>
