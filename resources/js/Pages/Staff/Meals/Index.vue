<template>
    <StaffLayout :user="user">
        <div class="p-6 bg-gray-50 min-h-screen">
            <!-- Flash Message Display -->
            <div v-if="page.props.flash?.success" class="mb-6">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ page.props.flash.success }}</span>
                    </div>
                </div>
            </div>

            <div v-if="page.props.flash?.error" class="mb-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ page.props.flash.error }}</span>
                    </div>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                Meal Availability Management
            </h1>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search Bar -->
                    <div class="flex-1">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search meals..."
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    <!-- Status Filter -->
                    <div class="flex-shrink-0">
                        <select
                            v-model="statusFilter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="all">All Items</option>
                            <option value="available">Unavailable Only</option>
                            <option value="unavailable">Available Only</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <i class="fas fa-utensils text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Meals</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ meals.length }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Available</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ availableMeals }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                            <i class="fas fa-times-circle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Unavailable</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ unavailableMeals }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No meals message -->
            <div v-if="filteredMeals.length === 0" class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No meals found</h3>
                <p class="text-gray-500">Try adjusting your search or filter criteria.</p>
            </div>

            <!-- Card Grid Layout -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="meal in filteredMeals"
                    :key="meal.id"
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300"
                >
                    <!-- Image Section -->
                    <div class="relative">
                        <img
                            :src="meal.image_url || '/placeholder.jpg'"
                            :alt="meal.name"
                            class="w-full h-48 object-cover"
                        />
                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            <span
                                :class="
                                    meal.is_available
                                        ? 'bg-green-500'
                                        : 'bg-orange-500'
                                "
                                class="px-3 py-1 rounded-full text-xs font-semibold text-white shadow-lg"
                            >
                                {{ meal.is_available ? "Available" : "Unavailable" }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">
                            {{ meal.name }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-3">
                            {{ meal.category.name }}
                        </p>
                        <div class="mb-4">
                            <span class="text-2xl font-bold text-green-600">
                                {{ formatPrice(meal.price) }}
                            </span>
                        </div>

                        <!-- Toggle Button -->
                        <button
                            @click="toggleAvailability(meal)"
                            :disabled="isToggling"
                            :class="
                                meal.is_available
                                    ? 'bg-orange-100 text-orange-700 hover:bg-orange-200 focus:ring-orange-500'
                                    : 'bg-green-100 text-green-700 hover:bg-green-200 focus:ring-green-500'
                            "
                            class="w-full py-2 px-3 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                        >
                            <span v-if="isToggling" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                            <span v-else>
                                {{ meal.is_available ? "Make Unavailable" : "Make Available" }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </StaffLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import StaffLayout from "@/Pages/Staff/Layout.vue";
import { usePage } from '@inertiajs/vue3';
const page = usePage();

const props = defineProps({
    meals: Array,
    categories: Array,
    permissions: Object,
    user: Object,
});

const searchQuery = ref("");
const statusFilter = ref("all");
const isToggling = ref(false);

// Computed properties
const filteredMeals = computed(() => {
    return props.meals.filter((meal) => {
        // Search filter
        const matchesSearch =
            searchQuery.value === "" ||
            meal.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            meal.category.name
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase());

        // Status filter
        const matchesStatus =
            statusFilter.value === "all" ||
            (statusFilter.value === "available" && meal.is_available) ||
            (statusFilter.value === "unavailable" && !meal.is_available);

        return matchesSearch && matchesStatus;
    });
});

const availableMeals = computed(() => {
    return props.meals.filter(meal => meal.is_available).length;
});

const unavailableMeals = computed(() => {
    return props.meals.filter(meal => !meal.is_available).length;
});

// Helper function for price formatting
const formatPrice = (price) => {
    return new Intl.NumberFormat("sw-TZ", {
        style: "currency",
        currency: "TZS",
    }).format(price);
};

const toggleAvailability = (meal) => {
    if (!props.permissions.canToggle) {
        alert('You do not have permission to toggle meal availability.');
        return;
    }

    isToggling.value = true;

    // Use the correct staff route
    router.post(
        route("staff.meals.toggle", meal.id),
        {},
        {
            preserveScroll: true,
            onSuccess: (page) => {
                // Flash message will now be displayed in the template automatically
                // No need for any additional code here
            },
            onError: (errors) => {
                console.error('Error toggling meal availability:', errors);
                alert('Failed to update meal availability. Please try again.');
            },
            onFinish: () => {
                isToggling.value = false;
            }
        }
    );
};
</script>
