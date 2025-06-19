<template>
    <AdminLayout>
        <div class="p-6 bg-white rounded shadow">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Meal Categories</h1>
                <Link :href="route('meals.index')"
                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Meals
                </Link>
            </div>

            <!-- Success Message -->
            <div v-if="flash.success"
                    class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ flash.success }}
            </div>

            <!-- Error Message -->
            <div v-if="flash.error"
                    class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ flash.error }}
            </div>

            <!-- View Mode Toggle -->
            <div class="mb-6 flex space-x-2">
                <Link
                    :href="route('meal-categories.index')"
                    :class="{ 'bg-blue-500 text-white': !showingTrashed && !trashedOnly, 'bg-gray-100 text-gray-700': showingTrashed || trashedOnly }"
                    class="px-4 py-2 border rounded hover:bg-blue-100 transition-colors duration-200"
                >
                    <i class="fas fa-list mr-2"></i>Active Categories
                </Link>
                <Link
                    :href="route('meal-categories.with-trashed')"
                    :class="{ 'bg-blue-500 text-white': showingTrashed, 'bg-gray-100 text-gray-700': !showingTrashed }"
                    class="px-4 py-2 border rounded hover:bg-blue-100 transition-colors duration-200"
                >
                    <i class="fas fa-eye mr-2"></i>All Categories
                </Link>
                <Link
                    :href="route('meal-categories.trashed')"
                    :class="{ 'bg-blue-500 text-white': trashedOnly, 'bg-gray-100 text-gray-700': !trashedOnly }"
                    class="px-4 py-2 border rounded hover:bg-blue-100 transition-colors duration-200"
                >
                    <i class="fas fa-trash mr-2"></i>Deleted Categories
                </Link>
            </div>

            <!-- Category Form (only show for active categories view) -->
            <div v-if="!trashedOnly" class="mb-6 p-4 bg-gray-50 rounded-lg">
                <CategoryForm @refresh="handleRefresh" />
            </div>

            <!-- Categories Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div v-if="loading" class="p-4 text-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading categories...
                </div>

                <div v-else-if="categories.length === 0" class="p-8 text-center text-gray-500">
                    <i class="fas fa-folder-open text-4xl mb-4"></i>
                    <p class="text-lg">No categories found</p>
                    <p class="text-sm" v-if="!trashedOnly">Create your first meal category using the form above.</p>
                    <p class="text-sm" v-else>No deleted categories to show.</p>
                </div>

                <table v-else class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Meals Count
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th v-if="showingTrashed || trashedOnly" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="category in categories" :key="category.id"
                            :class="{ 'hover:bg-gray-50': !category.deleted_at, 'bg-red-50 hover:bg-red-100': category.deleted_at }">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium"
                                        :class="{ 'text-gray-900': !category.deleted_at, 'text-gray-400 line-through': category.deleted_at }">
                                    {{ category.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    @click="showCategoryMeals(category)"
                                    :disabled="category.deleted_at"
                                    :class="{
                                        'bg-blue-100 text-blue-800 hover:bg-blue-200': !category.deleted_at,
                                        'bg-gray-100 text-gray-500 cursor-not-allowed': category.deleted_at
                                    }"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors duration-200"
                                    :title="category.deleted_at ? 'Cannot view meals of deleted category' : `View meals in ${category.name}`">
                                    {{ getMealCount(category) }} meals
                                    <i v-if="!category.deleted_at" class="fas fa-external-link-alt ml-1 text-xs"></i>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(category.created_at) }}
                            </td>
                            <td v-if="showingTrashed || trashedOnly" class="px-6 py-4 whitespace-nowrap">
                                <span v-if="category.deleted_at" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-trash mr-1"></i>Deleted {{ formatDate(category.deleted_at) }}
                                </span>
                                <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <!-- Edit button (only for active categories) -->
                                    <CategoryForm
                                        v-if="!category.deleted_at"
                                        :category="category"
                                        @refresh="handleRefresh"
                                        mode="edit"
                                    />

                                    <!-- Restore button (for deleted categories) -->
                                    <Link
                                        v-if="category.deleted_at"
                                        :href="route('meal-categories.restore', category.id)"
                                        method="post"
                                        :disabled="restoring === category.id"
                                        @click="restoring = category.id"
                                        class="inline-flex items-center px-3 py-1 border border-green-300 text-sm leading-4 font-medium rounded-md text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                        <i v-if="restoring === category.id" class="fas fa-spinner fa-spin mr-1"></i>
                                        <i v-else class="fas fa-undo mr-1"></i>
                                        {{ restoring === category.id ? 'Restoring...' : 'Restore' }}
                                    </Link>

                                    <!-- Delete button (for active categories) -->
                                    <button
                                        v-if="!category.deleted_at"
                                        @click="deleteCategory(category.id)"
                                        :disabled="deleting === category.id"
                                        class="inline-flex items-center px-3 py-1 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                        <i v-if="deleting === category.id" class="fas fa-spinner fa-spin mr-1"></i>
                                        <i v-else class="fas fa-trash mr-1"></i>
                                        {{ deleting === category.id ? 'Deleting...' : 'Delete' }}
                                    </button>

                                    <!-- Permanent delete button (for deleted categories) -->
                                    <button
                                        v-if="category.deleted_at"
                                        @click="permanentlyDeleteCategory(category.id)"
                                        :disabled="permanentDeleting === category.id"
                                        class="inline-flex items-center px-3 py-1 border border-red-600 text-sm leading-4 font-medium rounded-md text-red-800 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                        <i v-if="permanentDeleting === category.id" class="fas fa-spinner fa-spin mr-1"></i>
                                        <i v-else class="fas fa-times mr-1"></i>
                                        {{ permanentDeleting === category.id ? 'Deleting...' : 'Delete Forever' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="categories.length > 0" class="mt-6 flex justify-between items-center">
                <div class="text-sm text-gray-700">
                    Showing {{ categories.length }} categories
                </div>
                <!-- Add pagination component here if needed -->
            </div>

            <!-- Meals Modal -->
            <div v-if="showMealsModal"
                    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
                    @click="closeMealsModal">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white"
                        @click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            Meals in "{{ selectedCategory?.name }}" Category
                        </h3>
                        <button @click="closeMealsModal"
                                class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div v-if="categoryMeals.length === 0" class="text-center py-8">
                        <i class="fas fa-utensils text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No meals found in this category</p>
                    </div>

                    <div v-else class="max-h-96 overflow-y-auto">
                        <div class="grid gap-4">
                            <div v-for="meal in categoryMeals" :key="meal.id"
                                    class="flex items-center p-4 border rounded-lg hover:bg-gray-50">
                                <div class="flex-shrink-0 w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                    <i v-if="meal.image_url" class="fas fa-image text-gray-500"></i>
                                    <i v-else class="fas fa-utensils text-gray-500"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ meal.name }}</h4>
                                    <p v-if="meal.description" class="text-sm text-gray-500 mt-1">
                                        {{ meal.description }}
                                    </p>
                                    <div class="flex items-center mt-2 text-xs text-gray-400">
                                        <span v-if="meal.price">₹{{ meal.price }}</span>
                                        <span v-if="meal.price && meal.is_available !== undefined" class="mx-2">•</span>
                                        <span v-if="meal.is_available !== undefined"
                                                :class="meal.is_available ? 'text-green-600' : 'text-red-600'">
                                            {{ meal.is_available ? 'Available' : 'Not Available' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button @click="closeMealsModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Layout.vue';
import CategoryForm from './CategoryForm.vue';

const page = usePage();

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    showingTrashed: {
        type: Boolean,
        default: false
    },
    trashedOnly: {
        type: Boolean,
        default: false
    }
});

