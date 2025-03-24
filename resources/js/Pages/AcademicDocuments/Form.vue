<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    document: { type: Object, default: null }, // For edit, document data is passed
});

// Initialize form data
const form = useForm({
    title: props.document?.title || "",
    description: props.document?.description || "",
    image: null, // Image upload
    _method: props.document ? "PATCH" : "POST", // Determines request type
});

// Ref for image preview
const imagePreview = ref(props.document?.image || null);

// Handle file selection
const handleFileChange = (e) => {
    const file = e.target.files[0];
    form.image = file;
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        imagePreview.value = props.document?.image || null;
    }
};

// Determine form action
const formAction = computed(() => {
    return props.document
        ? route("academic-documents.update", { document: props.document.id })
        : route("academic-documents.store");
});

// Submit the form
const submit = () => {
    form.post(formAction.value);
};
</script>

<template>
    <AppLayout
        :title="document ? 'Edit Academic Document' : 'Add Academic Document'"
    >
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{
                        document
                            ? "Edit Academic Document"
                            : "Add Academic Document"
                    }}
                </h2>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="mb-4">
                        <InputLabel for="title" value="Title" />
                        <TextInput
                            id="title"
                            type="text"
                            v-model="form.title"
                            required
                            class="w-full"
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <div class="mb-4">
                        <InputLabel for="description" value="Description" />
                        <textarea
                            v-model="form.description"
                            class="w-full p-2 border rounded-md"
                            rows="3"
                        ></textarea>
                        <InputError :message="form.errors.description" />
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-4">
                        <InputLabel for="image" value="Document Image" />
                        <div class="flex items-center gap-4">
                            <label
                                for="image"
                                class="cursor-pointer px-4 py-2 text-white bg-black hover:bg-blue-700 flex items-center gap-2 rounded-md shadow"
                            >
                                <PhotoIcon class="w-5 h-5" />
                                Upload Image
                            </label>
                            <input
                                id="image"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleFileChange"
                            />

                            <!-- Image Preview -->
                            <div
                                v-if="imagePreview"
                                class="w-16 h-16 rounded border shadow overflow-hidden"
                            >
                                <img
                                    :src="imagePreview"
                                    alt="Document Preview"
                                    class="object-cover w-full h-full"
                                />
                            </div>
                        </div>
                        <InputError :message="form.errors.image" />
                    </div>
                    <div class="mt-6 flex justify-center">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <span v-if="!form.processing">Submit</span>
                            <span v-else>Submitting...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
