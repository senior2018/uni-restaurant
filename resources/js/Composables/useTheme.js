import { ref, computed } from 'vue'

// Light theme design tokens
const lightDesignTokens = {
  colors: {
    primary: {
      50: '#ECFDF5',   // Light green background
      100: '#D1FAE5',
      200: '#A7F3D0',
      300: '#6EE7B7',
      400: '#34D399',
      500: '#10B981',  // Main primary color
      600: '#059669',  // Secondary green
      700: '#047857',
      800: '#065F46',
      900: '#064E3B',
    },
    secondary: {
      500: '#F59E0B',  // Accent color
      600: '#D97706',
      700: '#B45309',
    },
    status: {
      success: '#10B981',
      warning: '#F59E0B',
      error: '#EF4444',
      info: '#3B82F6',
    },
    neutral: {
      50: '#F9FAFB',
      100: '#F3F4F6',
      200: '#E5E7EB',
      300: '#D1D5DB',
      400: '#9CA3AF',
      500: '#6B7280',
      600: '#4B5563',
      700: '#374151',
      800: '#1F2937',
      900: '#111827',
    },
    // Semantic colors for light theme
    background: '#F9FAFB',
    surface: '#FFFFFF',
    text: '#1F2937',
    textSecondary: '#6B7280',
    border: '#E5E7EB',
    shadow: 'rgba(0, 0, 0, 0.1)',
  },
  spacing: {
    xs: '0.5rem',
    sm: '1rem',
    md: '1.5rem',
    lg: '2rem',
    xl: '3rem',
  },
  typography: {
    heading: {
      h1: 'text-4xl font-bold',
      h2: 'text-3xl font-semibold',
      h3: 'text-2xl font-medium',
      h4: 'text-xl font-medium',
    },
    body: {
      large: 'text-lg',
      base: 'text-base',
      small: 'text-sm',
      xs: 'text-xs',
    }
  },
  borderRadius: {
    sm: '0.375rem',
    md: '0.5rem',
    lg: '0.75rem',
    xl: '1rem',
    '2xl': '1.5rem',
  },
  shadows: {
    sm: 'shadow-sm',
    md: 'shadow-md',
    lg: 'shadow-lg',
    xl: 'shadow-xl',
  }
}

// Dark theme design tokens
const darkDesignTokens = {
  colors: {
    primary: {
      50: '#064E3B',
      100: '#065F46',
      200: '#047857',
      300: '#059669',
      400: '#10B981',
      500: '#34D399',  // Main primary color for dark
      600: '#6EE7B7',
      700: '#A7F3D0',
      800: '#D1FAE5',
      900: '#ECFDF5',
    },
    secondary: {
      500: '#FBBF24',  // Accent color for dark
      600: '#F59E0B',
      700: '#D97706',
    },
    status: {
      success: '#34D399',
      warning: '#FBBF24',
      error: '#F87171',
      info: '#60A5FA',
    },
    neutral: {
      50: '#111827',
      100: '#1F2937',
      200: '#374151',
      300: '#4B5563',
      400: '#6B7280',
      500: '#9CA3AF',
      600: '#D1D5DB',
      700: '#E5E7EB',
      800: '#F3F4F6',
      900: '#F9FAFB',
    },
    // Semantic colors for dark theme
    background: '#111827',
    surface: '#1F2937',
    text: '#F9FAFB',
    textSecondary: '#D1D5DB',
    border: '#374151',
    shadow: 'rgba(0, 0, 0, 0.3)',
  },
  spacing: lightDesignTokens.spacing,
  typography: lightDesignTokens.typography,
  borderRadius: lightDesignTokens.borderRadius,
  shadows: lightDesignTokens.shadows
}

// Theme state
const currentTheme = ref('light')
const isDarkMode = ref(false)

// Computed current design tokens based on theme
const designTokens = computed(() => {
  return currentTheme.value === 'dark' ? darkDesignTokens : lightDesignTokens
})

// Theme composable
export function useTheme() {
  const setTheme = (theme) => {
    currentTheme.value = theme
    isDarkMode.value = theme === 'dark'

    // Update document class for global styling
    if (typeof document !== 'undefined') {
      document.documentElement.classList.toggle('dark', theme === 'dark')
    }

    // Save to localStorage
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('theme', theme)
    }
  }

  const toggleTheme = () => {
    setTheme(currentTheme.value === 'light' ? 'dark' : 'light')
  }

  const getColor = (colorPath) => {
    const keys = colorPath.split('.')
    let color = designTokens.value.colors
    for (const key of keys) {
      color = color[key]
      if (!color) return null
    }
    return color
  }

  const getSpacing = (size) => {
    return designTokens.value.spacing[size] || size
  }

  const getTypography = (type, variant) => {
    return designTokens.value.typography[type]?.[variant] || ''
  }

  const getBorderRadius = (size) => {
    return designTokens.value.borderRadius[size] || size
  }

  const getShadow = (size) => {
    return designTokens.value.shadows[size] || size
  }

  // Initialize theme from localStorage
  const initializeTheme = () => {
    if (typeof localStorage !== 'undefined') {
      const savedTheme = localStorage.getItem('theme')
      if (savedTheme && ['light', 'dark'].includes(savedTheme)) {
        setTheme(savedTheme)
      } else {
        // Check system preference
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        setTheme(prefersDark ? 'dark' : 'light')
      }
    }
  }

  return {
    currentTheme,
    isDarkMode,
    designTokens,
    setTheme,
    toggleTheme,
    initializeTheme,
    getColor,
    getSpacing,
    getTypography,
    getBorderRadius,
    getShadow,
  }
}

// Export design tokens for direct access
export { designTokens }
