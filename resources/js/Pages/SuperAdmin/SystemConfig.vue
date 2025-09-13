<script setup>
import SuperAdminLayout from './Layout.vue';

defineProps({
    config: Object
});
</script>

<template>
    <SuperAdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-responsive-lg font-bold text-gray-800">System Configuration</h2>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Read-only configuration view
                </div>
            </div>

            <!-- Application Configuration -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-cog text-blue-600 mr-2"></i>
                        Application Settings
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Application Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ config.app_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Application URL</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ config.app_url }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Environment</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="{
                                          'bg-green-100 text-green-800': config.app_env === 'production',
                                          'bg-yellow-100 text-yellow-800': config.app_env === 'staging',
                                          'bg-red-100 text-red-800': config.app_env === 'local'
                                      }">
                                    {{ config.app_env }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Database Configuration -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-database text-green-600 mr-2"></i>
                        Database Configuration
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Database Connection</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ config.database_connection }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Mail Configuration -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-envelope text-purple-600 mr-2"></i>
                        Mail Configuration
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Mail Driver</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ config.mail_driver }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Cache & Session Configuration -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-memory text-orange-600 mr-2"></i>
                        Cache & Session Configuration
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cache Driver</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ config.cache_driver }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Session Driver</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ config.session_driver }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Queue Connection</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ config.queue_connection }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-server text-gray-600 mr-2"></i>
                        System Information
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ '8.3+' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Laravel Version</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ '12.x' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Server Time</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ new Date().toLocaleString() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Timezone</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ Intl.DateTimeFormat().resolvedOptions().timeZone }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Security Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-shield-alt text-red-600 mr-2"></i>
                        Security Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-green-800">HTTPS Enabled</h4>
                                    <p class="text-sm text-green-600">All communications are encrypted</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-green-800">CSRF Protection</h4>
                                    <p class="text-sm text-green-600">Cross-site request forgery protection enabled</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-green-800">Password Hashing</h4>
                                    <p class="text-sm text-green-600">All passwords are securely hashed</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-green-800">Account Lockout</h4>
                                    <p class="text-sm text-green-600">Automatic account lockout after failed attempts</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Environment Variables Notice -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Configuration Management</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>System configuration is managed through environment variables and Laravel configuration files.
                            To modify these settings, update the appropriate configuration files or environment variables
                            and restart the application.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SuperAdminLayout>
</template>
