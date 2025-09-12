<script setup>
import StaffLayout from './Layout.vue';
import { computed, ref, nextTick } from 'vue';
import axios from 'axios';

const props = defineProps({
    user: Object,
    unassignedOrders: Array,
    myOrders: Array
});

function claimOrder(orderId) {
    axios.post(route('staff.orders.claim', orderId))
        .then(() => window.location.reload())
        .catch(err => alert(err.response?.data?.message || 'Failed to claim order.'));
}
function updateOrderStatus(orderId, newStatus) {
    axios.post(route('staff.orders.updateStatus', orderId), { status: newStatus })
        .then(() => window.location.reload())
        .catch(err => alert(err.response?.data?.message || 'Failed to update order status.'));
}

// Stats
const totalAssigned = computed(() => props.myOrders.length);
const totalUnassigned = computed(() => props.unassignedOrders.length);
const totalDelivered = computed(() => props.myOrders.filter(o => o.status === 'delivered').length);
const totalCancelledByMe = computed(() => props.myOrders.filter(o => o.status === 'cancelled' && o.cancelled_by === 'staff').length);

// Group myOrders by status
const myPending = computed(() => props.myOrders.filter(o => o.status === 'pending'));
const myPreparing = computed(() => props.myOrders.filter(o => o.status === 'preparing'));
const myDelivered = computed(() => props.myOrders.filter(o => o.status === 'delivered'));
const myPendingCancellationRequests = computed(() => props.myOrders.filter(o => o.cancellation_requested));
const unseenCancellationCount = computed(() => props.myOrders.filter(o => o.cancellation_requested && !o.cancellation_request_seen).length);
const cancellationRequestsSection = ref(null);
function scrollToCancellationRequests() {
    nextTick(() => {
        if (cancellationRequestsSection.value) {
            cancellationRequestsSection.value.scrollIntoView({ behavior: 'smooth' });
        }
    });
    // Mark all as seen (API call)
    axios.post(route('staff.orders.markCancellationSeen')).then(() => window.location.reload());
}
</script>

