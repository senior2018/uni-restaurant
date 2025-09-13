<script setup>
import SuperAdminLayout from './Layout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    config: Object,
    errors: Object
});

// Form for basic restaurant information
const basicForm = useForm({
    restaurant_name: props.config?.restaurant_name || '',
    restaurant_slug: props.config?.restaurant_slug || '',
    description: props.config?.description || '',
    email: props.config?.email || '',
    phone: props.config?.phone || '',
    address: props.config?.address || '',
    city: props.config?.city || '',
    state: props.config?.state || '',
    postal_code: props.config?.postal_code || '',
    country: props.config?.country || 'US',
});

// Form for branding and theme
const brandingForm = useForm({
    primary_color: props.config?.primary_color || '#10B981',
    secondary_color: props.config?.secondary_color || '#059669',
    accent_color: props.config?.accent_color || '#F59E0B',
    text_color: props.config?.text_color || '#1F2937',
    background_color: props.config?.background_color || '#FFFFFF',
});

// Form for business settings
const businessForm = useForm({
    currency: props.config?.currency || 'USD',
    currency_symbol: props.config?.currency_symbol || '$',
    tax_rate: props.config?.tax_rate || 0.0875,
    tax_name: props.config?.tax_name || 'Sales Tax',
    delivery_fee: props.config?.delivery_fee || 0.00,
    minimum_order: props.config?.minimum_order || 0.00,
    preparation_time: props.config?.preparation_time || 30,
});

// Form for business hours
const businessHoursForm = useForm({
    business_hours: props.config?.business_hours || {
        monday: { open: '09:00', close: '22:00', closed: false },
        tuesday: { open: '09:00', close: '22:00', closed: false },
        wednesday: { open: '09:00', close: '22:00', closed: false },
        thursday: { open: '09:00', close: '22:00', closed: false },
        friday: { open: '09:00', close: '23:00', closed: false },
        saturday: { open: '10:00', close: '23:00', closed: false },
        sunday: { open: '10:00', close: '21:00', closed: false },
    }
});

// Form for payment methods
const paymentForm = useForm({
    payment_methods: props.config?.payment_methods || ['cash', 'card'],
    cash_payment: props.config?.cash_payment || true,
    card_payment: props.config?.card_payment || true,
    digital_wallet: props.config?.digital_wallet || false,
});

// Form for notification settings
const notificationForm = useForm({
    email_notifications: props.config?.email_notifications || true,
    sms_notifications: props.config?.sms_notifications || false,
    push_notifications: props.config?.push_notifications || false,
});

// Form for system settings
const systemForm = useForm({
    maintenance_mode: props.config?.maintenance_mode || false,
    maintenance_message: props.config?.maintenance_message || '',
    registration_enabled: props.config?.registration_enabled || true,
    guest_checkout: props.config?.guest_checkout || false,
    session_timeout: props.config?.session_timeout || 120,
});

// Active tab state
const activeTab = ref('basic');

const days = [
    { key: 'monday', label: 'Monday' },
    { key: 'tuesday', label: 'Tuesday' },
    { key: 'wednesday', label: 'Wednesday' },
    { key: 'thursday', label: 'Thursday' },
    { key: 'friday', label: 'Friday' },
    { key: 'saturday', label: 'Saturday' },
    { key: 'sunday', label: 'Sunday' },
];

// Computed properties
const taxRatePercentage = computed(() => {
    return (businessForm.tax_rate * 100).toFixed(2);
});

// Methods
const updateBasicInfo = () => {
    basicForm.put(route('superadmin.restaurant.config.update'));
};

const updateBranding = () => {
    brandingForm.put(route('superadmin.restaurant.config.update'));
};

const updateBusinessSettings = () => {
    businessForm.put(route('superadmin.restaurant.config.update'));
};

const updateBusinessHours = () => {
    businessHoursForm.put(route('superadmin.restaurant.config.business-hours'));
};

const updatePaymentMethods = () => {
    paymentForm.put(route('superadmin.restaurant.config.payment-methods'));
};

