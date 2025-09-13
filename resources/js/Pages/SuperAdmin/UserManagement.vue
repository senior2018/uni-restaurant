<script setup>
import SuperAdminLayout from './Layout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    users: Object
});

const roleForm = useForm({
    role: ''
});

const updateUserRole = (user, newRole) => {
    if (confirm(`Are you sure you want to change ${user.name}'s role to ${newRole}?`)) {
        roleForm.role = newRole;
        roleForm.put(route('superadmin.users.role', user.id));
    }
};

const getRoleColor = (role) => {
    switch (role) {
        case 'super_admin':
            return 'bg-red-100 text-red-800';
        case 'admin':
            return 'bg-purple-100 text-purple-800';
        case 'staff':
            return 'bg-green-100 text-green-800';
        case 'customer':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getRoleIcon = (role) => {
    switch (role) {
        case 'super_admin':
            return 'fas fa-crown';
        case 'admin':
            return 'fas fa-user-shield';
        case 'staff':
            return 'fas fa-user-tie';
        case 'customer':
            return 'fas fa-user';
        default:
            return 'fas fa-user';
    }
};
</script>

<template>
    <SuperAdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-responsive-lg font-bold text-gray-800">User Management</h2>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-users mr-1"></i>
                    Manage all system users
                </div>
            </div>

            <!-- User Statistics -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ props.users?.total || 0 }}</div>
                            <div class="text-sm text-gray-600">Total Users</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ props.users?.data?.filter(u => u.role === 'customer').length || 0 }}
                            </div>
                            <div class="text-sm text-gray-600">Customers</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-tie text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ props.users?.data?.filter(u => u.role === 'staff').length || 0 }}
                            </div>
                            <div class="text-sm text-gray-600">Staff</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-shield text-purple-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ props.users?.data?.filter(u => u.role === 'admin').length || 0 }}
                            </div>
                            <div class="text-sm text-gray-600">Admins</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">All Users</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage user roles and permissions</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contact
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Activity
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Joined
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in (props.users?.data || [])" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                <i :class="getRoleIcon(user.role)" class="text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                            <div class="text-sm text-gray-500">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="getRoleColor(user.role)">
                                        <i :class="getRoleIcon(user.role)" class="mr-1"></i>
                                        {{ user.role.replace('_', ' ').toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ user.phone || 'Not provided' }}</div>
                                    <div class="text-sm text-gray-500">{{ user.permanent_location || 'Not provided' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div>{{ user.orders_count || 0 }} orders</div>
                                        <div>{{ user.ratings_count || 0 }} ratings</div>
                                        <div>{{ user.alerts_count || 0 }} alerts</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ new Date(user.created_at).toLocaleDateString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-1">
                                        <!-- Role Change Dropdown -->
                                        <select v-if="user.role !== 'super_admin'"
                                                @change="updateUserRole(user, $event.target.value)"
                                                :value="user.role"
                                                class="text-xs border border-gray-300 rounded px-2 py-1">
                                            <option value="customer">Customer</option>
                                            <option value="staff">Staff</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                        <span v-else class="text-xs text-gray-500">Super Admin</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="props.users?.links" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="props.users?.links && props.users.links[0] && props.users.links[0].url"
                                  :href="props.users.links[0].url"
                                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="props.users?.links && props.users.links[props.users.links.length - 1] && props.users.links[props.users.links.length - 1].url"
                                  :href="props.users.links[props.users.links.length - 1].url"
                                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{ props.users?.from || 0 }}</span>
                                    to
                                    <span class="font-medium">{{ props.users?.to || 0 }}</span>
                                    of
                                    <span class="font-medium">{{ props.users?.total || 0 }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="link in (props.users?.links || [])" :key="link?.label || 'link'">
                                        <Link v-if="link && link.url"
                                              :href="link.url"
                                              v-html="link.label"
                                              :class="[
                                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                  link.active
                                                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                              ]">
                                        </Link>
                                        <span v-else-if="link && !link.url"
                                              v-html="link.label"
                                              :class="[
                                                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium cursor-not-allowed opacity-50',
                                                  'bg-white border-gray-300 text-gray-500'
                                              ]">
                                        </span>
                                    </template>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Role Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-crown text-red-600 mr-2"></i>
                                <span class="font-medium">Super Admin</span>
                            </div>
                            <p class="text-sm text-gray-600">Full system access, cannot be modified</p>
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user-shield text-purple-600 mr-2"></i>
                                <span class="font-medium">Admin</span>
                            </div>
                            <p class="text-sm text-gray-600">Restaurant management, user management</p>
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user-tie text-green-600 mr-2"></i>
                                <span class="font-medium">Staff</span>
                            </div>
                            <p class="text-sm text-gray-600">Order management, meal availability</p>
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user text-blue-600 mr-2"></i>
                                <span class="font-medium">Customer</span>
                            </div>
                            <p class="text-sm text-gray-600">Order placement, ratings, support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>
