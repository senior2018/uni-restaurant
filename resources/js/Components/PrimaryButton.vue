<script setup>
import { computed } from 'vue'
import { useTheme } from '@/Composables/useTheme'

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'success', 'warning', 'danger', 'info'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    icon: {
        type: String,
        default: null
    },
    iconPosition: {
        type: String,
        default: 'left',
        validator: (value) => ['left', 'right'].includes(value)
    },
    fullWidth: {
        type: Boolean,
        default: false
    }
})

const { getColor } = useTheme()

const buttonClasses = computed(() => {
    const baseClasses = 'inline-flex items-center justify-center font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed'

    // Size classes
    const sizeClasses = {
        xs: 'px-2 py-1 text-xs rounded',
        sm: 'px-3 py-1.5 text-sm rounded-md',
        md: 'px-4 py-2 text-sm rounded-md',
        lg: 'px-6 py-3 text-base rounded-lg',
        xl: 'px-8 py-4 text-lg rounded-lg'
    }

    // Variant classes
    const variantClasses = {
        primary: 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        secondary: 'bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500',
        success: 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        warning: 'bg-yellow-600 hover:bg-yellow-700 text-white focus:ring-yellow-500',
        danger: 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        info: 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500'
    }

    // Width classes
    const widthClasses = props.fullWidth ? 'w-full' : ''

    return [
        baseClasses,
        sizeClasses[props.size],
        variantClasses[props.variant],
        widthClasses
    ].filter(Boolean).join(' ')
})
</script>

<template>
    <button
        :class="buttonClasses"
        :disabled="disabled || loading"
        v-bind="$attrs"
    >
        <!-- Loading spinner -->
        <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>

        <!-- Left icon -->
        <i v-else-if="icon && iconPosition === 'left'" :class="[icon, 'mr-2']"></i>

        <!-- Button content -->
        <slot />

        <!-- Right icon -->
        <i v-if="icon && iconPosition === 'right' && !loading" :class="[icon, 'ml-2']"></i>
    </button>
</template>
