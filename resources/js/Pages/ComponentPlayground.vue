<script setup>
import { ref, computed } from 'vue';
import MetricCard from '@/Components/UI/MetricCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import Alert from '@/Components/UI/Alert.vue';
import LoadingSpinner from '@/Components/UI/LoadingSpinner.vue';
import Card from '@/Components/UI/Card.vue';
import Input from '@/Components/UI/Input.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import ThemeToggle from '@/Components/UI/ThemeToggle.vue';
import AnimatedCard from '@/Components/UI/AnimatedCard.vue';
import LanguageSelector from '@/Components/UI/LanguageSelector.vue';
import { useTheme } from '@/Composables/useTheme';
import { useI18n } from '@/Composables/useI18n';

const { currentTheme, isDarkMode } = useTheme();
const { currentLocale, t } = useI18n();

// Sample data for components
const sampleData = ref([
  { id: 1, name: 'John Doe', email: 'john@example.com', role: 'admin', created_at: '2024-01-15' },
  { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'staff', created_at: '2024-01-16' },
  { id: 3, name: 'Bob Johnson', email: 'bob@example.com', role: 'customer', created_at: '2024-01-17' }
]);

const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'role', label: 'Role', type: 'status', sortable: true },
  { key: 'created_at', label: 'Created', type: 'date', sortable: true }
];

const inputValue = ref('');
const showAlert = ref(false);
const isLoading = ref(false);

const toggleLoading = () => {
  isLoading.value = !isLoading.value;
};

