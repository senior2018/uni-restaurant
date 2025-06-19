<script setup>
import { computed } from 'vue';
import BaseLayout from "../Shared/BaseLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();

const flash = computed(() => {
    return page.props.flash || {};
});
</script>

<template>
    <BaseLayout title="Admin Dashboard">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Left Side: Navigation Links -->
                    <div class="flex items-center space-x-4">
                        <!-- Dashboard Link -->
                        <NavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            <i
                                class="fas fa-user-shield text-purple-600 mr-2"
                            ></i>
                            Dashboard
                        </NavLink>

                        <!-- Meal Management Link -->
                        <NavLink
                            :href="route('meals.index')"
                            :active="route().current('meals.index')"
                        >
                            <i class="fas fa-utensils text-green-600 mr-2"></i>
                            Meal Management
                        </NavLink>

                        <!-- Meal Categories Link -->
                        <NavLink
                            :href="route('meal-categories.index')"
                            :active="route().current('meal-categories.index')"
                        >
                            <i class="fas fa-list text-blue-600 mr-2"></i>
                            Meal Categories
                        </NavLink>
                    </div>

                    <!-- Right Side: Profile + Logout -->
                    <div class="flex items-center space-x-6">
                        <Link
                            :href="route('profile.edit')"
                            class="text-gray-600 hover:text-green-600"
                        >
                            Profile
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            class="text-red-600 hover:text-red-800"
                        >
                            Logout
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Message + Main Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Flash messages -->
                <div v-if="flash.success" class="bg-green-100 text-green-800 p-2 mb-4 rounded">
                    {{ flash.success }}
                </div>
                <div v-if="flash.error" class="bg-red-100 text-red-800 p-2 mb-4 rounded">
                    {{ flash.error }}
                </div>

                <!-- Main slot content -->
                <slot />
            </div>
        </main>
    </BaseLayout>
</template>
