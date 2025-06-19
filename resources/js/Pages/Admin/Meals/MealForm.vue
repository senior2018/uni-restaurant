<template>
    <div>
        <!-- Trigger Buttons -->
        <button
            v-if="!meal"
            @click="showModal = true"
            class="px-4 py-2 bg-green-500 text-white rounded"
        >
            Add New Meal
        </button>

        <button
            v-else
            @click="showModal = true"
            class="px-3 py-1 bg-blue-500 text-white rounded text-sm"
        >
            Edit
        </button>

        <!-- Modal -->
        <Modal :show="showModal" @close="showModal = false">
            <form @submit.prevent="submitForm" class="p-6 space-y-4">
                <h2 class="text-xl font-semibold mb-4">
                    {{ meal ? "Edit Meal" : "Create New Meal" }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="name" value="Meal Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <InputLabel for="price" value="Price ($)" />
                        <TextInput
                            id="price"
                            type="number"
                            step="0.01"
                            v-model="form.price"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <InputLabel for="category" value="Category" />
                        <select
                            v-model="form.category_id"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                        >
                            <option value="">Select Category</option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="description" value="Description" />
                        <textarea
                            v-model="form.description"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                        ></textarea>
                    </div>

                    <div>
                        <InputLabel for="image" value="Meal Image" />
                        <input
                            type="file"
                            accept="image/*"
                            @change="handleImageUpload"
                            class="block w-full"
                        />
                        <div v-if="previewImage" class="mt-2">
                            <img
                                :src="previewImage"
                                class="w-32 h-32 object-cover rounded border"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <SecondaryButton @click.prevent="showModal = false"
                        >Cancel</SecondaryButton
                    >
                    <PrimaryButton type="submit">Save Meal</PrimaryButton>
                </div>
            </form>
        </Modal>
    </div>
</template>
<script setup>
import { ref, watch } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    meal: Object,
    categories: Array,
});

const emit = defineEmits(['refresh']);

const showModal = ref(false);
const previewImage = ref(null);

const form = useForm({
    name: props.meal?.name || "",
    price: props.meal?.price || 0,
    category_id: props.meal?.category_id || "",
    description: props.meal?.description || "",
    image: null,
});

watch(
    () => props.meal,
    (newMeal) => {
        form.name = newMeal?.name || "";
        form.price = newMeal?.price || 0;
        form.category_id = newMeal?.category_id || "";
        form.description = newMeal?.description || "";
        form.image = null;
        previewImage.value = newMeal?.image_url || null;
    },
    { immediate: true }
);

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        previewImage.value = URL.createObjectURL(file);
    }
};

const submitForm = async () => {
    try {
        if (props.meal) {
            // Updating an existing meal
            await form
                .transform(data => ({
                    ...data,
                    _method: 'PUT',
                }))
                .post(route("meals.update", props.meal.id), {
                    forceFormData: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        emit("refresh");
                        showModal.value = false;
                        form.reset();
                    },
                    onError: (errors) => {
                        console.error("Error updating meal:", errors);
                    }
                });
        } else {
            // Creating a new meal
            await form.post(route("meals.store"), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    emit("refresh");
                    showModal.value = false;
                    form.reset();
                },
                onError: (errors) => {
                    console.error("Error creating meal:", errors);
                }
            });
        }
    } catch (error) {
        console.error("Unexpected error:", error);
    }
};
</script>
