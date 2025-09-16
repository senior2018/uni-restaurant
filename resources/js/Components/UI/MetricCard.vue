<script setup>
import { computed } from 'vue'
import { useTheme } from '@/Composables/useTheme'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: null
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'success', 'warning', 'danger', 'info', 'primary'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    loading: {
        type: Boolean,
        default: false
    },
    clickable: {
        type: Boolean,
        default: false
    }
})

const { getColor } = useTheme()

const cardClasses = computed(() => {
    const baseClasses = 'bg-white rounded-lg shadow-md transition-all duration-300 hover:shadow-lg'
    const clickableClasses = props.clickable ? 'cursor-pointer hover:-translate-y-1' : ''

    // Size classes
    const sizeClasses = {
        sm: 'p-3',
        md: 'p-4 sm:p-6',
        lg: 'p-6 sm:p-8'
    }

    // Variant classes for background
    const variantClasses = {
        default: '',
        success: 'bg-green-50 border border-green-200',
        warning: 'bg-yellow-50 border border-yellow-200',
        danger: 'bg-red-50 border border-red-200',
        info: 'bg-blue-50 border border-blue-200',
        primary: 'bg-green-50 border border-green-200'
    }

    return [
        baseClasses,
        clickableClasses,
        sizeClasses[props.size],
        variantClasses[props.variant]
    ].filter(Boolean).join(' ')
})

const iconClasses = computed(() => {
    const baseClasses = 'text-xl sm:text-2xl mb-2'

    const variantClasses = {
        default: 'text-gray-600',
        success: 'text-green-600',
        warning: 'text-yellow-600',
        danger: 'text-red-600',
        info: 'text-blue-600',
        primary: 'text-green-600'
    }

    return [baseClasses, variantClasses[props.variant]].join(' ')
})

const titleClasses = computed(() => {
    const baseClasses = 'text-base sm:text-lg font-semibold mb-2'

    const variantClasses = {
        default: 'text-gray-800',
        success: 'text-green-800',
        warning: 'text-yellow-800',
        danger: 'text-red-800',
        info: 'text-blue-800',
        primary: 'text-green-800'
    }

    return [baseClasses, variantClasses[props.variant]].join(' ')
})

const valueClasses = computed(() => {
    const baseClasses = 'text-xl sm:text-2xl font-bold'

    const variantClasses = {
        default: 'text-gray-900',
        success: 'text-green-700',
        warning: 'text-yellow-700',
        danger: 'text-red-700',
        info: 'text-blue-700',
        primary: 'text-green-700'
    }

    return [baseClasses, variantClasses[props.variant]].join(' ')
})

const subtitleClasses = computed(() => {
    return 'text-xs sm:text-sm text-gray-500 mt-1'
})
</script>

<template>
    <div :class="cardClasses" @click="$emit('click')">
        <!-- Icon -->
        <div v-if="icon" class="flex items-center justify-center mb-2">
            <i :class="[icon, iconClasses]"></i>
        </div>

        <!-- Content -->
        <div class="text-center">
            <!-- Title -->
            <h3 :class="titleClasses">{{ title }}</h3>

            <!-- Value -->
            <div v-if="loading" class="flex items-center justify-center">
                <i class="fas fa-spinner fa-spin text-gray-400"></i>
            </div>
            <p v-else :class="valueClasses">{{ value }}</p>

            <!-- Subtitle -->
            <p v-if="subtitle" :class="subtitleClasses">{{ subtitle }}</p>
        </div>

        <!-- Badge slot for additional info -->
        <div v-if="$slots.badge" class="absolute top-2 right-2">
            <slot name="badge" />
        </div>
    </div>
</template>