const toggleAlert = () => {
  showAlert.value = !showAlert.value;
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Component Playground</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Test and explore all UI components</p>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ t('theme.toggleTheme') }}:</span>
            <ThemeToggle />
            <span class="text-sm text-gray-600 dark:text-gray-400">Language:</span>
            <LanguageSelector />
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="space-y-12">

        <!-- Theme Toggle Section -->
        <AnimatedCard animation="fadeIn" trigger="onMount">
          <Card title="Theme Toggle" subtitle="Switch between light and dark modes">
            <div class="flex items-center space-x-4">
              <span class="text-sm text-gray-600 dark:text-gray-400">Current theme: {{ currentTheme }}</span>
              <ThemeToggle />
            </div>
          </Card>
        </AnimatedCard>

        <!-- Metric Cards Section -->
        <AnimatedCard animation="slideUp" trigger="onScroll" delay="100">
          <Card title="Metric Cards" subtitle="Display key metrics with icons and variants">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              <MetricCard
                title="Total Users"
                :value="1250"
                subtitle="Registered users"
                icon="fas fa-users"
                variant="default"
              />
              <MetricCard
                title="Active Orders"
                :value="45"
                subtitle="In progress"
                icon="fas fa-shopping-cart"
                variant="info"
              />
              <MetricCard
                title="Revenue"
                value="$24,500"
                subtitle="This month"
                icon="fas fa-dollar-sign"
                variant="success"
              />
              <MetricCard
                title="Issues"
                :value="3"
                subtitle="Needs attention"
                icon="fas fa-exclamation-triangle"
                variant="warning"
              />
            </div>
          </Card>
        </AnimatedCard>

        <!-- Buttons Section -->
        <AnimatedCard animation="slideUp" trigger="onScroll" delay="200">
          <Card title="Primary Buttons" subtitle="Various button variants and sizes">
            <div class="space-y-6">
              <div>
                <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Variants</h4>
                <div class="flex flex-wrap gap-3">
                  <PrimaryButton variant="primary" icon="fas fa-star">Primary</PrimaryButton>
                  <PrimaryButton variant="secondary" icon="fas fa-tag">Secondary</PrimaryButton>
                  <PrimaryButton variant="success" icon="fas fa-check">Success</PrimaryButton>
                  <PrimaryButton variant="warning" icon="fas fa-exclamation">Warning</PrimaryButton>
                  <PrimaryButton variant="danger" icon="fas fa-times">Danger</PrimaryButton>
                  <PrimaryButton variant="info" icon="fas fa-info">Info</PrimaryButton>
                </div>
              </div>

              <div>
                <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Sizes</h4>
                <div class="flex flex-wrap items-center gap-3">
                  <PrimaryButton variant="primary" size="xs">Extra Small</PrimaryButton>
                  <PrimaryButton variant="primary" size="sm">Small</PrimaryButton>
                  <PrimaryButton variant="primary" size="md">Medium</PrimaryButton>
                  <PrimaryButton variant="primary" size="lg">Large</PrimaryButton>
                  <PrimaryButton variant="primary" size="xl">Extra Large</PrimaryButton>
                </div>
              </div>

              <div>
                <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">States</h4>
                <div class="flex flex-wrap gap-3">
                  <PrimaryButton variant="primary" :loading="isLoading" @click="toggleLoading">
                    {{ isLoading ? 'Loading...' : 'Click to Load' }}
                  </PrimaryButton>
                  <PrimaryButton variant="primary" disabled>Disabled</PrimaryButton>
                  <PrimaryButton variant="primary" full-width>Full Width</PrimaryButton>
                </div>
              </div>
            </div>
          </Card>
        </AnimatedCard>

        <!-- Status Badges Section -->
        <AnimatedCard animation="slideUp" trigger="onScroll" delay="300">
          <Card title="Status Badges" subtitle="Display status with automatic color coding">
            <div class="space-y-4">
              <div>
                <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Auto-detected Status</h4>
                <div class="flex flex-wrap gap-2">
                  <StatusBadge status="Active" variant="auto" />
                  <StatusBadge status="Pending" variant="auto" />
                  <StatusBadge status="Completed" variant="auto" />
                  <StatusBadge status="Cancelled" variant="auto" />
                  <StatusBadge status="Error" variant="auto" />
                  <StatusBadge status="Debug" variant="auto" />
                </div>
              </div>

              <div>
                <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Manual Variants</h4>
                <div class="flex flex-wrap gap-2">
                  <StatusBadge status="Success" variant="success" />
                  <StatusBadge status="Warning" variant="warning" />
                  <StatusBadge status="Danger" variant="danger" />
                  <StatusBadge status="Info" variant="info" />
                  <StatusBadge status="Primary" variant="primary" />
                  <StatusBadge status="Secondary" variant="secondary" />
                </div>
              </div>
            </div>
          </Card>
        </AnimatedCard>

        <!-- Alerts Section -->
        <AnimatedCard animation="slideUp" trigger="onScroll" delay="400">
          <Card title="Alerts" subtitle="Display important messages to users">
            <div class="space-y-4">
              <Alert
                type="info"
                message="This is an informational alert with some important details."
                :closable="true"
                @close="() => {}"
              />
              <Alert
                type="success"
                message="Operation completed successfully! Your changes have been saved."
                :closable="true"
                @close="() => {}"
              />
              <Alert
                type="warning"
                message="Please review your input before proceeding. Some fields may need attention."
                :closable="true"
                @close="() => {}"
              />
              <Alert
                type="danger"
                message="An error occurred while processing your request. Please try again."
                :closable="true"
                @close="() => {}"
              />
            </div>
          </Card>
        </AnimatedCard>

        <!-- Loading Spinners Section -->
        <AnimatedCard animation="slideUp" trigger="onScroll" delay="500">
          <Card title="Loading Spinners" subtitle="Indicate loading states with various sizes and colors">
            <div class="space-y-6">
              <div>
                <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Sizes</h4>
                <div class="flex items-center space-x-6">
                  <div class="text-center">
                    <LoadingSpinner size="sm" />
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Small</p>
                  </div>
                  <div class="text-center">
                    <LoadingSpinner size="md" />
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Medium</p>
                  </div>
                  <div class="text-center">
                    <LoadingSpinner size="lg" />
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Large</p>
                  </div>
                  <div class="text-center">
                    <LoadingSpinner size="xl" />
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Extra Large</p>
                  </div>
                </div>
              </div>

              <div>
                <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Colors</h4>
                <div class="flex items-center space-x-6">
                  <LoadingSpinner color="primary" />
                  <LoadingSpinner color="success" />
                  <LoadingSpinner color="warning" />
                  <LoadingSpinner color="danger" />
                  <LoadingSpinner color="info" />
                  <LoadingSpinner color="gray" />
                </div>
              </div>
            </div>
          </Card>
        </AnimatedCard>

        <!-- Input Fields Section -->
        <AnimatedCard animation="slideUp" trigger="onScroll" delay="600">
          <Card title="Input Fields" subtitle="Form inputs with validation and features">
            <div class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <Input
                  v-model="inputValue"
                  label="Text Input"
                  placeholder="Enter some text..."
                  icon="fas fa-user"
                />
                <Input
                  type="email"
                  label="Email Input"
                  placeholder="Enter your email..."
                  icon="fas fa-envelope"
                />
                <Input
                  type="password"
                  label="Password Input"
                  placeholder="Enter your password..."
                  :show-password-toggle="true"
                />
                <Input
                  label="Input with Error"
                  placeholder="This field has an error..."
                  error="This field is required"
                />
              </div>
            </div>
          </Card>
        </AnimatedCard>

        <!-- Data Table Section -->
        <AnimatedCard animation="slideUp" trigger="onScroll" delay="700">
          <Card title="Data Table" subtitle="Advanced table with search, sorting, and pagination">
            <DataTable
              :data="sampleData"
              :columns="columns"
              searchable
              sortable
              default-sort="name"
              default-sort-direction="asc"
            >
              <template #actions="{ item }">
                <div class="flex space-x-2">
                  <PrimaryButton variant="info" size="xs" icon="fas fa-edit">Edit</PrimaryButton>
                  <PrimaryButton variant="danger" size="xs" icon="fas fa-trash">Delete</PrimaryButton>
                </div>
              </template>
            </DataTable>
          </Card>
        </AnimatedCard>

        <!-- Animation Examples -->
        <AnimatedCard animation="slideUp" trigger="onScroll" delay="800">
          <Card title="Animation Examples" subtitle="Different animation types and triggers">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <AnimatedCard animation="fadeIn" trigger="onHover">
                <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                  <h4 class="font-semibold text-blue-900 dark:text-blue-100">Fade In on Hover</h4>
                  <p class="text-blue-700 dark:text-blue-300 text-sm">Hover to see animation</p>
                </div>
              </AnimatedCard>

              <AnimatedCard animation="slideUp" trigger="onScroll">
                <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                  <h4 class="font-semibold text-green-900 dark:text-green-100">Slide Up on Scroll</h4>
                  <p class="text-green-700 dark:text-green-300 text-sm">Scroll to see animation</p>
                </div>
              </AnimatedCard>

              <AnimatedCard animation="scale" trigger="onMount" delay="500">
                <div class="p-4 bg-purple-50 dark:bg-purple-900 rounded-lg">
                  <h4 class="font-semibold text-purple-900 dark:text-purple-100">Scale on Mount</h4>
                  <p class="text-purple-700 dark:text-purple-300 text-sm">Animated on page load</p>
                </div>
              </AnimatedCard>
            </div>
          </Card>
        </AnimatedCard>

      </div>
    </div>
  </div>
</template>