const updateNotificationSettings = () => {
    notificationForm.put(route('superadmin.restaurant.config.notifications'));
};

const updateSystemSettings = () => {
    systemForm.put(route('superadmin.restaurant.config.system-settings'));
};

const toggleDayClosed = (day) => {
    businessHoursForm.business_hours[day].closed = !businessHoursForm.business_hours[day].closed;
};

const togglePaymentMethod = (method) => {
    const methods = paymentForm.payment_methods;
    const index = methods.indexOf(method);
    if (index > -1) {
        methods.splice(index, 1);
    } else {
        methods.push(method);
    }
};
</script>

<template>
    <SuperAdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-responsive-lg font-bold text-gray-800">Restaurant Configuration</h2>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-store mr-1"></i>
                    Manage restaurant settings and preferences
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button
                            v-for="tab in [
                                { id: 'basic', name: 'Basic Info', icon: 'fas fa-info-circle' },
                                { id: 'branding', name: 'Branding', icon: 'fas fa-palette' },
                                { id: 'business', name: 'Business', icon: 'fas fa-business-time' },
                                { id: 'hours', name: 'Hours', icon: 'fas fa-clock' },
                                { id: 'payment', name: 'Payment', icon: 'fas fa-credit-card' },
                                { id: 'notifications', name: 'Notifications', icon: 'fas fa-bell' },
                                { id: 'system', name: 'System', icon: 'fas fa-cogs' }
                            ]"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                activeTab === tab.id
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center'
                            ]"
                        >
                            <i :class="tab.icon" class="mr-2"></i>
                            {{ tab.name }}
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- Basic Information Tab -->
                    <div v-if="activeTab === 'basic'" class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-800">Basic Restaurant Information</h3>

                        <form @submit.prevent="updateBasicInfo" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Restaurant Name -->
                                <div>
                                    <label for="restaurant_name" class="block text-sm font-medium text-gray-700">
                                        Restaurant Name
                                    </label>
                                    <input
                                        id="restaurant_name"
                                        v-model="basicForm.restaurant_name"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.restaurant_name }"
                                        required
                                    />
                                    <div v-if="errors.restaurant_name" class="mt-1 text-sm text-red-600">
                                        {{ errors.restaurant_name }}
                                    </div>
                                </div>

                                <!-- Restaurant Slug -->
                                <div>
                                    <label for="restaurant_slug" class="block text-sm font-medium text-gray-700">
                                        Restaurant Slug
                                    </label>
                                    <input
                                        id="restaurant_slug"
                                        v-model="basicForm.restaurant_slug"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.restaurant_slug }"
                                        required
                                    />
                                    <div v-if="errors.restaurant_slug" class="mt-1 text-sm text-red-600">
                                        {{ errors.restaurant_slug }}
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Email Address
                                    </label>
                                    <input
                                        id="email"
                                        v-model="basicForm.email"
                                        type="email"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.email }"
                                    />
                                    <div v-if="errors.email" class="mt-1 text-sm text-red-600">
                                        {{ errors.email }}
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">
                                        Phone Number
                                    </label>
                                    <input
                                        id="phone"
                                        v-model="basicForm.phone"
                                        type="tel"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.phone }"
                                    />
                                    <div v-if="errors.phone" class="mt-1 text-sm text-red-600">
                                        {{ errors.phone }}
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    v-model="basicForm.description"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.description }"
                                ></textarea>
                                <div v-if="errors.description" class="mt-1 text-sm text-red-600">
                                    {{ errors.description }}
                                </div>
                            </div>

                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">
                                    Address
                                </label>
                                <textarea
                                    id="address"
                                    v-model="basicForm.address"
                                    rows="2"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.address }"
                                ></textarea>
                                <div v-if="errors.address" class="mt-1 text-sm text-red-600">
                                    {{ errors.address }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- City -->
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">
                                        City
                                    </label>
                                    <input
                                        id="city"
                                        v-model="basicForm.city"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.city }"
                                    />
                                    <div v-if="errors.city" class="mt-1 text-sm text-red-600">
                                        {{ errors.city }}
                                    </div>
                                </div>

                                <!-- State -->
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700">
                                        State
                                    </label>
                                    <input
                                        id="state"
                                        v-model="basicForm.state"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.state }"
                                    />
                                    <div v-if="errors.state" class="mt-1 text-sm text-red-600">
                                        {{ errors.state }}
                                    </div>
                                </div>

                                <!-- Postal Code -->
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                        Postal Code
                                    </label>
                                    <input
                                        id="postal_code"
                                        v-model="basicForm.postal_code"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.postal_code }"
                                    />
                                    <div v-if="errors.postal_code" class="mt-1 text-sm text-red-600">
                                        {{ errors.postal_code }}
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="basicForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <i v-if="basicForm.processing" class="fas fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fas fa-save mr-2"></i>
                                    {{ basicForm.processing ? 'Updating...' : 'Update Basic Info' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Branding Tab -->
                    <div v-if="activeTab === 'branding'" class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-800">Branding & Theme</h3>

                        <form @submit.prevent="updateBranding" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Primary Color -->
                                <div>
                                    <label for="primary_color" class="block text-sm font-medium text-gray-700">
                                        Primary Color
                                    </label>
                                    <div class="mt-1 flex items-center space-x-3">
                                        <input
                                            id="primary_color"
                                            v-model="brandingForm.primary_color"
                                            type="color"
                                            class="h-10 w-20 border border-gray-300 rounded-md"
                                            :class="{ 'border-red-500': errors.primary_color }"
                                        />
                                        <input
                                            v-model="brandingForm.primary_color"
                                            type="text"
                                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :class="{ 'border-red-500': errors.primary_color }"
                                        />
                                    </div>
                                    <div v-if="errors.primary_color" class="mt-1 text-sm text-red-600">
                                        {{ errors.primary_color }}
                                    </div>
                                </div>

                                <!-- Secondary Color -->
                                <div>
                                    <label for="secondary_color" class="block text-sm font-medium text-gray-700">
                                        Secondary Color
                                    </label>
                                    <div class="mt-1 flex items-center space-x-3">
                                        <input
                                            id="secondary_color"
                                            v-model="brandingForm.secondary_color"
                                            type="color"
                                            class="h-10 w-20 border border-gray-300 rounded-md"
                                            :class="{ 'border-red-500': errors.secondary_color }"
                                        />
                                        <input
                                            v-model="brandingForm.secondary_color"
                                            type="text"
                                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :class="{ 'border-red-500': errors.secondary_color }"
                                        />
                                    </div>
                                    <div v-if="errors.secondary_color" class="mt-1 text-sm text-red-600">
                                        {{ errors.secondary_color }}
                                    </div>
                                </div>

                                <!-- Accent Color -->
                                <div>
                                    <label for="accent_color" class="block text-sm font-medium text-gray-700">
                                        Accent Color
                                    </label>
                                    <div class="mt-1 flex items-center space-x-3">
                                        <input
                                            id="accent_color"
                                            v-model="brandingForm.accent_color"
                                            type="color"
                                            class="h-10 w-20 border border-gray-300 rounded-md"
                                            :class="{ 'border-red-500': errors.accent_color }"
                                        />
                                        <input
                                            v-model="brandingForm.accent_color"
                                            type="text"
                                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :class="{ 'border-red-500': errors.accent_color }"
                                        />
                                    </div>
                                    <div v-if="errors.accent_color" class="mt-1 text-sm text-red-600">
                                        {{ errors.accent_color }}
                                    </div>
                                </div>

                                <!-- Text Color -->
                                <div>
                                    <label for="text_color" class="block text-sm font-medium text-gray-700">
                                        Text Color
                                    </label>
                                    <div class="mt-1 flex items-center space-x-3">
                                        <input
                                            id="text_color"
                                            v-model="brandingForm.text_color"
                                            type="color"
                                            class="h-10 w-20 border border-gray-300 rounded-md"
                                            :class="{ 'border-red-500': errors.text_color }"
                                        />
                                        <input
                                            v-model="brandingForm.text_color"
                                            type="text"
                                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :class="{ 'border-red-500': errors.text_color }"
                                        />
                                    </div>
                                    <div v-if="errors.text_color" class="mt-1 text-sm text-red-600">
                                        {{ errors.text_color }}
                                    </div>
                                </div>
                            </div>

                            <!-- Color Preview -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Color Preview</h4>
                                <div class="flex space-x-4">
                                    <div class="text-center">
                                        <div
                                            class="w-16 h-16 rounded-lg border-2 border-gray-300"
                                            :style="{ backgroundColor: brandingForm.primary_color }"
                                        ></div>
                                        <p class="text-xs text-gray-600 mt-1">Primary</p>
                                    </div>
                                    <div class="text-center">
                                        <div
                                            class="w-16 h-16 rounded-lg border-2 border-gray-300"
                                            :style="{ backgroundColor: brandingForm.secondary_color }"
                                        ></div>
                                        <p class="text-xs text-gray-600 mt-1">Secondary</p>
                                    </div>
                                    <div class="text-center">
                                        <div
                                            class="w-16 h-16 rounded-lg border-2 border-gray-300"
                                            :style="{ backgroundColor: brandingForm.accent_color }"
                                        ></div>
                                        <p class="text-xs text-gray-600 mt-1">Accent</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="brandingForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <i v-if="brandingForm.processing" class="fas fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fas fa-save mr-2"></i>
                                    {{ brandingForm.processing ? 'Updating...' : 'Update Branding' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Business Settings Tab -->
                    <div v-if="activeTab === 'business'" class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-800">Business Settings</h3>

                        <form @submit.prevent="updateBusinessSettings" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Currency -->
                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700">
                                        Currency
                                    </label>
                                    <select
                                        id="currency"
                                        v-model="businessForm.currency"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.currency }"
                                    >
                                        <option value="USD">USD - US Dollar</option>
                                        <option value="EUR">EUR - Euro</option>
                                        <option value="GBP">GBP - British Pound</option>
                                        <option value="CAD">CAD - Canadian Dollar</option>
                                        <option value="AUD">AUD - Australian Dollar</option>
                                    </select>
                                    <div v-if="errors.currency" class="mt-1 text-sm text-red-600">
                                        {{ errors.currency }}
                                    </div>
                                </div>

                                <!-- Currency Symbol -->
                                <div>
                                    <label for="currency_symbol" class="block text-sm font-medium text-gray-700">
                                        Currency Symbol
                                    </label>
                                    <input
                                        id="currency_symbol"
                                        v-model="businessForm.currency_symbol"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.currency_symbol }"
                                        required
                                    />
                                    <div v-if="errors.currency_symbol" class="mt-1 text-sm text-red-600">
                                        {{ errors.currency_symbol }}
                                    </div>
                                </div>

                                <!-- Tax Rate -->
                                <div>
                                    <label for="tax_rate" class="block text-sm font-medium text-gray-700">
                                        Tax Rate ({{ taxRatePercentage }}%)
                                    </label>
                                    <input
                                        id="tax_rate"
                                        v-model="businessForm.tax_rate"
                                        type="number"
                                        step="0.0001"
                                        min="0"
                                        max="1"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.tax_rate }"
                                        required
                                    />
                                    <div v-if="errors.tax_rate" class="mt-1 text-sm text-red-600">
                                        {{ errors.tax_rate }}
                                    </div>
                                </div>

                                <!-- Tax Name -->
                                <div>
                                    <label for="tax_name" class="block text-sm font-medium text-gray-700">
                                        Tax Name
                                    </label>
                                    <input
                                        id="tax_name"
                                        v-model="businessForm.tax_name"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        :class="{ 'border-red-500': errors.tax_name }"
                                        required
                                    />
                                    <div v-if="errors.tax_name" class="mt-1 text-sm text-red-600">
                                        {{ errors.tax_name }}
                                    </div>
                                </div>

                                <!-- Delivery Fee -->
                                <div>
                                    <label for="delivery_fee" class="block text-sm font-medium text-gray-700">
                                        Delivery Fee
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">{{ businessForm.currency_symbol }}</span>
                                        </div>
                                        <input
                                            id="delivery_fee"
                                            v-model="businessForm.delivery_fee"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="pl-8 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :class="{ 'border-red-500': errors.delivery_fee }"
                                        />
                                    </div>
                                    <div v-if="errors.delivery_fee" class="mt-1 text-sm text-red-600">
                                        {{ errors.delivery_fee }}
                                    </div>
                                </div>

                                <!-- Minimum Order -->
                                <div>
                                    <label for="minimum_order" class="block text-sm font-medium text-gray-700">
                                        Minimum Order
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">{{ businessForm.currency_symbol }}</span>
                                        </div>
                                        <input
                                            id="minimum_order"
                                            v-model="businessForm.minimum_order"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="pl-8 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            :class="{ 'border-red-500': errors.minimum_order }"
                                        />
                                    </div>
                                    <div v-if="errors.minimum_order" class="mt-1 text-sm text-red-600">
                                        {{ errors.minimum_order }}
                                    </div>
                                </div>
                            </div>

                            <!-- Preparation Time -->
                            <div>
                                <label for="preparation_time" class="block text-sm font-medium text-gray-700">
                                    Preparation Time (minutes)
                                </label>
                                <input
                                    id="preparation_time"
                                    v-model="businessForm.preparation_time"
                                    type="number"
                                    min="1"
                                    max="480"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    :class="{ 'border-red-500': errors.preparation_time }"
                                    required
                                />
                                <div v-if="errors.preparation_time" class="mt-1 text-sm text-red-600">
                                    {{ errors.preparation_time }}
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="businessForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <i v-if="businessForm.processing" class="fas fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fas fa-save mr-2"></i>
                                    {{ businessForm.processing ? 'Updating...' : 'Update Business Settings' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Business Hours Tab -->
                    <div v-if="activeTab === 'hours'" class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-800">Business Hours</h3>

                        <form @submit.prevent="updateBusinessHours" class="space-y-6">
                            <div class="space-y-4">
                                <div v-for="day in days" :key="day.key" class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0 w-24">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ day.label }}
                                        </label>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <input
                                            :id="`${day.key}_closed`"
                                            type="checkbox"
                                            :checked="businessHoursForm.business_hours[day.key].closed"
                                            @change="toggleDayClosed(day.key)"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label :for="`${day.key}_closed`" class="text-sm text-gray-700">
                                            Closed
                                        </label>
                                    </div>

                                    <div v-if="!businessHoursForm.business_hours[day.key].closed" class="flex items-center space-x-2">
                                        <input
                                            :id="`${day.key}_open`"
                                            v-model="businessHoursForm.business_hours[day.key].open"
                                            type="time"
                                            class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                        <span class="text-gray-500">to</span>
                                        <input
                                            :id="`${day.key}_close`"
                                            v-model="businessHoursForm.business_hours[day.key].close"
                                            type="time"
                                            class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="businessHoursForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <i v-if="businessHoursForm.processing" class="fas fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fas fa-save mr-2"></i>
                                    {{ businessHoursForm.processing ? 'Updating...' : 'Update Business Hours' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Payment Methods Tab -->
                    <div v-if="activeTab === 'payment'" class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-800">Payment Methods</h3>

                        <form @submit.prevent="updatePaymentMethods" class="space-y-6">
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <input
                                            id="cash_payment"
                                            v-model="paymentForm.cash_payment"
                                            type="checkbox"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label for="cash_payment" class="text-sm font-medium text-gray-700">
                                            Cash Payment
                                        </label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input
                                            id="cash_method"
                                            type="checkbox"
                                            :checked="paymentForm.payment_methods.includes('cash')"
                                            @change="togglePaymentMethod('cash')"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label for="cash_method" class="text-sm text-gray-600">
                                            Include in payment methods
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <input
                                            id="card_payment"
                                            v-model="paymentForm.card_payment"
                                            type="checkbox"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label for="card_payment" class="text-sm font-medium text-gray-700">
                                            Card Payment
                                        </label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input
                                            id="card_method"
                                            type="checkbox"
                                            :checked="paymentForm.payment_methods.includes('card')"
                                            @change="togglePaymentMethod('card')"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label for="card_method" class="text-sm text-gray-600">
                                            Include in payment methods
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <input
                                            id="digital_wallet"
                                            v-model="paymentForm.digital_wallet"
                                            type="checkbox"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label for="digital_wallet" class="text-sm font-medium text-gray-700">
                                            Digital Wallet
                                        </label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input
                                            id="wallet_method"
                                            type="checkbox"
                                            :checked="paymentForm.payment_methods.includes('digital_wallet')"
                                            @change="togglePaymentMethod('digital_wallet')"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label for="wallet_method" class="text-sm text-gray-600">
                                            Include in payment methods
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="paymentForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <i v-if="paymentForm.processing" class="fas fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fas fa-save mr-2"></i>
                                    {{ paymentForm.processing ? 'Updating...' : 'Update Payment Methods' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Notifications Tab -->
                    <div v-if="activeTab === 'notifications'" class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-800">Notification Settings</h3>

                        <form @submit.prevent="updateNotificationSettings" class="space-y-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">Email Notifications</h4>
                                        <p class="text-sm text-gray-600">Send notifications via email</p>
                                    </div>
                                    <input
                                        id="email_notifications"
                                        v-model="notificationForm.email_notifications"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                </div>

                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">SMS Notifications</h4>
                                        <p class="text-sm text-gray-600">Send notifications via SMS</p>
                                    </div>
                                    <input
                                        id="sms_notifications"
                                        v-model="notificationForm.sms_notifications"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                </div>

                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">Push Notifications</h4>
                                        <p class="text-sm text-gray-600">Send push notifications to mobile devices</p>
                                    </div>
                                    <input
                                        id="push_notifications"
                                        v-model="notificationForm.push_notifications"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="notificationForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <i v-if="notificationForm.processing" class="fas fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fas fa-save mr-2"></i>
                                    {{ notificationForm.processing ? 'Updating...' : 'Update Notifications' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- System Settings Tab -->
                    <div v-if="activeTab === 'system'" class="space-y-6">
                        <h3 class="text-lg font-semibold text-gray-800">System Settings</h3>

                        <form @submit.prevent="updateSystemSettings" class="space-y-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">Maintenance Mode</h4>
                                        <p class="text-sm text-gray-600">Enable maintenance mode to temporarily disable the site</p>
                                    </div>
                                    <input
                                        id="maintenance_mode"
                                        v-model="systemForm.maintenance_mode"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                </div>

                                <div v-if="systemForm.maintenance_mode">
                                    <label for="maintenance_message" class="block text-sm font-medium text-gray-700">
                                        Maintenance Message
                                    </label>
                                    <textarea
                                        id="maintenance_message"
                                        v-model="systemForm.maintenance_message"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="Enter maintenance message..."
                                    ></textarea>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">Registration Enabled</h4>
                                        <p class="text-sm text-gray-600">Allow new users to register</p>
                                    </div>
                                    <input
                                        id="registration_enabled"
                                        v-model="systemForm.registration_enabled"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                </div>

                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">Guest Checkout</h4>
                                        <p class="text-sm text-gray-600">Allow customers to checkout without registration</p>
                                    </div>
                                    <input
                                        id="guest_checkout"
                                        v-model="systemForm.guest_checkout"
                                        type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                </div>

                                <div>
                                    <label for="session_timeout" class="block text-sm font-medium text-gray-700">
                                        Session Timeout (minutes)
                                    </label>
                                    <input
                                        id="session_timeout"
                                        v-model="systemForm.session_timeout"
                                        type="number"
                                        min="5"
                                        max="1440"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    />
                                    <p class="mt-1 text-sm text-gray-600">How long users stay logged in (5-1440 minutes)</p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="systemForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <i v-if="systemForm.processing" class="fas fa-spinner fa-spin mr-2"></i>
                                    <i v-else class="fas fa-save mr-2"></i>
                                    {{ systemForm.processing ? 'Updating...' : 'Update System Settings' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>
