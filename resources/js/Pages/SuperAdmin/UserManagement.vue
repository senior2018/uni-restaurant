<script setup>
import SuperAdminLayout from './Layout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
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

// Table columns configuration
const columns = [
    { key: 'name', label: 'Name', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'role', label: 'Role', type: 'status', sortable: true },
    { key: 'created_at', label: 'Joined', type: 'date', sortable: true },
    { key: 'actions', label: 'Actions', slot: 'actions', sortable: false }
];

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

                <DataTable
                    :data="props.users?.data || []"
                    :columns="columns"
                    :pagination="props.users"
                    searchable
                    sortable
                >
                    <template #actions="{ item: user }">
                        <div class="flex space-x-2">
                            <select
                                :value="user.role"
                                @change="updateUserRole(user, $event.target.value)"
                                class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            >
                                <option value="customer">Customer</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                    </template>
                </DataTable>
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
