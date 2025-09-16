import { ref, computed } from 'vue';

// Translation data
const translations = {
  en: {
    // Common
    common: {
      save: 'Save',
      cancel: 'Cancel',
      delete: 'Delete',
      edit: 'Edit',
      create: 'Create',
      update: 'Update',
      search: 'Search',
      loading: 'Loading...',
      error: 'Error',
      success: 'Success',
      warning: 'Warning',
      info: 'Information',
      confirm: 'Confirm',
      yes: 'Yes',
      no: 'No',
      close: 'Close',
      back: 'Back',
      next: 'Next',
      previous: 'Previous',
      submit: 'Submit',
      reset: 'Reset',
      clear: 'Clear',
      select: 'Select',
      all: 'All',
      none: 'None',
      required: 'Required',
      optional: 'Optional'
    },

    // Navigation
    nav: {
      home: 'Home',
      dashboard: 'Dashboard',
      menu: 'Menu',
      orders: 'Orders',
      profile: 'Profile',
      settings: 'Settings',
      logout: 'Logout',
      login: 'Login',
      register: 'Register'
    },

    // Dashboard
    dashboard: {
      title: 'Dashboard',
      welcome: 'Welcome',
      totalUsers: 'Total Users',
      activeOrders: 'Active Orders',
      revenue: 'Revenue',
      issues: 'Issues',
      recentActivity: 'Recent Activity',
      quickActions: 'Quick Actions'
    },

    // Orders
    orders: {
      title: 'Orders',
      orderNumber: 'Order #',
      status: 'Status',
      customer: 'Customer',
      total: 'Total',
      date: 'Date',
      actions: 'Actions',
      pending: 'Pending',
      preparing: 'Preparing',
      completed: 'Completed',
      cancelled: 'Cancelled',
      delivered: 'Delivered'
    },

    // Status
    status: {
      active: 'Active',
      inactive: 'Inactive',
      pending: 'Pending',
      approved: 'Approved',
      rejected: 'Rejected',
      success: 'Success',
      error: 'Error',
      warning: 'Warning',
      info: 'Info'
    },

    // Forms
    forms: {
      email: 'Email',
      password: 'Password',
      confirmPassword: 'Confirm Password',
      name: 'Name',
      phone: 'Phone',
      address: 'Address',
      city: 'City',
      country: 'Country',
      zipCode: 'ZIP Code',
      required: 'This field is required',
      invalidEmail: 'Please enter a valid email address',
      passwordTooShort: 'Password must be at least 8 characters',
      passwordsDoNotMatch: 'Passwords do not match'
    },

    // Theme
    theme: {
      light: 'Light',
      dark: 'Dark',
      system: 'System',
      toggleTheme: 'Toggle theme'
    }
  },

  es: {
    // Common
    common: {
      save: 'Guardar',
      cancel: 'Cancelar',
      delete: 'Eliminar',
      edit: 'Editar',
      create: 'Crear',
      update: 'Actualizar',
      search: 'Buscar',
      loading: 'Cargando...',
      error: 'Error',
      success: 'Éxito',
      warning: 'Advertencia',
      info: 'Información',
      confirm: 'Confirmar',
      yes: 'Sí',
      no: 'No',
      close: 'Cerrar',
      back: 'Atrás',
      next: 'Siguiente',
      previous: 'Anterior',
      submit: 'Enviar',
      reset: 'Restablecer',
      clear: 'Limpiar',
      select: 'Seleccionar',
      all: 'Todos',
      none: 'Ninguno',
      required: 'Requerido',
      optional: 'Opcional'
    },

    // Navigation
    nav: {
      home: 'Inicio',
      dashboard: 'Panel',
      menu: 'Menú',
      orders: 'Pedidos',
      profile: 'Perfil',
      settings: 'Configuración',
      logout: 'Cerrar Sesión',
      login: 'Iniciar Sesión',
      register: 'Registrarse'
    },

    // Dashboard
    dashboard: {
      title: 'Panel de Control',
      welcome: 'Bienvenido',
      totalUsers: 'Total de Usuarios',
      activeOrders: 'Pedidos Activos',
      revenue: 'Ingresos',
      issues: 'Problemas',
      recentActivity: 'Actividad Reciente',
      quickActions: 'Acciones Rápidas'
    },

    // Orders
    orders: {
      title: 'Pedidos',
      orderNumber: 'Pedido #',
      status: 'Estado',
      customer: 'Cliente',
      total: 'Total',
      date: 'Fecha',
      actions: 'Acciones',
      pending: 'Pendiente',
      preparing: 'Preparando',
      completed: 'Completado',
      cancelled: 'Cancelado',
      delivered: 'Entregado'
    },

    // Status
    status: {
      active: 'Activo',
      inactive: 'Inactivo',
      pending: 'Pendiente',
      approved: 'Aprobado',
      rejected: 'Rechazado',
      success: 'Éxito',
      error: 'Error',
      warning: 'Advertencia',
      info: 'Info'
    },

    // Forms
    forms: {
      email: 'Correo Electrónico',
      password: 'Contraseña',
      confirmPassword: 'Confirmar Contraseña',
      name: 'Nombre',
      phone: 'Teléfono',
      address: 'Dirección',
      city: 'Ciudad',
      country: 'País',
      zipCode: 'Código Postal',
      required: 'Este campo es requerido',
      invalidEmail: 'Por favor ingrese un correo electrónico válido',
      passwordTooShort: 'La contraseña debe tener al menos 8 caracteres',
      passwordsDoNotMatch: 'Las contraseñas no coinciden'
    },

    // Theme
    theme: {
      light: 'Claro',
      dark: 'Oscuro',
      system: 'Sistema',
      toggleTheme: 'Cambiar tema'
    }
  }
};

// Current locale state
const currentLocale = ref('en');

// I18n composable
export function useI18n() {
  const setLocale = (locale) => {
    if (translations[locale]) {
      currentLocale.value = locale;

      // Save to localStorage
      if (typeof localStorage !== 'undefined') {
        localStorage.setItem('locale', locale);
      }

      // Update document language
      if (typeof document !== 'undefined') {
        document.documentElement.lang = locale;
      }
    }
  };

  const t = (key, params = {}) => {
    const keys = key.split('.');
    let translation = translations[currentLocale.value];

    for (const k of keys) {
      translation = translation?.[k];
      if (!translation) {
        console.warn(`Translation missing for key: ${key}`);
        return key; // Return key if translation not found
      }
    }

    // Replace parameters in translation
    if (typeof translation === 'string' && Object.keys(params).length > 0) {
      return translation.replace(/\{\{(\w+)\}\}/g, (match, param) => {
        return params[param] || match;
      });
    }

    return translation;
  };

  const initializeLocale = () => {
    if (typeof localStorage !== 'undefined') {
      const savedLocale = localStorage.getItem('locale');
      if (savedLocale && translations[savedLocale]) {
        setLocale(savedLocale);
      } else {
        // Check browser language
        const browserLang = navigator.language.split('-')[0];
        if (translations[browserLang]) {
          setLocale(browserLang);
        }
      }
    }
  };

  const availableLocales = computed(() => {
    return Object.keys(translations).map(locale => ({
      code: locale,
      name: locale === 'en' ? 'English' : locale === 'es' ? 'Español' : locale
    }));
  });

  return {
    currentLocale,
    availableLocales,
    setLocale,
    t,
    initializeLocale
  };
}

// Export translations for direct access
export { translations };
