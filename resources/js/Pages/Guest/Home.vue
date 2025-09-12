<script setup>
import { Head } from '@inertiajs/vue3';
import ResponsiveNavbar from '@/Components/ResponsiveNavbar.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome to Our Restaurant" />

    <div class="min-h-screen" style="background-color: #ECFDF5;">
        <!-- Navigation -->
        <ResponsiveNavbar :can-login="canLogin" :can-register="canRegister" />

        <!-- Hero Section -->
        <section class="pt-32 pb-20 text-center">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-5xl font-extrabold text-gray-900 mb-6 leading-tight">
                    Campus Dining Made Simple
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    Order fresh meals from your university restaurant, track your orders in real-time,
                    and enjoy convenient delivery to your location — whether you're a student, lecturer, or staff.
                </p>
                <div class="flex justify-center space-x-4">
                    <Link :href="route('login')"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Order Now
                    </Link>
                    <Link :href="route('register')"
                        class="px-6 py-3 border-2 border-green-600 text-green-600 rounded-lg hover:bg-green-50 transition">
                        Create Account
                    </Link>
                    <Link :href="route('contact')"
                        class="px-6 py-3 border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition flex items-center gap-2">
                        <i class="fas fa-headset"></i> Contact Support
                    </Link>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="bg-white py-12">
            <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <i class="fas fa-hamburger text-green-500 text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Diverse Meal Options</h3>
                    <p class="text-gray-600">Choose from a wide range of main meals, sides, and drinks curated daily for campus needs.</p>
                </div>
                <div>
                    <i class="fas fa-clock text-green-500 text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Fast & Timely Delivery</h3>
                    <p class="text-gray-600">We deliver to your faculty, office, or hall with real-time tracking and alerts for delays.</p>
                </div>
                <div>
                    <i class="fas fa-star text-green-500 text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Rate & Review</h3>
                    <p class="text-gray-600">Share feedback on meals and service quality to help us serve you better every day.</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="text-center py-6 text-sm text-gray-500">
            &copy; {{ new Date().getFullYear() }} Our Restaurant. Built for Campus Life.
        </footer>
    </div>
</template>