// Reactive state
const categories = ref(props.categories);
const loading = ref(false);
const deleting = ref(null);
const restoring = ref(null);
const permanentDeleting = ref(null);
const showMealsModal = ref(false);
const selectedCategory = ref(null);
const categoryMeals = ref([]);
const mealsCount = ref({});

// Computed properties
const flash = computed(() => page.props.flash || {});
const showingTrashed = computed(() => props.showingTrashed);
const trashedOnly = computed(() => props.trashedOnly);

// Methods
const handleRefresh = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('meal-categories.index'));
        // Handle different response structures
        if (response.data.props && response.data.props.categories) {
            categories.value = response.data.props.categories;
        } else if (response.data.categories) {
            categories.value = response.data.categories;
        } else if (Array.isArray(response.data)) {
            categories.value = response.data;
        }

        // Fetch meal counts for each category
        await fetchMealCounts();
    } catch (error) {
        console.error("Error fetching categories:", error);
        // Optionally show error message to user
        page.props.flash = { error: 'Failed to refresh categories' };
    } finally {
        loading.value = false;
    }
};

const fetchMealCounts = async () => {
    // Since we should get meal counts from the category controller,
    // we'll use the relationship data that should be provided by the backend
    const countsMap = {};
    categories.value.forEach(category => {
        countsMap[category.id] = category.meals_count || 0;
    });
    mealsCount.value = countsMap;
};

