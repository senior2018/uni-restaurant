<script setup>
import { computed } from 'vue'
import { useTheme } from '@/Composables/useTheme'

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'elevated', 'outlined', 'filled'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    clickable: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    }
})

const { getColor } = useTheme()

const cardClasses = computed(() => {
    const baseClasses = 'rounded-lg transition-all duration-200'

    // Size classes
    const sizeClasses = {
        sm: 'p-3',
        md: 'p-4 sm:p-6',
        lg: 'p-6 sm:p-8',
        xl: 'p-8 sm:p-10'
    }

    // Variant classes
    const variantClasses = {
        default: 'bg-white border border-gray-200',
        elevated: 'bg-white shadow-lg border border-gray-100',
        outlined: 'bg-white border-2 border-gray-300',
        filled: 'bg-gray-50 border border-gray-200'
    }

    // Interactive classes
    const interactiveClasses = props.clickable ? 'cursor-pointer hover:shadow-md hover:-translate-y-1' : ''

    return [
        baseClasses,
        sizeClasses[props.size],
        variantClasses[props.variant],
        interactiveClasses
    ].filter(Boolean).join(' ')
})

const headerClasses = computed(() => {
    return 'border-b border-gray-200 pb-4 mb-4'
})

const footerClasses = computed(() => {
    return 'border-t border-gray-200 pt-4 mt-4'
})
</script>

<template>
    <div :class="cardClasses" @click="$emit('click')">
        <!-- Loading Overlay -->
        <div v-if="loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
        </div>

        <!-- Header Slot -->
        <div v-if="$slots.header" :class="headerClasses">
            <slot name="header" />
        </div>

        <!-- Default Content -->
        <div v-if="!$slots.header && !$slots.footer">
            <slot />
        </div>

        <!-- Content with Header/Footer -->
        <div v-else class="space-y-4">
            <slot />
        </div>

        <!-- Footer Slot -->
        <div v-if="$slots.footer" :class="footerClasses">
            <slot name="footer" />
        </div>
    </div>
</template>
