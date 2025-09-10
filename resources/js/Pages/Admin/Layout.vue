<script setup>
import { computed, ref, watch } from 'vue';
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

// Flash message auto-dismiss logic
const showSuccess = ref(!!flash.value.success);
watch(
  () => flash.value.success,
  (val) => {
    showSuccess.value = !!val;
    if (val) {
      setTimeout(() => { showSuccess.value = false; }, 10000);
    }
  },
  { immediate: true }
);
</script>

<template>
    <BaseLayout title="Admin Dashboard">
        <TopNavBar :links="navLinks" :user="page.props.user" role="admin" brand="Admin Panel" />
        <!-- Flash Message + Main Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Flash messages -->
                <div v-if="showSuccess && flash.success" class="mb-4">
                  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative transition-opacity duration-500" role="alert">
                    <span class="block sm:inline">{{ flash.success }}</span>
                  </div>
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
