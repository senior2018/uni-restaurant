<template>
    <AdminLayout>
        <div class="p-6 bg-white rounded shadow">
            <div class="flex justify-between mb-6">
                <h1 class="text-2xl font-bold">Meal Management</h1>
                <Link
                    :href="route('meal-categories.index')"
                    class="px-4 py-2 bg-blue-500 text-white rounded"
                >
                    Manage Categories
                </Link>
            </div>

            <!-- Form for adding new meals -->
            <MealForm
                :categories="categories"
                @refresh="fetchMeals"
            />

            <table class="min-w-full mt-6">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-4 py-2">Image</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Category</th>
                        <th class="px-4 py-2">Price</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="meal in meals"
                        :key="meal.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-4 py-2">
                            <img
                                :src="meal.image_url || '/placeholder.jpg'"
                                class="w-16 h-16 object-cover rounded"
                            />
                        </td>
                        <td class="px-4 py-2">{{ meal.name }}</td>
                        <td class="px-4 py-2">{{ meal.category.name }}</td>
                        <td class="px-4 py-2">
                            ${{ Number(meal.price).toFixed(2) }}
                        </td>
                        <td class="px-4 py-2">
                            <span
                                :class="
                                    meal.is_available
                                        ? 'bg-green-200 text-green-800'
                                        : 'bg-red-200 text-red-800'
                                "
                                class="px-2 py-1 rounded-full text-xs font-medium"
                            >
                                {{
                                    meal.is_available
                                        ? "Available"
                                        : "Unavailable"
                                }}
                            </span>
                        </td>
                        <td class="px-4 py-2 flex space-x-2">
                            <button
                                @click="toggleAvailability(meal)"
                                class="px-3 py-1 bg-gray-200 rounded text-sm"
                            >
                                Toggle
                            </button>
                            <MealForm
                                :meal="meal"
                                :categories="categories"
                                @refresh="fetchMeals"
                            />
                            <button
                                @click="deleteMeal(meal)"
                                class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
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
    if (!confirm(`Are you sure you want to delete "${meal.name}"? This action cannot be undone.`)) {
        return;
    }

    router.delete(route('meals.destroy', meal.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Meal deleted successfully');
        },
        onError: (errors) => {
            console.error('Error deleting meal:', errors);
            alert('An error occurred while deleting the meal. Please try again.');
        }
    });
};
</script>
