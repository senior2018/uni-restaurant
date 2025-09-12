<script setup>
import { ref, computed, onMounted } from 'vue';
import CustomerLayout from './Layout.vue';
import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
const page = usePage();

const props = defineProps({
    user: Object,
});

const cart = ref([]);
const deliveryLocation = ref(props.user?.permanent_location || '');
const paymentMethod = ref('cash');
const error = ref("");

onMounted(() => {
    cart.value = JSON.parse(localStorage.getItem('cart') || '[]');
    cart.value = cart.value.map(item => ({
        ...item,
        price: item.price ?? item.meal?.price ?? 0
    }));
    localStorage.setItem('cart', JSON.stringify(cart.value));
    // If user has a permanent location and deliveryLocation is empty, prefill it
    if (!deliveryLocation.value && props.user?.permanent_location) {
        deliveryLocation.value = props.user.permanent_location;
    }
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

function placeOrder() {
    error.value = "";
    router.post(
        route('checkout.store'),
        {
            cart: cart.value,
            delivery_location: deliveryLocation.value,
            payment_method: paymentMethod.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                localStorage.removeItem('cart');
                cart.value = [];
            },
            onError: (err) => {
                error.value = err.message || 'Order failed. Please try again.';
            }
        }
    );
}
</script>

<template>
    <CustomerLayout :user="user">
        <div class="min-h-screen bg-gray-50 p-4 sm:p-6 w-full">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4 sm:mb-6 text-center">Checkout</h1>
            <div v-if="page.props.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 sm:mb-6 text-center text-sm sm:text-base">
                {{ page.props.flash.success }}
            </div>
            <div v-if="page.props.flash?.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 sm:mb-6 text-center text-sm sm:text-base">
                {{ page.props.flash.error }}
            </div>
            <div v-if="error && !page.props.flash?.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 sm:mb-6 text-center text-sm sm:text-base">
                {{ error }}
            </div>
            <div v-if="cart.length === 0" class="text-center py-8 sm:py-12">
                <div class="text-gray-400 text-4xl sm:text-6xl mb-4">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">Your cart is empty</h3>
            </div>
            <div v-else>
                <h2 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">Order Summary</h2>
                <div class="bg-white rounded-xl shadow p-3 sm:p-4 mb-4 sm:mb-6">
                    <div v-for="item in cart" :key="item.id" class="flex justify-between items-center border-b py-2">
                        <div>
                            <span class="font-semibold text-sm sm:text-base">{{ item.name }}</span>
                            <span class="text-gray-500 text-xs sm:text-sm"> x{{ item.quantity }}</span>
                        </div>
                        <span class="text-sm sm:text-base">{{ formatPrice(item.price * item.quantity) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-3 sm:mt-4">
                        <span class="font-bold text-sm sm:text-base">Total:</span>
                        <span class="text-lg sm:text-xl font-bold">{{ formatPrice(total) }}</span>
                    </div>
                </div>
                <form @submit.prevent="placeOrder" class="space-y-4 sm:space-y-6">
                    <div>
                        <label class="block font-medium mb-1 text-sm sm:text-base">Delivery Location</label>
                        <input v-model="deliveryLocation" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base" placeholder="Enter your delivery address" />
                    </div>
                    <div>
                        <label class="block font-medium mb-1 text-sm sm:text-base">Payment Method</label>
                        <select v-model="paymentMethod" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm sm:text-base">
                            <option value="cash">Cash</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full btn-responsive bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors">
                        Place Order
                    </button>
                </form>
            </div>
        </div>
    </CustomerLayout>
</template>
