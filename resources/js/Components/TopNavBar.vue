<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    links: Array, // [{ name, route, icon }]
    user: Object,
    role: String, // 'admin', 'staff', 'customer'
    brand: { type: String, default: 'Uni Restaurant' },
});

const showDropdown = ref(false);
const initials = computed(() => {
    if (!props.user || !props.user.name) return '';
    return props.user.name.split(' ').map(n => n[0]).join('').toUpperCase();
});

function logout() {
    router.post(route('logout'));
}

const colorClass = computed(() => {
    switch (props.role) {
        case 'admin': return 'text-purple-700';
        case 'staff': return 'text-blue-700';
        case 'customer': return 'text-green-700';
        default: return 'text-gray-700';
    }
});
const bgClass = computed(() => {
    switch (props.role) {
        case 'admin': return 'bg-purple-100';
        case 'staff': return 'bg-blue-100';
        case 'customer': return 'bg-green-100';
        default: return 'bg-gray-100';
    }
});
</script>

<template>
    <nav class="bg-white shadow sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo/App Name -->
                <div class="flex items-center gap-4">
                    <span :class="['text-2xl font-bold tracking-tight', colorClass]">
                        <slot name="brand">
                            <i class="fas fa-leaf mr-2"></i> {{ brand }}
                        </slot>
                    </span>
                </div>
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center gap-6">
                    <Link v-for="link in links" :key="link.name" :href="link.route"
                          :class="[isActive(link.route) ? colorClass + ' font-bold' : 'text-gray-600 hover:' + colorClass, 'flex items-center gap-2 transition-colors relative']">
                        <i :class="link.icon"></i>
                        {{ link.name }}
                        <span v-if="link.badge && link.badge > 0"
                              class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                          {{ link.badge }}
                        </span>
                    </Link>
                </div>
                <!-- User Avatar/Dropdown -->
                <div class="relative flex items-center">
                    <button @click="showDropdown = !showDropdown" class="flex items-center gap-2 focus:outline-none">
                        <span :class="['inline-flex items-center justify-center w-10 h-10 rounded-full font-bold text-lg', bgClass, colorClass]">
                            {{ initials }}
                        </span>
                        <span class="hidden md:inline text-gray-800 font-medium">{{ user?.name || 'User' }}</span>
                        <i class="fas fa-chevron-down ml-1 text-gray-500"></i>
                    </button>
                    <div v-if="showDropdown" class="absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-50">
                        <Link :href="route('profile.edit')" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</Link>
                        <button @click="logout" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">Logout</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    methods: {
        isActive(href) {
            return window.location.pathname === href;
        }
    }
}
</script>

<style scoped>
nav { box-shadow: 0 2px 8px 0 rgba(0,0,0,0.03); }
</style>
