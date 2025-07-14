<template>
    <AdminLayout>
        <div class="p-6 bg-gray-50 min-h-screen">
            <div class="flex justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Meal Management</h1>
                <Link
                    :href="route('meal-categories.index')"
                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200"
                >
                    Manage Categories
                </Link>
            </div>

            <!-- Form for adding new meals -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <MealForm
                    :categories="categories"
                    @refresh="fetchMeals"
                />
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search Bar -->
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search meals by name or category..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <!-- Clear Search Button -->
                            <button
                                v-if="searchQuery"
                                @click="searchQuery = ''"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Status Filter (Updated) -->
                    <div class="flex-shrink-0">
                        <select
                            v-model="statusFilter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="all">All Items</option>
                            <option value="available">Available Only</option>
                            <option value="unavailable">Unavailable Only</option>
                            <option value="deleted">Deleted Only</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="flex-shrink-0">
                        <select
                            v-model="categoryFilter"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="all">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Clear Filters Button -->
                    <button
                        v-if="searchQuery || statusFilter !== 'all' || categoryFilter !== 'all'"
                        @click="clearAllFilters"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Clear Filters
                    </button>
                </div>

                <!-- Active Filters Display -->
                <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
                    <span class="text-sm text-gray-600">Active filters:</span>
                    <span v-if="searchQuery" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Search: "{{ searchQuery }}"
                        <button @click="searchQuery = ''" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                    </span>
                    <span v-if="statusFilter !== 'all'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ getStatusFilterName(statusFilter) }}
                        <button @click="statusFilter = 'all'" class="ml-1 text-green-600 hover:text-green-800">×</button>
                    </span>
                    <span v-if="categoryFilter !== 'all'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        {{ getCategoryName(categoryFilter) }}
                        <button @click="categoryFilter = 'all'" class="ml-1 text-purple-600 hover:text-purple-800">×</button>
                    </span>
                </div>
            </div>

            <!-- Card Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div
                    v-for="meal in filteredMeals"
                    :key="meal.id"
                    :class="[
                        'bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300',
                        meal.deleted_at
                            ? 'opacity-70 border-2 border-red-200 bg-red-50'
                            : 'hover:shadow-xl transform hover:-translate-y-2'
                    ]"
                >
                    <!-- Image Section -->
                    <div class="relative">
                        <img
                            :src="meal.image_url || '/placeholder.jpg'"
                            :alt="meal.name"
                            class="w-full h-48 object-cover"
                            :class="{ 'grayscale': meal.deleted_at }"
                        />
                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3">
                            <span
                                v-if="meal.deleted_at"
                                class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg"
                            >
                                Deleted
                            </span>
                            <span
                                v-else
                                :class="
                                    meal.is_available
                                        ? 'bg-green-500 text-white'
                                        : 'bg-orange-500 text-white'
                                "
                                class="px-3 py-1 rounded-full text-xs font-semibold shadow-lg"
                            >
                                {{ meal.is_available ? "Available" : "Unavailable" }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="p-5">
                        <!-- Meal Name -->
                        <h3 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2">
                            {{ meal.name }}
                            <span v-if="meal.deleted_at" class="text-red-500 text-sm font-normal">(Deleted)</span>
                        </h3>

                        <!-- Category -->
                        <p class="text-gray-600 text-sm mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            {{ meal.category.name }}
                        </p>

                        <!-- Price -->
                        <!-- <div class="mb-4">
                            <span class="text-2xl font-bold text-green-600">
                                ${{ Number(meal.price).toFixed(2) }}
                            </span>
                        </div> -->
                        <div class="mb-4">
                            <span class="text-2xl font-bold text-green-600">
                                {{ Number(meal.price).toLocaleString('sw-TZ', {
                                    style: 'currency',
                                    currency: 'TZS'
                                }) }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <!-- Normal meal actions (not deleted) -->
                            <template v-if="!meal.deleted_at">
                                <!-- Toggle Button -->
                                <button
                                    @click="toggleAvailability(meal)"
                                    :class="meal.is_available
                                        ? 'bg-orange-100 text-orange-700 hover:bg-orange-200'
                                        : 'bg-green-100 text-green-700 hover:bg-green-200'"
                                    class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200"
                                >
                                    {{ meal.is_available ? 'Make Unavailable' : 'Make Available' }}
                                </button>

                                <!-- Edit Button -->
                                <div class="flex-shrink-0">
                                    <MealForm
                                        :meal="meal"
                                        :categories="categories"
                                        @refresh="fetchMeals"
                                    />
                                </div>

                                <!-- Delete Button -->
                                <button
                                    @click="deleteMeal(meal)"
                                    class="bg-red-100 text-red-600 hover:bg-red-200 p-2 rounded-lg transition-colors duration-200 flex-shrink-0"
                                    title="Move to Trash"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </template>

                            <!-- Deleted meal actions -->
                            <template v-else>
                                <!-- Restore Button -->
                                <button
                                    @click="restoreMeal(meal)"
                                    class="flex-1 py-2 px-3 rounded-lg text-sm font-medium bg-green-100 text-green-700 hover:bg-green-200 transition-colors duration-200 flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Restore
                                </button>

                                <!-- Permanent Delete Button -->
                                <button
                                    @click="permanentDelete(meal)"
                                    class="bg-red-500 text-white hover:bg-red-600 p-2 rounded-lg transition-colors duration-200 flex-shrink-0"
                                    title="Delete Forever"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="filteredMeals.length === 0 && meals.length > 0"
                    class="col-span-full flex flex-col items-center justify-center py-16 text-gray-500"
                >
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium mb-2">No meals match your filters</h3>
                    <p class="text-sm mb-4">Try adjusting your search or filter criteria.</p>
                    <button
                        @click="clearAllFilters"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                    >
                        Clear All Filters
                    </button>
                </div>

                <!-- No Meals State -->
                <div
                    v-else-if="meals.length === 0"
                    class="col-span-full flex flex-col items-center justify-center py-16 text-gray-500"
                >
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="text-lg font-medium mb-2">No meals found</h3>
                    <p class="text-sm">Add your first meal using the form above.</p>
                </div>
            </div>

            <!-- Meals Count -->
            <div class="mt-8 text-center text-gray-600">
                <p class="text-sm">
                    <span v-if="filteredMeals.length !== meals.length">
                        Showing {{ filteredMeals.length }} of {{ meals.length }} meals
                    </span>
                    <span v-else>
                        Showing {{ meals.length }} meal{{ meals.length !== 1 ? 's' : '' }}
                    </span>
                    <span v-if="filteredMeals.length > 0 && statusFilter !== 'deleted'">
                        • {{ filteredMeals.filter(meal => meal.is_available && !meal.deleted_at).length }} available
                        • {{ filteredMeals.filter(meal => !meal.is_available && !meal.deleted_at).length }} unavailable
                    </span>
                    <span v-if="statusFilter === 'deleted'">
                        • {{ filteredMeals.length }} deleted
                    </span>
                    <span v-else-if="statusFilter === 'all' && meals.filter(meal => meal.deleted_at).length > 0">
                        • {{ meals.filter(meal => meal.deleted_at).length }} deleted
                    </span>
                </p>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import AdminLayout from "@/Pages/Admin/Layout.vue";
import MealForm from "./MealForm.vue";

const page = usePage();

const props = defineProps({
    meals: Array,
    categories: Array,
});

// Use computed to always get fresh data from Inertia
const meals = computed(() => page.props.meals);

// Filter states (renamed availabilityFilter to statusFilter)
const searchQuery = ref('');
const statusFilter = ref('all');
const categoryFilter = ref('all');

// Computed filtered meals
const filteredMeals = computed(() => {
    let filtered = meals.value;

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(meal =>
            meal.name.toLowerCase().includes(query) ||
            meal.category.name.toLowerCase().includes(query)
        );
    }

    // Status filter (updated logic)
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(meal => {
            switch(statusFilter.value) {
                case 'available':
                    return meal.is_available && !meal.deleted_at;
                case 'unavailable':
                    return !meal.is_available && !meal.deleted_at;
                case 'deleted':
                    return meal.deleted_at !== null;
                default:
                    return true;
            }
        });
    }

    // Category filter
    if (categoryFilter.value !== 'all') {
        filtered = filtered.filter(meal => meal.category.id == categoryFilter.value);
    }

    return filtered;
});