const getMealCount = (category) => {
    return mealsCount.value[category.id] || category.meals_count || 0;
};

const showCategoryMeals = (category) => {
    if (category.deleted_at) return; // Don't show meals for deleted categories

    console.log('Showing meals for category:', category.name);

    selectedCategory.value = category;
    showMealsModal.value = true;

    // Use the meals that are already loaded with the category
    if (category.meals && Array.isArray(category.meals)) {
        categoryMeals.value = category.meals;
        console.log('Meals loaded:', category.meals.length);
    } else {
        categoryMeals.value = [];
        console.log('No meals found for this category');
    }
};

const closeMealsModal = () => {
    showMealsModal.value = false;
    selectedCategory.value = null;
    categoryMeals.value = [];
};

const deleteCategory = async (id) => {
    if (!confirm('Are you sure you want to delete this category? You can restore it later if needed.')) {
        return;
    }

    deleting.value = id;

    try {
        await router.delete(route('meal-categories.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                // Remove from local state immediately for better UX
                categories.value = categories.value.filter(category => category.id !== id);
                deleting.value = null;
            },
            onError: (errors) => {
                console.error('Delete error:', errors);
                deleting.value = null;
            }
        });
    } catch (error) {
        console.error('Delete error:', error);
        deleting.value = null;
    }
};

const permanentlyDeleteCategory = async (id) => {
    if (!confirm('Are you sure you want to permanently delete this category? This action cannot be undone!')) {
        return;
    }

    permanentDeleting.value = id;

    try {
        await router.delete(route('meal-categories.force-delete', id), {
            preserveScroll: true,
            onSuccess: () => {
                // Remove from local state immediately for better UX
                categories.value = categories.value.filter(category => category.id !== id);
                permanentDeleting.value = null;
            },
            onError: (errors) => {
                console.error('Permanent delete error:', errors);
                permanentDeleting.value = null;
            }
        });
    } catch (error) {
        console.error('Permanent delete error:', error);
        permanentDeleting.value = null;
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';

    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (error) {
        return 'Invalid date';
    }
};

// Watch for prop changes
import { watch, onMounted } from 'vue';
watch(() => props.categories, (newCategories) => {
    categories.value = newCategories;
    // Fetch meal counts when categories change
    if (newCategories && newCategories.length > 0) {
        fetchMealCounts();
    }
}, { immediate: true });

// Fetch meal counts on component mount
onMounted(() => {
    if (categories.value && categories.value.length > 0) {
        fetchMealCounts();
    }
});
</script>

<style scoped>
/* Add any component-specific styles here */
.table-hover tbody tr:hover {
    background-color: #f9fafb;
}

/* Loading animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.fa-spin {
    animation: spin 1s linear infinite;
}
</style>
