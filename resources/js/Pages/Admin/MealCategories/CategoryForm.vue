<template>
    <div>
        <!-- Add Button -->
        <button
            v-if="!category"
            @click="showModal = true"
            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md shadow-sm hover:shadow-md transition-all duration-200 mb-2"
        >
            Add New Category
        </button>

        <!-- Edit Button -->
        <button
            v-else
            @click="showModal = true"
            class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-md text-sm shadow-sm hover:shadow-md transition-all duration-200"
        >
            Edit
        </button>

        <!-- Modal -->
        <Modal :show="showModal" @close="showModal = false">
            <form @submit.prevent="submitForm" class="p-8 max-w-4xl mx-auto bg-white rounded-lg shadow-xl">
                <h2 class="text-2xl font-semibold mb-6 text-gray-800 border-b pb-4">
                    {{ category ? 'Edit Category' : 'Create New Category' }}
                </h2>

                <div class="mb-6">
                    <InputLabel for="name" value="Category Name" class="text-gray-700 font-medium mb-2" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                        placeholder="Enter category name"
                    />
                </div>

                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <SecondaryButton
                        @click="showModal = false"
                        class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors"
                    >
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton
                        type="submit"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors"
                    >
                        Save Category
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    category: Object
});

const emit = defineEmits(['refresh']);
const showModal = ref(false);

const form = useForm({
    name: props.category?.name || ''
});

const submitForm = async () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            setTimeout(() => {
                router.reload({
                    only: ['categories', 'flash'],
                    preserveScroll: true
                });
            }, 1000);
        },
        onError: (errors) => {
            console.log('Form errors:', errors);
        }
    };

    if (props.category) {
        form.put(route('meal-categories.update', props.category.id), options);
    } else {
        form.post(route('meal-categories.store'), options);
    }
};

watch(() => props.category, (newCategory) => {
    form.name = newCategory?.name || '';
}, { immediate: true });
</script>
