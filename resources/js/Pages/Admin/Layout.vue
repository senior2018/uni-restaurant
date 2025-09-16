<script setup>
import { computed, ref, watch } from 'vue';
import BaseLayout from "../Shared/BaseLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import TopNavBar from '@/Components/TopNavBar.vue';
import FlashMessage from '@/Components/UI/FlashMessage.vue';
import PageContainer from '@/Components/UI/PageContainer.vue';

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
    { name: 'Reports', route: route('admin.reports.index'), icon: 'fas fa-chart-line' },
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
        <!-- Flash Messages -->
        <FlashMessage
            v-if="showSuccess && flash.success"
            type="success"
            :message="flash.success"
            @close="showSuccess = false"
        />
        <FlashMessage
            v-if="flash.error"
            type="error"
            :message="flash.error"
            @close="flash.error = null"
        />

        <!-- Main Content -->
        <PageContainer background="gray" padding="responsive">
            <slot />
        </PageContainer>
    </BaseLayout>
</template>