<template>
    <StaffLayout :user="user">
        <div class="space-y-6 sm:space-y-8">
            <h2 class="text-responsive-lg font-bold text-gray-800 mb-6">Staff Dashboard</h2>

            <!-- Stats Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <div class="card-responsive bg-blue-100">
                    <i class="fas fa-tasks text-xl sm:text-2xl text-blue-600 mb-2"></i>
                    <div class="text-xs sm:text-sm text-gray-500">Assigned Orders</div>
                    <div class="text-xl sm:text-2xl font-bold text-blue-800">{{ totalAssigned }}</div>
                </div>
                <div class="card-responsive bg-yellow-100">
                    <i class="fas fa-inbox text-xl sm:text-2xl text-yellow-600 mb-2"></i>
                    <div class="text-xs sm:text-sm text-gray-500">Unassigned Orders</div>
                    <div class="text-xl sm:text-2xl font-bold text-yellow-800">{{ totalUnassigned }}</div>
                </div>
                <div class="card-responsive bg-green-100">
                    <i class="fas fa-check-circle text-xl sm:text-2xl text-green-600 mb-2"></i>
                    <div class="text-xs sm:text-sm text-gray-500">Delivered Orders</div>
                    <div class="text-xl sm:text-2xl font-bold text-green-800">{{ totalDelivered }}</div>
                </div>
                <div class="card-responsive bg-red-100">
                    <i class="fas fa-ban text-xl sm:text-2xl text-red-600 mb-2"></i>
                    <div class="text-xs sm:text-sm text-gray-500">Orders Cancelled by Me</div>
                    <div class="text-xl sm:text-2xl font-bold text-red-800">{{ totalCancelledByMe }}</div>
                </div>
                <div class="card-responsive bg-yellow-100 relative">
                    <i class="fas fa-ban text-xl sm:text-2xl text-yellow-600 mb-2"></i>
                    <div class="text-xs sm:text-sm text-gray-500">Pending Cancellation Requests</div>
                    <div class="text-xl sm:text-2xl font-bold text-yellow-800">{{ myPendingCancellationRequests.length }}</div>
                    <span v-if="unseenCancellationCount > 0"
                          class="absolute top-2 right-2 sm:right-4 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                      {{ unseenCancellationCount }}
                    </span>
                </div>
            </div>

            <!-- My Orders Section -->
            <div class="card-responsive mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-bold text-blue-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-tasks"></i> My Orders
                </h3>
                <p class="text-sm sm:text-base text-gray-500 mb-4">These are orders currently assigned to you. Update their status as you work.</p>
                <div v-if="myOrders.length === 0" class="text-gray-500">No orders assigned to you.</div>
                <div v-else>
                    <!-- Pending -->
                    <div v-if="myPending.length">
                        <h4 class="text-md font-semibold text-yellow-700 mb-2 mt-4 flex items-center gap-2">
                            <i class="fas fa-hourglass-half"></i> Pending
                        </h4>
                        <div class="space-y-3">
                            <div v-for="order in myPending" :key="order.id" class="p-3 sm:p-4 border rounded-lg flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 hover:bg-yellow-50 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <span class="font-medium">Order #{{ order.id }}</span>
                                    <span class="text-xs sm:text-sm text-gray-500">{{ order.created_at }}</span>
                                    <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ order.status }}</span>
                                </div>
                                <button @click="updateOrderStatus(order.id, 'preparing')" class="btn-responsive bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">Start Preparing</button>
                            </div>
                        </div>
                    </div>
                    <!-- Preparing -->
                    <div v-if="myPreparing.length">
                        <h4 class="text-md font-semibold text-blue-700 mb-2 mt-6 flex items-center gap-2">
                            <i class="fas fa-utensils"></i> Preparing
                        </h4>
                        <div class="space-y-3">
                            <div v-for="order in myPreparing" :key="order.id" class="p-4 border rounded-lg flex justify-between items-center hover:bg-blue-50 transition-colors">
                                <div>
                                    <span class="font-medium">Order #{{ order.id }}</span>
                                    <span class="text-sm text-gray-500 ml-2">{{ order.created_at }}</span>
                                    <span class="ml-4 px-3 py-1 rounded-full text-xs font-semibold"
                                          :class="{
                                            'bg-orange-100 text-orange-700': order.status === 'preparing' && order.cancellation_requested,
                                            'bg-blue-100 text-blue-700': order.status === 'preparing' && !order.cancellation_requested,
                                            'bg-yellow-100 text-yellow-700': order.status === 'pending',
                                            'bg-green-100 text-green-700': order.status === 'delivered',
                                            'bg-red-100 text-red-700': order.status === 'cancelled',
                                          }">
  {{ order.status === 'preparing' && order.cancellation_requested ? 'Preparing (Cancellation Requested)' : order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
</span>
                                </div>
                                <button @click="updateOrderStatus(order.id, 'delivered')" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition">Mark as Delivered</button>
                            </div>
                        </div>
                    </div>
                    <!-- Delivered -->
                    <div v-if="myDelivered.length">
                        <h4 class="text-md font-semibold text-green-700 mb-2 mt-6 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Delivered
                        </h4>
                        <div class="space-y-3">
                            <div v-for="order in myDelivered" :key="order.id" class="p-4 border rounded-lg flex justify-between items-center hover:bg-green-50 transition-colors">
                                <div>
                                    <span class="font-medium">Order #{{ order.id }}</span>
                                    <span class="text-sm text-gray-500 ml-2">{{ order.created_at }}</span>
                                    <span class="ml-4 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ order.status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unassigned Orders Section -->
            <div class="card-responsive">
                <h3 class="text-lg sm:text-xl font-bold text-yellow-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-inbox"></i> Unassigned Orders
                </h3>
                <p class="text-sm sm:text-base text-gray-500 mb-4">These are new orders waiting for a staff member. Claim an order to start working on it.</p>
                <div v-if="unassignedOrders.length === 0" class="text-gray-500">No unassigned orders.</div>
                <div v-else class="space-y-4">
                    <div v-for="order in unassignedOrders" :key="order.id" class="p-3 sm:p-4 border rounded-lg flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 hover:bg-yellow-50 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <span class="font-medium">Order #{{ order.id }}</span>
                            <span class="text-xs sm:text-sm text-gray-500">{{ order.created_at }}</span>
                            <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ order.status }}</span>
                        </div>
                        <button @click="claimOrder(order.id)" class="btn-responsive bg-blue-600 text-white rounded hover:bg-blue-700 transition">Claim</button>
                    </div>
                </div>
            </div>

            <!-- My Pending Cancellation Requests Section -->
            <div ref="cancellationRequestsSection" class="card-responsive mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-bold text-yellow-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-ban"></i> My Pending Cancellation Requests
                </h3>
                <div v-if="myPendingCancellationRequests.length === 0" class="text-gray-500">No pending cancellation requests.</div>
                <div v-else>
                    <div v-for="order in myPendingCancellationRequests" :key="order.id" class="p-3 sm:p-4 border rounded-lg flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-2">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <span class="font-medium">Order #{{ order.id }}</span>
                            <span class="text-xs sm:text-sm text-gray-500">{{ order.created_at }}</span>
                            <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ order.status }}</span>
                            <span class="text-xs text-gray-700">Reason: {{ order.cancellation_reason }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button @click="() => {}" class="btn-responsive bg-blue-500 text-white rounded hover:bg-blue-600">View</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StaffLayout>
</template>
