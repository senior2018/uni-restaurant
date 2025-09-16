<script setup>
import { computed, ref, onMounted } from 'vue'
import { useTheme } from '@/Composables/useTheme'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    label: {
        type: String,
        default: null
    },
    placeholder: {
        type: String,
        default: null
    },
    error: {
        type: String,
        default: null
    },
    disabled: {
        type: Boolean,
        default: false
    },
    required: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'filled', 'outlined'].includes(value)
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
    showPassword: {
        type: Boolean,
        default: false
    },
    autofocus: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur', 'input'])

const { getColor } = useTheme()
const inputRef = ref(null)

const inputClasses = computed(() => {
    const baseClasses = 'w-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500'

    // Size classes
    const sizeClasses = {
        sm: 'px-3 py-2 text-sm',
        md: 'px-4 py-3 text-base',
        lg: 'px-5 py-4 text-lg'
    }

    // Variant classes
    const variantClasses = {
        default: 'border border-gray-300 rounded-md bg-white',
        filled: 'border-0 rounded-md bg-gray-100 focus:bg-white',
        outlined: 'border-2 border-gray-300 rounded-md bg-white focus:border-green-500'
    }

    // State classes
    const stateClasses = props.error
        ? 'border-red-500 focus:ring-red-500'
        : props.disabled
        ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
        : ''

    // Icon padding
    const iconPadding = props.icon
        ? props.iconPosition === 'left' ? 'pl-10' : 'pr-10'
        : ''

    return [
        baseClasses,
        sizeClasses[props.size],
        variantClasses[props.variant],
        stateClasses,
        iconPadding
    ].filter(Boolean).join(' ')
})

const containerClasses = computed(() => {
    return 'relative'
})

const iconClasses = computed(() => {
    const baseClasses = 'absolute top-1/2 transform -translate-y-1/2 text-gray-400'
    const positionClasses = props.iconPosition === 'left' ? 'left-3' : 'right-3'

    return [baseClasses, positionClasses].join(' ')
})

const labelClasses = computed(() => {
    const baseClasses = 'block text-sm font-medium mb-2'
    const colorClasses = props.error ? 'text-red-700' : 'text-gray-700'

    return [baseClasses, colorClasses].join(' ')
})

const errorClasses = computed(() => {
    return 'mt-2 text-sm text-red-600'
})

const handleInput = (event) => {
    emit('update:modelValue', event.target.value)
    emit('input', event.target.value)
}

const handleFocus = (event) => {
    emit('focus', event)
}

const handleBlur = (event) => {
    emit('blur', event)
}

const focus = () => {
    inputRef.value?.focus()
}

onMounted(() => {
    if (props.autofocus) {
        setTimeout(() => {
            if (inputRef.value && document.activeElement === document.body) {
                inputRef.value.focus()
            }
        }, 100)
    }
})

defineExpose({ focus })
</script>

<template>
    <div class="space-y-2">
        <!-- Label -->
        <label v-if="label" :class="labelClasses">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>

        <!-- Input Container -->
        <div :class="containerClasses">
            <!-- Left Icon -->
            <i v-if="icon && iconPosition === 'left'" :class="[icon, iconClasses]"></i>

            <!-- Input Field -->
            <input
                ref="inputRef"
                :type="type === 'password' && showPassword ? 'text' : type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :required="required"
                :class="inputClasses"
                @input="handleInput"
                @focus="handleFocus"
                @blur="handleBlur"
            />

            <!-- Right Icon -->
            <i v-if="icon && iconPosition === 'right'" :class="[icon, iconClasses]"></i>
        </div>

        <!-- Error Message -->
        <p v-if="error" :class="errorClasses">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ error }}
        </p>
    </div>
</template>
