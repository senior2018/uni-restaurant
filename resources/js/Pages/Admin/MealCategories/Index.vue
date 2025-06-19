<template>
    <AdminLayout>
    <div class="p-6 bg-white rounded shadow">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Meal Categories</h1>
            <Link :href="route('meals.index')"
                class="px-4 py-2 bg-blue-500 text-white rounded">
                Back to Meals
            </Link>
        </div>

        <CategoryForm @refresh="fetchCategories" />

        <table class="min-w-full mt-6">
            <thead>
                <tr>
                    <th class="px-4 py-2">Category Name</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="category in categories" :key="category.id">
                    <td class="px-4 py-2">{{ category.name }}</td>
                    <td class="px-4 py-2 flex space-x-2">
                        <CategoryForm :category="category" @refresh="fetchCategories" />

                        <button @click="deleteCategory(category.id)"
                                class="px-3 py-1 bg-red-500 text-white rounded text-sm">
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
import { ref } from 'vue';
import axios from 'axios';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Layout.vue';
import CategoryForm from './CategoryForm.vue';

const page = usePage();

const props = defineProps({
    categories: Array
});

const categories = ref(props.categories);

const fetchCategories = async () => {
    try {
        const response = await axios.get(route('meal-categories.index'));
        categories.value = response.data.props.categories;
    } catch (error) {
        console.error("Error fetching categories:", error);
    }
};

const deleteCategory = (id) => {
    if (confirm('Are you sure you want to delete this category?')) {
        router.delete(route('meal-categories.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                setTimeout(() => {
                    router.reload({ only: ['categories', 'flash'] });
                }, 1000);
            }
        });
    }
};
</script>
