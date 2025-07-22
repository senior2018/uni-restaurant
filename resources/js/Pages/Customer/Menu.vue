<template>
    <CustomerLayout :user="user">
        <div class="min-h-screen bg-gray-50 p-6 w-full px-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Menu</h1>
            <div v-if="meals.length === 0" class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No meals available</h3>
                <p class="text-gray-500">Please check back later.</p>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="meal in meals" :key="meal.id" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <img :src="meal.image_url || '/placeholder.jpg'" :alt="meal.name" class="w-full h-48 object-cover" />
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">{{ meal.name }}</h3>
                        <p class="text-gray-600 text-sm mb-3">{{ meal.category.name }}</p>
                        <div class="mb-2">
                            <span class="text-2xl font-bold text-green-600">{{ formatPrice(meal.price) }}</span>
                        </div>
                        <button
                            class="mt-4 w-full py-2 px-3 rounded-lg bg-green-500 text-white font-semibold hover:bg-green-600 transition-colors"
                            @click="addToCart(meal)"
                        >
                            Add to Plate
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="notification" class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
                {{ notification }}
            </div>
            <!-- Floating My Plate button -->
            <button @click="router.visit(route('cart'))" class="fixed bottom-6 right-6 bg-green-600 text-white rounded-full shadow-lg flex items-center px-5 py-3 text-lg font-bold z-50 hover:bg-green-700 transition-colors">
                <i class="fas fa-utensils mr-2"></i>
                My Plate
                <span v-if="plateCount > 0" class="ml-2 bg-white text-green-700 rounded-full px-3 py-1 text-sm font-bold">{{ plateCount }}</span>
            </button>
        </div>
    </CustomerLayout>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import CustomerLayout from './Layout.vue';

const props = defineProps({
    meals: Array,
    user: Object,
});

const page = usePage();
const notification = ref("");
const plateCount = ref(0);

onMounted(() => {
    // Update plate count on mount
    const plate = JSON.parse(localStorage.getItem('cart') || '[]');
    plateCount.value = plate.reduce((sum, item) => sum + item.quantity, 0);
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('sw-TZ', {
        style: 'currency',
        currency: 'TZS',
    }).format(price);
};

function addToCart(meal) {
    // If not logged in, redirect to login
    if (!props.user) {
        router.visit(route('login'));
        return;
    }
    // Get plate from localStorage or initialize
    let plate = JSON.parse(localStorage.getItem('cart') || '[]');
    // Check if meal already in plate
    const existing = plate.find(item => item.id === meal.id);
    if (existing) {
        existing.quantity += 1;
    } else {
        plate.push({ ...meal, quantity: 1, price: meal.price });
    }
    localStorage.setItem('cart', JSON.stringify(plate));
    plateCount.value = plate.reduce((sum, item) => sum + item.quantity, 0);
    notification.value = `${meal.name} added to plate!`;
    setTimeout(() => notification.value = '', 2000);
}
</script>

