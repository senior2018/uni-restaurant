<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>


    <Head title="Welcome to Uni Restaurant" />

    <div class="min-h-screen bg-gradient-to-b from-green-50 to-white">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-green-600">
                            <i class="fas fa-utensils mr-2"></i>UniRestaurant
                        </span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link v-if="canLogin"
                            :href="route('login')"
                            class="px-4 py-2 text-green-600 hover:text-green-800 transition">
                            Login
                        </Link>
                        <Link v-if="canRegister"
                            :href="route('register')"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                            Register
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <!-- <div class="py-20 text-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-6">
                    Campus Dining Made Easy
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Order from your favorite campus restaurants and get meals delivered to your location
                </p>
                <div class="flex justify-center space-x-4">
                    <Link :href="route('login')"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Order Now
                    </Link> -->
                    <!-- <Link :href="route('menu')"
                        class="px-6 py-3 border-2 border-green-600 text-green-600 rounded-lg hover:bg-green-50 transition">
                        View Menu
                    </Link> -->
                <!-- </div>
            </div>
        </div> -->
    </div>
</template>
