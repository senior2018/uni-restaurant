<template>
    <CustomerLayout :user="user">
        <div class="min-h-screen bg-gray-50 p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">My Plate</h1>
            <div v-if="cart.length === 0" class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Your plate is empty</h3>
                <p class="text-gray-500">Browse the <a :href="route('menu.public')" class="text-green-600 underline">menu</a> to add meals to your plate.</p>
            </div>
            <div v-else class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg p-6">
                <div v-for="item in cart" :key="item.id" class="flex items-center justify-between border-b py-4">
                    <div class="flex items-center gap-4">
                        <img :src="item.image_url || '/placeholder.jpg'" :alt="item.name" class="w-16 h-16 object-cover rounded" />
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">{{ item.name }}</h3>
                            <p class="text-gray-600 text-sm">{{ item.category.name }}</p>
                            <span class="text-green-600 font-semibold">{{ formatPrice(item.price) }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="decreaseQty(item)" class="px-2 py-1 bg-gray-200 rounded">-</button>
                        <span class="font-semibold">{{ item.quantity }}</span>
                        <button @click="increaseQty(item)" class="px-2 py-1 bg-gray-200 rounded">+</button>
                        <button @click="removeItem(item)" class="ml-4 text-red-500 hover:underline">Remove</button>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-6">
                    <span class="text-xl font-bold">Total: {{ formatPrice(total) }}</span>
                    <button class="bg-green-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-600 transition-colors" @click="proceedToCheckout">
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
