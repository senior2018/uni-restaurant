<script setup>
import { computed } from 'vue'
import { useTheme } from '@/Composables/useTheme'

const props = defineProps({
    status: {
        type: String,
        required: true
    },
    variant: {
        type: String,
        default: 'auto',
        validator: (value) => ['auto', 'success', 'warning', 'danger', 'error', 'info', 'primary', 'secondary'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
    },
    icon: {
        type: String,
        default: null
    },
    showIcon: {
        type: Boolean,
        default: true
    }
})

const { getColor } = useTheme()

// Auto-detect variant based on status if not explicitly set
const computedVariant = computed(() => {
    if (props.variant !== 'auto') {
        // Map 'error' variant to 'danger' for consistency
        return props.variant === 'error' ? 'danger' : props.variant
    }

    const status = props.status.toLowerCase()

    // Success variants
    if (['active', 'completed', 'delivered', 'approved', 'success', 'online', 'available'].includes(status)) {
        return 'success'
    }

    // Warning variants
    if (['pending', 'processing', 'preparing', 'warning', 'in-progress', 'waiting'].includes(status)) {
        return 'warning'
    }

    // Danger variants
    if (['cancelled', 'failed', 'error', 'offline', 'unavailable', 'rejected', 'expired'].includes(status)) {
        return 'danger'
    }

    // Info variants
    if (['info', 'new', 'updated', 'draft', 'scheduled'].includes(status)) {
        return 'info'
    }

    // Default to primary
    return 'primary'
})

// Auto-detect icon based on status if not explicitly set
const computedIcon = computed(() => {
    if (props.icon) return props.icon
    if (!props.showIcon) return null

    const status = props.status.toLowerCase()

    // Success icons
    if (['active', 'completed', 'delivered', 'approved', 'success', 'online', 'available'].includes(status)) {
        return 'fas fa-check-circle'
    }

    // Warning icons
    if (['pending', 'processing', 'preparing', 'warning', 'in-progress', 'waiting'].includes(status)) {
        return 'fas fa-clock'
    }

    // Danger icons
    if (['cancelled', 'failed', 'error', 'offline', 'unavailable', 'rejected', 'expired'].includes(status)) {
        return 'fas fa-times-circle'
    }

    // Info icons
    if (['info', 'new', 'updated', 'draft', 'scheduled'].includes(status)) {
        return 'fas fa-info-circle'
    }

    // Default icon
    return 'fas fa-circle'
})

const badgeClasses = computed(() => {
    const baseClasses = 'inline-flex items-center font-medium rounded-full'

    // Size classes
    const sizeClasses = {
        xs: 'px-1.5 py-0.5 text-xs',
        sm: 'px-2 py-1 text-xs',
        md: 'px-2.5 py-1.5 text-sm',
        lg: 'px-3 py-2 text-base'
    }

    // Variant classes
    const variantClasses = {
        success: 'bg-green-100 text-green-800',
        warning: 'bg-yellow-100 text-yellow-800',
        danger: 'bg-red-100 text-red-800',
        info: 'bg-blue-100 text-blue-800',
        primary: 'bg-green-100 text-green-800',
        secondary: 'bg-gray-100 text-gray-800'
    }

    return [
        baseClasses,
        sizeClasses[props.size],
        variantClasses[computedVariant.value]
    ].join(' ')
})

const iconClasses = computed(() => {
    const sizeClasses = {
        xs: 'w-3 h-3',
        sm: 'w-3 h-3',
        md: 'w-4 h-4',
        lg: 'w-5 h-5'
    }

    return [
        computedIcon.value,
        sizeClasses[props.size],
        'mr-1'
    ].join(' ')
})
</script>

<template>
    <span :class="badgeClasses">
        <i v-if="computedIcon" :class="iconClasses"></i>
        {{ status }}
    </span>
</template>
