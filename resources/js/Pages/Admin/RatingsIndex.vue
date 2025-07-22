<script setup>
import AdminLayout from './Layout.vue';
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();
const props = defineProps({
    ratings: Object, // paginated
    user: Object,
});

const filterMeal = ref('');
const filterUser = ref('');
const filterValue = ref('all');
const showModal = ref(false);
const selectedRating = ref(null);
const staffResponse = ref('');

function applyFilters() {
    router.get(route('admin.ratings.index'), {
        meal_id: filterMeal.value || undefined,
        user_id: filterUser.value || undefined,
        rating: filterValue.value !== 'all' ? filterValue.value : undefined,
    }, { preserveState: true, replace: true });
}
function openModal(rating) {
    selectedRating.value = rating;
    staffResponse.value = '';
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    selectedRating.value = null;
    staffResponse.value = '';
}
function submitResponse() {
    if (!staffResponse.value.trim()) return;
    router.patch(route('admin.ratings.respond', selectedRating.value.id), {
        response_comment: staffResponse.value
    }, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {},
    });
}
// Analytics: average rating per meal (from current page)
const avgRating = computed(() => {
    if (!props.ratings.data.length) return 0;
    const sum = props.ratings.data.reduce((acc, r) => acc + r.rating, 0);
    return (sum / props.ratings.data.length).toFixed(2);
});
</script>

<template>
    <AdminLayout :user="user">
        <div class="max-w-6xl mx-auto p-6">
            <h1 class="text-2xl font-bold text-primary-dark mb-6">Ratings Management</h1>
            <div class="flex flex-wrap gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Meal ID</label>
                    <input v-model="filterMeal" @keyup.enter="applyFilters" type="text" class="border rounded px-2 py-1" placeholder="Meal ID" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">User ID</label>
                    <input v-model="filterUser" @keyup.enter="applyFilters" type="text" class="border rounded px-2 py-1" placeholder="User ID" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Rating</label>
                    <select v-model="filterValue" @change="applyFilters" class="border rounded px-2 py-1">
                        <option value="all">All</option>
                        <option v-for="n in 5" :key="n" :value="n">{{ n }} stars</option>
                    </select>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <span class="text-sm text-gray-600">Average rating (page):</span>
                    <span class="font-bold text-green-700">{{ avgRating }}</span>
                </div>
            </div>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">ID</th>
                            <th class="px-4 py-2 text-left">Order</th>
                            <th class="px-4 py-2 text-left">Meal</th>
                            <th class="px-4 py-2 text-left">User</th>
                            <th class="px-4 py-2 text-left">Rating</th>
                            <th class="px-4 py-2 text-left">Comment</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rating in ratings.data" :key="rating.id" class="border-b hover:bg-green-50">
                            <td class="px-4 py-2 font-semibold">{{ rating.id }}</td>
                            <td class="px-4 py-2">#{{ rating.order_id }}</td>
                            <td class="px-4 py-2">{{ rating.meal?.name || rating.meal_id }}</td>
                            <td class="px-4 py-2">{{ rating.user?.name || rating.user_id }}</td>
                            <td class="px-4 py-2">
                                <span class="flex items-center">
                                    <i v-for="n in 5" :key="n" class="fas fa-star" :class="n <= rating.rating ? 'text-yellow-400' : 'text-gray-300'"></i>
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ rating.comment }}</td>
                            <td class="px-4 py-2">
                                <button @click="openModal(rating)" class="px-3 py-1 bg-primary text-white rounded hover:bg-primary-dark">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="flex justify-center mt-6 gap-2">
                <button :disabled="!ratings.prev_page_url" @click="router.get(ratings.prev_page_url, {}, { preserveState: true, replace: true })" class="px-3 py-1 rounded border" :class="ratings.prev_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Prev</button>
                <span class="px-2">Page {{ ratings.current_page }} of {{ ratings.last_page }}</span>
                <button :disabled="!ratings.next_page_url" @click="router.get(ratings.next_page_url, {}, { preserveState: true, replace: true })" class="px-3 py-1 rounded border" :class="ratings.next_page_url ? 'bg-white hover:bg-gray-100' : 'bg-gray-100 text-gray-400'">Next</button>
            </div>
            <!-- Rating Details Modal -->
            <Modal :show="showModal" @close="closeModal">
                <div class="p-6 max-w-lg mx-auto">
                    <h2 class="text-lg font-bold mb-2">Rating #{{ selectedRating?.id }}</h2>
                    <div class="mb-2"><span class="font-medium">Order:</span> #{{ selectedRating?.order_id }}</div>
                    <div class="mb-2"><span class="font-medium">Meal:</span> {{ selectedRating?.meal?.name || selectedRating?.meal_id }}</div>
                    <div class="mb-2"><span class="font-medium">User:</span> {{ selectedRating?.user?.name || selectedRating?.user_id }}</div>
                    <div class="mb-2"><span class="font-medium">Rating:</span>
                        <span class="flex items-center">
                            <i v-for="n in 5" :key="n" class="fas fa-star" :class="n <= (selectedRating?.rating || 0) ? 'text-yellow-400' : 'text-gray-300'"></i>
                        </span>
                    </div>
                    <div class="mb-2"><span class="font-medium">Comment:</span> {{ selectedRating?.comment }}</div>
                    <div v-if="selectedRating?.response_comment" class="mb-2"><span class="font-medium">Staff Response:</span> {{ selectedRating?.response_comment }}</div>
                    <div v-if="!selectedRating?.response_comment" class="mt-4 flex gap-2">
                        <input v-model="staffResponse" type="text" class="flex-1 border rounded px-2 py-1" placeholder="Write a response..." />
                        <button @click="submitResponse" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark">Send</button>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button @click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
                    </div>
                </div>
            </Modal>
        </div>
    </AdminLayout>
</template>