// Check if any filters are active
const hasActiveFilters = computed(() => {
    return searchQuery.value || statusFilter.value !== 'all' || categoryFilter.value !== 'all';
});

// Helper function to get category name
const getCategoryName = (categoryId) => {
    const category = props.categories.find(cat => cat.id == categoryId);
    return category ? category.name : '';
};

// Helper function to get status filter name
const getStatusFilterName = (status) => {
    const statusNames = {
        'available': 'Available',
        'unavailable': 'Unavailable',
        'deleted': 'Deleted'
    };
    return statusNames[status] || status;
};

// Clear all filters
const clearAllFilters = () => {
    searchQuery.value = '';
    statusFilter.value = 'all';
    categoryFilter.value = 'all';
};

const fetchMeals = () => {
    router.reload({
        only: ['meals', 'flash'],
        preserveScroll: true
    });
};

const toggleAvailability = (meal) => {
    router.post(route("meals.toggle", meal.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['meals'] });
        }
    });
};

const deleteMeal = (meal) => {
    if (!confirm(`Are you sure you want to move "${meal.name}" to trash? You can restore it later.`)) {
        return;
    }

    router.delete(route('meals.destroy', meal.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Meal moved to trash successfully');
        },
        onError: (errors) => {
            console.error('Error moving meal to trash:', errors);
            alert('An error occurred while moving the meal to trash. Please try again.');
        }
    });
};

const restoreMeal = (meal) => {
    if (!confirm(`Are you sure you want to restore "${meal.name}"?`)) {
        return;
    }

    router.post(route('meals.restore', meal.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Meal restored successfully');
        },
        onError: (errors) => {
            console.error('Error restoring meal:', errors);
            alert('An error occurred while restoring the meal. Please try again.');
        }
    });
};

const permanentDelete = (meal) => {
    if (!confirm(`Are you sure you want to PERMANENTLY delete "${meal.name}"? This action cannot be undone!`)) {
        return;
    }

    router.delete(route('meals.force-delete', meal.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Meal permanently deleted');
        },
        onError: (errors) => {
            console.error('Error permanently deleting meal:', errors);
            alert('An error occurred while permanently deleting the meal. Please try again.');
        }
    });
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-clamp: 2;
}
</style>
