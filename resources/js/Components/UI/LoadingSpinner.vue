<script setup>
import { computed } from 'vue'
import { useTheme } from '@/Composables/useTheme'

const props = defineProps({
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
    },
    color: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'white', 'gray'].includes(value)
    },
    text: {
        type: String,
        default: null
    },
    overlay: {
        type: Boolean,
        default: false
    }
})

const { getColor } = useTheme()

const spinnerClasses = computed(() => {
    const baseClasses = 'animate-spin'

    // Size classes
    const sizeClasses = {
        xs: 'w-3 h-3',
        sm: 'w-4 h-4',
        md: 'w-6 h-6',
        lg: 'w-8 h-8',
        xl: 'w-12 h-12'
    }

    // Color classes
    const colorClasses = {
        primary: 'text-green-600',
        secondary: 'text-gray-600',
        white: 'text-white',
        gray: 'text-gray-400'
    }

    return [
        baseClasses,
        sizeClasses[props.size],
        colorClasses[props.color]
    ].join(' ')
})

const containerClasses = computed(() => {
    const baseClasses = 'flex items-center justify-center'

    if (props.overlay) {
        return [baseClasses, 'fixed inset-0 bg-black bg-opacity-50 z-50'].join(' ')
    }

    return baseClasses
})

const textClasses = computed(() => {
    const baseClasses = 'ml-2'

    const sizeClasses = {
        xs: 'text-xs',
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg',
        xl: 'text-xl'
    }

    const colorClasses = {
        primary: 'text-green-600',
        secondary: 'text-gray-600',
        white: 'text-white',
        gray: 'text-gray-400'
    }

    return [
        baseClasses,
        sizeClasses[props.size],
        colorClasses[props.color]
    ].join(' ')
})
</script>

<template>
    <div :class="containerClasses">
        <svg
            :class="spinnerClasses"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>

        <span v-if="text" :class="textClasses">{{ text }}</span>
    </div>
</template>
