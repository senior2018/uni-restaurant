<!-- For unauthenticated users (login/register pages) -->
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BackgroundContainer from '@/Components/UI/BackgroundContainer.vue';
</script>

<template>
    <Head title="Our Reustarant" />

    <BackgroundContainer variant="primary" min-height="screen">
        <!-- Simple guest navigation -->
        <nav class="bg-white shadow">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <Link :href="route('home')" class="text-lg sm:text-xl font-semibold text-gray-800">
                            Reustarant
                        </Link>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <Link :href="route('login')" class="text-sm sm:text-base text-gray-600 hover:text-green-600 px-2 py-1 sm:px-3 sm:py-2 rounded-md hover:bg-gray-50 transition-colors">
                            Login
                        </Link>
                        <Link :href="route('register')" class="text-sm sm:text-base text-gray-600 hover:text-green-600 px-2 py-1 sm:px-3 sm:py-2 rounded-md hover:bg-gray-50 transition-colors">
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
    </BackgroundContainer>
</template>
