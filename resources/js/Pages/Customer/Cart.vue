<template>
    <CustomerLayout :user="user">
        <div class="min-h-screen bg-gray-50 p-4 sm:p-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">My Plate</h1>
            <div v-if="cart.length === 0" class="text-center py-8 sm:py-12">
                <div class="text-gray-400 text-4xl sm:text-6xl mb-4">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">Your plate is empty</h3>
                <p class="text-sm sm:text-base text-gray-500">Browse the <a :href="route('menu.public')" class="text-green-600 underline">menu</a> to add meals to your plate.</p>
            </div>
            <div v-else class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg p-4 sm:p-6">
                <div v-for="item in cart" :key="item.id" class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b py-4 gap-3 sm:gap-0">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <img :src="item.image_url || '/placeholder.jpg'" :alt="item.name" class="w-12 h-12 sm:w-16 sm:h-16 object-cover rounded" />
                        <div>
                            <h3 class="font-bold text-base sm:text-lg text-gray-800">{{ item.name }}</h3>
                            <p class="text-gray-600 text-xs sm:text-sm">{{ item.category.name }}</p>
                            <span class="text-green-600 font-semibold text-sm sm:text-base">{{ formatPrice(item.price) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-2">
                        <div class="flex items-center gap-2">
                            <button @click="decreaseQty(item)" class="px-2 py-1 bg-gray-200 rounded text-sm sm:text-base">-</button>
                            <span class="font-semibold text-sm sm:text-base">{{ item.quantity }}</span>
                            <button @click="increaseQty(item)" class="px-2 py-1 bg-gray-200 rounded text-sm sm:text-base">+</button>
                        </div>
                        <button @click="removeItem(item)" class="text-red-500 hover:underline text-xs sm:text-sm">Remove</button>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mt-6 gap-4">
                    <span class="text-lg sm:text-xl font-bold">Total: {{ formatPrice(total) }}</span>
                    <button class="btn-responsive bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors" @click="proceedToCheckout">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import CustomerLayout from './Layout.vue';

const props = defineProps({
    user: Object,
});

const cart = ref([]);

onMounted(() => {
    cart.value = JSON.parse(localStorage.getItem('cart') || '[]');
    // Ensure every item has a price
    cart.value = cart.value.map(item => ({
        ...item,
        price: item.price ?? item.meal?.price ?? 0
    }));
    localStorage.setItem('cart', JSON.stringify(cart.value));
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('sw-TZ', {
        style: 'currency',
        currency: 'TZS',
    }).format(price);
};

const total = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0);
});

function increaseQty(item) {
    item.quantity += 1;
    if (!('price' in item)) item.price = item.price ?? item.meal?.price ?? 0;
    saveCart();
}

function decreaseQty(item) {
    if (item.quantity > 1) {
        item.quantity -= 1;
        if (!('price' in item)) item.price = item.price ?? item.meal?.price ?? 0;
        saveCart();
    }
}

function removeItem(item) {
    cart.value = cart.value.filter(i => i.id !== item.id);
    saveCart();
}

function saveCart() {
    // Ensure every item has a price before saving
    cart.value = cart.value.map(item => ({ ...item, price: item.price ?? item.meal?.price ?? 0 }));
    localStorage.setItem('cart', JSON.stringify(cart.value));
}

function proceedToCheckout() {
    router.visit(route('checkout'));
}
</script>
