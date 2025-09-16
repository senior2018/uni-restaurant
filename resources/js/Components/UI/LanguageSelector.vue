<script setup>
import { useI18n } from '@/Composables/useI18n';
import { onMounted } from 'vue';

const { currentLocale, availableLocales, setLocale, initializeLocale } = useI18n();

onMounted(() => {
  initializeLocale();
});

const handleLocaleChange = (event) => {
  setLocale(event.target.value);
};
</script>

<template>
  <select
    :value="currentLocale"
    @change="handleLocaleChange"
    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
  >
    <option
      v-for="locale in availableLocales"
      :key="locale.code"
      :value="locale.code"
    >
      {{ locale.name }}
    </option>
  </select>
</template>
