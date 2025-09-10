<template>
    <CustomerLayout :user="user">
        <div class="min-h-screen p-4 sm:p-6 w-full">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                    Our Delicious Menu
                </h1>

                <div v-if="meals.length === 0" class="text-center py-16">
                    <div class="text-gray-300 text-8xl mb-6">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-3">No meals available</h3>
                    <p class="text-gray-500 text-lg">Check back soon for amazing dishes!</p>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                    <div
                        v-for="meal in meals"
                        :key="meal.id"
                        class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 transform"
                    >
                        <div class="relative overflow-hidden">
                            <img
                                :src="meal.image_url || '/placeholder.jpg'"
                                :alt="meal.name"
                                class="w-full h-40 sm:h-44 object-cover group-hover:scale-110 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
                            <div class="absolute top-3 left-3">
                                <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg">
                                    {{ meal.category.name }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4">
                            <h3 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2 group-hover:text-green-700 transition-colors">
                                {{ meal.name }}
                            </h3>

                            <div class="flex items-center justify-between mb-4">
                                <span class="text-2xl font-bold text-green-600 bg-green-50 px-3 py-1 rounded-lg">
                                    {{ formatPrice(meal.price) }}
                                </span>
                            </div>

                            <button
                                class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg active:scale-95"
                                @click="addToCart(meal)"
                            >
                                <i class="fas fa-plus mr-2"></i>
                                Add to Plate
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification with better positioning -->
            <div
                v-if="notification"
                class="fixed top-6 right-6 bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl shadow-2xl z-50 transform animate-bounce"
            >
                <i class="fas fa-check-circle mr-2"></i>
                {{ notification }}
            </div>

            <!-- Enhanced Floating My Plate button -->
            <button
                @click="router.visit(route('cart'))"
                class="fixed bottom-6 right-6 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-2xl shadow-2xl flex items-center px-6 py-4 text-lg font-bold z-50 hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-110 hover:-translate-y-1"
            >
                <i class="fas fa-utensils mr-3 text-xl"></i>
                <span class="hidden sm:inline">My Plate</span>
                <span class="sm:hidden">Plate</span>
                <span
                    v-if="plateCount > 0"
                    class="ml-3 bg-white text-green-700 rounded-full px-3 py-1 text-sm font-bold shadow-lg animate-pulse"
                >
                    {{ plateCount }}
                </span>
            </button>
        </div>
    </CustomerLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    overflow: hidden;
}
</style>

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

