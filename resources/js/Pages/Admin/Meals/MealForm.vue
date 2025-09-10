<template>
    <!-- Modal -->
    <Modal :show="show" @close="emit('close')">
        <form @submit.prevent="submitForm" class="p-8 max-w-6xl mx-auto bg-white rounded-lg shadow-xl">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800 border-b pb-4">
                {{ meal ? "Edit Meal" : "Create New Meal" }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <InputLabel for="name" value="Meal Name" class="text-gray-700 font-medium mb-2" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                        placeholder="Enter meal name"
                    />
                </div>

                <div>
                    <InputLabel for="price" value="Price (Tsh)" class="text-gray-700 font-medium mb-2" />
                    <TextInput
                        id="price"
                        type="number"
                        step="0.01"
                        v-model="form.price"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                        placeholder="0.00"
                    />
                </div>

                <div>
                    <InputLabel for="category" value="Category" class="text-gray-700 font-medium mb-2" />
                    <select
                        v-model="form.category_id"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
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

                <div>
                    <InputLabel for="image" value="Meal Image" class="text-gray-700 font-medium mb-2" />
                    <input
                        type="file"
                        accept="image/*"
                        @change="handleImageUpload"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 file:cursor-pointer border border-gray-300 rounded-md"
                    />
                    <div v-if="previewImage" class="mt-3">
                        <img
                            :src="previewImage"
                            class="w-32 h-32 object-cover rounded-md border shadow-sm"
                            alt="Meal preview"
                        />
                    </div>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <InputLabel for="description" value="Description" class="text-gray-700 font-medium mb-2" />
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                        placeholder="Describe your meal..."
                    ></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <SecondaryButton
                    @click.prevent="emit('close')"
                    class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors"
                >
                    Cancel
                </SecondaryButton>
                <PrimaryButton
                    type="submit"
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors"
                >
                    Save Meal
                </PrimaryButton>
            </div>
        </form>
    </Modal>
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
    show: Boolean,
});

const emit = defineEmits(['refresh', 'close']);

const previewImage = ref(null);

const form = useForm({
    name: props.meal?.name || "",
    price: props.meal?.price?.toString() || "0",
    category_id: props.meal?.category_id || "",
    description: props.meal?.description || "",
    image: null,
});

watch(
    () => props.meal,
    (newMeal) => {
        form.name = newMeal?.name || "";
        form.price = newMeal?.price?.toString() || "0";
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
                        emit("close");
                        router.reload({ replace: true });
                        form.reset();
                    },
                    onError: (errors) => {
                        console.error("Error updating meal:", errors);
                    }
                });
        } else {
            // Creating a new meal (no _method override)
            await form.post(route("meals.store"), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    emit("close");
                    router.reload({ replace: true });
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
