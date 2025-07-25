<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
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

const page = usePage();
const notifications = ref(page.props.notifications || []);
const showNotifDropdown = ref(false);

const groupedNotifications = computed(() => {
  const groups = {};
  notifications.value.forEach(n => {
    if (!groups[n.type]) groups[n.type] = [];
    groups[n.type].push(n);
  });
  return groups;
});

// Add a computed for unread support ticket responses
const unreadSupportReplies = computed(() => notifications.value.filter(n => n.type === 'support_ticket_response'));

function iconClass(type) {
  switch (type) {
    case 'alert': return 'fas fa-exclamation-triangle text-yellow-500';
    case 'alert_response': return 'fas fa-reply text-blue-500';
    case 'rating': return 'fas fa-star text-yellow-400';
    case 'rating_response': return 'fas fa-comment-dots text-green-500';
    case 'support_ticket_response': return 'fas fa-headset text-blue-600';
    default: return 'fas fa-bell text-gray-400';
  }
}
function typeLabel(type) {
  switch (type) {
    case 'alert': return 'Alerts';
    case 'alert_response': return 'Alert Responses';
    case 'rating': return 'Ratings';
    case 'rating_response': return 'Rating Responses';
    case 'support_ticket_response': return 'Support Replies';
    default: return 'Other';
  }
}

function logout() {
    router.post(route('logout'));
}

function markNotificationRead(id) {
    router.post(route('notifications.read', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            showNotifDropdown.value = false;
        }
    });
}

function handleNotificationClick(notif) {
  router.post(route('notifications.read', notif.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      notifications.value = notifications.value.filter(n => n.id !== notif.id);
      if (notif.data.url) {
        router.visit(notif.data.url);
      }
    }
  });
}

function markAllAsRead() {
  const ids = notifications.value.map(n => n.id);
  if (!ids.length) return;
  router.post(route('notifications.read'), { ids }, {
    preserveScroll: true,
    onSuccess: () => {
      notifications.value = [];
    }
  });
}

// Use green as the main color for all roles
const colorClass = 'text-primary-dark';
const bgClass = 'bg-primary-light';

const isActive = (href) => {
    return window.location.pathname === href;
};

</script>

<template>
    <nav class="bg-white shadow sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo/App Name -->
                <div class="flex items-center gap-4">
                    <span class="text-2xl font-bold tracking-tight text-primary-dark">
                        <slot name="brand">
                            <i class="fas fa-leaf mr-2 text-primary"></i> {{ brand }}
                        </slot>
                    </span>
                </div>
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center gap-6">
                    <Link v-for="link in links" :key="link.name" :href="link.route"
                          :class="[isActive(link.route) ? 'text-primary-dark font-bold' : 'text-gray-600 hover:text-primary-dark', 'flex items-center gap-2 transition-colors relative']">
                        <i :class="link.icon"></i>
                        {{ link.name }}
                        <span v-if="link.name === 'Contact Support' && unreadSupportReplies.length > 0"
                              class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                          {{ unreadSupportReplies.length }}
                        </span>
                        <span v-else-if="link.badge && link.badge > 0"
                              class="absolute -top-2 -right-2 bg-accent-pink text-white text-xs font-bold rounded-full px-2 py-0.5">
                          {{ link.badge }}
                        </span>
                    </Link>
                    <!-- Notification Bell -->
                    <div class="relative">
                        <button @click="showNotifDropdown = !showNotifDropdown" class="relative focus:outline-none">
                            <span class="font-semibold text-gray-700">Notifications</span>
                            <span v-if="notifications.length" class="absolute -top-2 -right-2 bg-primary text-white text-xs font-bold rounded-full px-2 py-0.5">{{ notifications.length }}</span>
                        </button>
                        <div v-if="showNotifDropdown" class="absolute right-0 mt-2 w-80 bg-white border rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
                            <div class="p-4 border-b font-bold text-primary-dark flex items-center justify-between">
                                <span>Notifications</span>
                                <button v-if="notifications.length" @click.stop="markAllAsRead" class="text-xs text-blue-600 hover:underline">Mark all as read</button>
                            </div>
                            <div v-if="!notifications.length" class="p-4 text-gray-500">No new notifications.</div>
                            <div v-else>
                                <div v-for="(group, type) in groupedNotifications" :key="type">
                                    <div class="px-4 py-2 text-xs font-bold text-gray-500 uppercase">{{ typeLabel(type) }}</div>
                                    <div v-for="notif in group" :key="notif.id"
                                         :class="['p-4 border-b last:border-b-0 flex flex-col gap-1 cursor-pointer', notif.read_at ? 'bg-white' : 'bg-blue-50 font-bold']"
                                         @click="handleNotificationClick(notif)">
                                        <div class="flex items-center gap-2">
                                            <i :class="iconClass(notif.type)"></i>
                                            <span>{{ notif.data.title || notif.type }}</span>
                                            <span class="text-xs text-gray-400 ml-auto">{{ notif.created_at }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600">{{ notif.data.message || notif.data.reason || notif.data.comment }}</div>
                                        <div v-if="notif.data.url" class="text-xs text-blue-600 underline">Go to page</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- User Avatar/Dropdown -->
                <div class="relative flex items-center">
                    <button @click="showDropdown = !showDropdown" class="flex items-center gap-2 focus:outline-none">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full font-bold text-lg bg-primary-light text-primary-dark">
                            {{ initials }}
                        </span>
                        <span class="hidden md:inline text-gray-800 font-medium">{{ user?.name || 'User' }}</span>
                        <i class="fas fa-chevron-down ml-1 text-gray-500"></i>
                    </button>
                    <div v-if="showDropdown" class="absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-50">
                        <Link :href="route('profile.edit')" class="block px-4 py-2 text-gray-700 hover:bg-primary-light">Profile</Link>
                        <button @click="logout" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">Logout</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<style scoped>
nav { box-shadow: 0 2px 8px 0 rgba(0,0,0,0.03); }
</style>
