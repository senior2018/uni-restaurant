<script setup>
import { computed } from 'vue'
import { useTheme } from '@/Composables/useTheme'

const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'warning', 'danger', 'error', 'info'].includes(value)
    },
    title: {
        type: String,
        default: null
    },
    dismissible: {
        type: Boolean,
        default: false
    },
    showIcon: {
        type: Boolean,
        default: true
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    }
})

const emit = defineEmits(['dismiss'])

const { getColor } = useTheme()

const alertClasses = computed(() => {
    const baseClasses = 'rounded-lg border p-4'

    // Size classes
    const sizeClasses = {
        sm: 'p-3',
        md: 'p-4',
        lg: 'p-6'
    }

    // Type classes
    const typeClasses = {
        success: 'bg-green-100 border-green-400 text-green-700',
        warning: 'bg-yellow-100 border-yellow-400 text-yellow-700',
        danger: 'bg-red-100 border-red-400 text-red-700',
        error: 'bg-red-100 border-red-400 text-red-700',
        info: 'bg-blue-100 border-blue-400 text-blue-700'
    }

    return [
        baseClasses,
        sizeClasses[props.size],
        typeClasses[props.type]
    ].join(' ')
})

const iconClasses = computed(() => {
    const baseClasses = 'mr-2'

    const typeClasses = {
        success: 'fas fa-check-circle',
        warning: 'fas fa-exclamation-triangle',
        danger: 'fas fa-exclamation-circle',
        error: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle'
    }

    return [baseClasses, typeClasses[props.type]].join(' ')
})

const titleClasses = computed(() => {
    const baseClasses = 'font-semibold'

    const sizeClasses = {
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg'
    }

    return [baseClasses, sizeClasses[props.size]].join(' ')
})

const contentClasses = computed(() => {
    const sizeClasses = {
        sm: 'text-sm',
        md: 'text-sm',
        lg: 'text-base'
    }

    return sizeClasses[props.size]
})

const dismissClasses = computed(() => {
    const baseClasses = 'ml-auto text-current hover:opacity-75'

    const sizeClasses = {
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg'
    }

    return [baseClasses, sizeClasses[props.size]].join(' ')
})

const handleDismiss = () => {
    emit('dismiss')
}
</script>

<template>
    <div :class="alertClasses" role="alert">
        <div class="flex items-start">
            <!-- Icon -->
            <i v-if="showIcon" :class="iconClasses"></i>

            <!-- Content -->
            <div class="flex-1">
                <!-- Title -->
                <h4 v-if="title" :class="titleClasses">{{ title }}</h4>

                <!-- Message -->
                <div :class="contentClasses">
                    <slot />
                </div>
            </div>

            <!-- Dismiss button -->
            <button
                v-if="dismissible"
                @click="handleDismiss"
                :class="dismissClasses"
                type="button"
                aria-label="Dismiss alert"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</template>
