<template>
    <div>
    <!-- Add Button -->
    <button v-if="!category"
            @click="showModal = true"
            class="px-4 py-2 bg-green-500 text-white rounded mb-2">
        Add New Category
    </button>

    <!-- Edit Button -->
    <button v-else
            @click="showModal = true"
            class="px-3 py-1 bg-blue-500 text-white rounded text-sm">
        Edit
    </button>

    <!-- Modal -->
    <Modal :show="showModal" @close="showModal = false">
        <form @submit.prevent="submitForm" class="p-6">
            <h2 class="text-lg font-medium mb-4">
                {{ category ? 'Edit Category' : 'Create New Category' }}
            </h2>

            <div class="mb-4">
                <InputLabel for="name" value="Category Name" />
                <TextInput id="name" v-model="form.name" class="w-full" />
            </div>

            <div class="flex justify-end space-x-2">
                <SecondaryButton @click="showModal = false">Cancel</SecondaryButton>
                <PrimaryButton type="submit">Save</PrimaryButton>
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
