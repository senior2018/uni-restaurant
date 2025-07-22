<script setup>
import { computed } from 'vue';
import BaseLayout from "../Shared/BaseLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import TopNavBar from '@/Components/TopNavBar.vue';

const page = usePage();

const flash = computed(() => page.props.flash || {});

const unseenCancellationCount = page.props.unseenCancellationCount || 0;

const navLinks = [
    { name: 'Dashboard', route: route('dashboard'), icon: 'fas fa-user-shield' },
    { name: 'Meal Management', route: route('meals.index'), icon: 'fas fa-utensils' },
    { name: 'Meal Categories', route: route('meal-categories.index'), icon: 'fas fa-list' },
    { name: 'Orders', route: route('admin.orders.index'), icon: 'fas fa-receipt', badge: unseenCancellationCount },
    { name: 'Requests', route: route('admin.pendingCancellations'), icon: 'fas fa-question-circle', badge: unseenCancellationCount },
    { name: 'Alerts', route: route('admin.alerts.index'), icon: 'fas fa-exclamation-triangle', badge: page.props.unresolvedAlertCount || 0 },
    { name: 'Support Tickets', route: route('admin.support-tickets.index'), icon: 'fas fa-envelope-open-text' },
];
</script>

<template>
    <BaseLayout title="Admin Dashboard">
        <TopNavBar :links="navLinks" :user="page.props.user" role="admin" brand="Admin Panel" />
        <!-- Flash Message + Main Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Flash messages -->
                <div v-if="flash.success" class="bg-primary-light text-primary-dark p-2 mb-4 rounded">
                    {{ flash.success }}
                </div>
                <div v-if="flash.error" class="bg-accent-pink text-white p-2 mb-4 rounded">
                    {{ flash.error }}
                </div>

                <!-- Main slot content -->
                <slot />
            </div>
        </main>
    </BaseLayout>
</template>
