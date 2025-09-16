<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ResponsiveNavbar from '@/Components/ResponsiveNavbar.vue';
import BackgroundContainer from '@/Components/UI/BackgroundContainer.vue';
import HeroSection from '@/Components/UI/HeroSection.vue';
import ActionButtonGroup from '@/Components/UI/ActionButtonGroup.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome to Our Restaurant" />

    <BackgroundContainer variant="primary" min-height="screen">
        <!-- Navigation -->
        <ResponsiveNavbar :can-login="canLogin" :can-register="canRegister" />

        <!-- Hero Section -->
        <HeroSection
            title="Campus Dining Made Simple"
            subtitle="Order fresh meals from your university restaurant, track your orders in real-time, and enjoy convenient delivery to your location — whether you're a student, lecturer, or staff."
            size="lg"
        >
            <template #actions>
                <ActionButtonGroup layout="horizontal" alignment="center" spacing="md">
                    <Link :href="route('login')"
                        class="px-4 py-2 sm:px-6 sm:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm sm:text-base">
                        Order Now
                    </Link>
                    <Link :href="route('register')"
                        class="px-4 py-2 sm:px-6 sm:py-3 border-2 border-green-600 text-green-600 rounded-lg hover:bg-green-50 transition text-sm sm:text-base">
                        Create Account
                    </Link>
                    <Link :href="route('contact')"
                        class="px-4 py-2 sm:px-6 sm:py-3 border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition flex items-center gap-2 text-sm sm:text-base">
                        <i class="fas fa-headset"></i> Contact Support
                    </Link>
                </ActionButtonGroup>
            </template>
        </HeroSection>

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
    </BackgroundContainer>
</template>
