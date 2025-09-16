<script setup>
import SuperAdminLayout from './Layout.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    admins: Object
});

const deleteAdmin = (admin) => {
    if (confirm(`Are you sure you want to delete admin "${admin.name}"? This action cannot be undone.`)) {
        router.delete(route('superadmin.admins.destroy', admin.id));
    }
};

// Table columns configuration
const columns = [
    { key: 'name', label: 'Name', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'role', label: 'Role', type: 'status', sortable: true },
    { key: 'created_at', label: 'Created', type: 'date', sortable: true },
    { key: 'actions', label: 'Actions', slot: 'actions', sortable: false }
];
</script>

<template>
    <SuperAdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-responsive-lg font-bold text-gray-800">Admin Management</h2>
                <Link :href="route('superadmin.admins.create')"
                      class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create New Admin
                </Link>
            </div>

            <!-- Admins Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">System Administrators</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage all admin users in the system</p>
                </div>

                <DataTable
                    :data="props.admins?.data || []"
                    :columns="columns"
                    :pagination="props.admins"
                    searchable
                    sortable
                    default-sort="created_at"
                    default-sort-direction="desc"
                >
                    <template #actions="{ item: admin }">
                        <div class="flex space-x-2">
                            <Link :href="route('superadmin.admins.edit', admin.id)">
                                <PrimaryButton
                                    variant="info"
                                    size="xs"
                                    icon="fas fa-edit"
                                >
                                    Edit
                                </PrimaryButton>
                            </Link>
                            <PrimaryButton
                                @click="deleteAdmin(admin)"
                                variant="danger"
                                size="xs"
                                icon="fas fa-trash"
                            >
                                Delete
                            </PrimaryButton>
                        </div>
                    </template>
                </DataTable>

                <!-- Pagination -->
                <div v-if="props.admins?.links" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="props.admins?.links && props.admins.links[0] && props.admins.links[0].url"
                                  :href="props.admins.links[0].url"
                                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </Link>
                            <Link v-if="props.admins?.links && props.admins.links[props.admins.links.length - 1] && props.admins.links[props.admins.links.length - 1].url"
                                  :href="props.admins.links[props.admins.links.length - 1].url"
                                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{ props.admins?.from || 0 }}</span>
                                    to
                                    <span class="font-medium">{{ props.admins?.to || 0 }}</span>
                                    of
                                    <span class="font-medium">{{ props.admins?.total || 0 }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="link in (props.admins?.links || [])" :key="link?.label || 'link'">
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

            <!-- Empty State -->
            <div v-if="(props.admins?.data || []).length === 0" class="text-center py-12">
                <i class="fas fa-users-cog text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No admins found</h3>
                <p class="text-gray-500 mb-6">Get started by creating your first admin user.</p>
                <Link :href="route('superadmin.admins.create')"
                      class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Admin
                </Link>
            </div>
        </div>
    </SuperAdminLayout>
</template>
