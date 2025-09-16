<script setup>
import BaseLayout from '../Shared/BaseLayout.vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watchEffect } from 'vue';
import TopNavBar from '@/Components/TopNavBar.vue';
import FlashMessage from '@/Components/UI/FlashMessage.vue';
import PageContainer from '@/Components/UI/PageContainer.vue';

const props = defineProps({
    user: Object,
});

const page = usePage();
const navLinks = [
    { name: 'Menu', route: route('menu.public'), icon: 'fas fa-utensils' },
    { name: 'My Plate', route: route('cart'), icon: 'fas fa-plate-wheat' },
    { name: 'My Orders', route: route('customer.orders'), icon: 'fas fa-receipt' },
    { name: 'Contact Support', route: route('contact'), icon: 'fas fa-headset' },
];

const isActive = (href) => {
    return window.location.pathname === href;
};

const showDropdown = ref(false);
const initials = computed(() => {
    if (!props.user?.name) return '';
    return props.user.name.split(' ').map(n => n[0]).join('').toUpperCase();
});

function logout() {
    router.post(route('logout'));
}

watchEffect(() => {
    if (page.props.flash?.success) {
        setTimeout(() => { page.props.flash.success = null; }, 10000);
    }
    if (page.props.flash?.error) {
        setTimeout(() => { page.props.flash.error = null; }, 10000);
    }
});
</script>

<template>
    <BaseLayout>
        <TopNavBar :links="navLinks" :user="user" role="customer" brand="Our Restaurant" />

        <!-- Flash Messages -->
        <FlashMessage
            v-if="page.props.flash?.success"
            type="success"
            :message="page.props.flash.success"
            @close="page.props.flash.success = null"
        />
        <FlashMessage
            v-if="page.props.flash?.error"
            type="error"
            :message="page.props.flash.error"
            @close="page.props.flash.error = null"
        />

        <!-- Main Content -->
        <PageContainer background="gray" padding="responsive">
            <slot />
        </PageContainer>
    </BaseLayout>
</template>

<style scoped>
nav { box-shadow: 0 2px 8px 0 rgba(0,0,0,0.03); }
</style>
