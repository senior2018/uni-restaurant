<script setup>
import { computed, ref } from 'vue'
import StatusBadge from './StatusBadge.vue'

const props = defineProps({
    data: {
        type: Array,
        required: true
    },
    columns: {
        type: Array,
        required: true
    },
    loading: {
        type: Boolean,
        default: false
    },
    searchable: {
        type: Boolean,
        default: false
    },
    sortable: {
        type: Boolean,
        default: false
    },
    pagination: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['sort', 'search', 'page-change'])

const searchTerm = ref('')
const sortField = ref('')
const sortDirection = ref('asc')

const filteredData = computed(() => {
    if (!props.searchable || !searchTerm.value) return props.data

    return props.data.filter(item => {
        return props.columns.some(column => {
            const value = item[column.key]
            return value && value.toString().toLowerCase().includes(searchTerm.value.toLowerCase())
        })
    })
})

const handleSort = (field) => {
    if (!props.sortable) return

    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortField.value = field
        sortDirection.value = 'asc'
    }

    emit('sort', { field, direction: sortDirection.value })
}

const handleSearch = () => {
    emit('search', searchTerm.value)
}

const getCellValue = (item, column) => {
    if (column.key.includes('.')) {
        return column.key.split('.').reduce((obj, key) => obj?.[key], item)
    }
    return item[column.key]
}

const getCellClasses = (column) => {
    const baseClasses = 'px-6 py-4 whitespace-nowrap text-sm'

    if (column.align === 'center') return `${baseClasses} text-center`
    if (column.align === 'right') return `${baseClasses} text-right`
    return `${baseClasses} text-left`
}
</script>

<template>
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Search Bar -->
        <div v-if="searchable" class="p-4 border-b border-gray-200">
            <div class="relative">
                <input
                    v-model="searchTerm"
                    @input="handleSearch"
                    type="text"
                    placeholder="Search..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="p-8 text-center">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
            <p class="text-gray-500">Loading...</p>
        </div>

        <!-- Table -->
        <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            :class="[
                                'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
                                sortable && column.sortable !== false ? 'cursor-pointer hover:bg-gray-100' : ''
                            ]"
                            @click="column.sortable !== false && handleSort(column.key)"
                        >
                            <div class="flex items-center space-x-1">
                                <span>{{ column.label }}</span>
                                <i
                                    v-if="sortable && column.sortable !== false"
                                    :class="[
                                        'fas fa-sort text-gray-400',
                                        sortField === column.key ? 'text-gray-600' : ''
                                    ]"
                                ></i>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                        v-for="(item, index) in filteredData"
                        :key="index"
                        class="hover:bg-gray-50 transition-colors"
                    >
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            :class="getCellClasses(column)"
                        >
                            <!-- Status Badge -->
                            <StatusBadge
                                v-if="column.type === 'status'"
                                :status="getCellValue(item, column)"
                                :variant="column.variant || 'auto'"
                                size="sm"
                            />

                            <!-- Date -->
                            <span v-else-if="column.type === 'date'">
                                {{ new Date(getCellValue(item, column)).toLocaleDateString() }}
                            </span>

                            <!-- Currency -->
                            <span v-else-if="column.type === 'currency'">
                                ${{ Number(getCellValue(item, column)).toFixed(2) }}
                            </span>

                            <!-- Number -->
                            <span v-else-if="column.type === 'number'">
                                {{ Number(getCellValue(item, column)).toLocaleString() }}
                            </span>

                            <!-- Custom Render -->
                            <slot
                                v-else-if="column.slot"
                                :name="column.slot"
                                :item="item"
                                :value="getCellValue(item, column)"
                            />

                            <!-- Default Text -->
                            <span v-else>
                                {{ getCellValue(item, column) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination" class="px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
                </div>
                <div class="flex space-x-2">
                    <button
                        v-for="link in pagination.links"
                        :key="link.label"
                        :disabled="!link.url"
                        :class="[
                            'px-3 py-1 text-sm rounded-md',
                            link.url
                                ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        ]"
                        @click="link.url && emit('page-change', link.url)"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
