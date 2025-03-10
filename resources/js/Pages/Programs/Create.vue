<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { router, useForm } from "@inertiajs/vue3";

// Define the props for this component
defineProps({
    units: {
        type: Object,
    },
});

// Initialize the form with the program fields
const form = useForm({
    name: "",
    description: "",
    language: "",
    study: "",
});

const submit = () => {
    form.post(route("programs.store")); // Assuming this is the correct route for storing the program
};
</script>

<template>
    <AppLayout>
        <div class="flex justify-center">
            <div
                class="bg-gray-200 dark:bg-gray-800 shadow-lg rounded-md p-6 max-w-lg w-full"
            >
                <h2 class="text-xl font-semibold mb-4 dark:text-gray-200">
                    program Creation Form
                </h2>
                <form @submit.prevent="submit">
                    <!-- program Name Field -->
                    <div class="mb-4">
                        <InputLabel
                            for="name"
                            value="program Name"
                            class="block mb-1 dark:text-gray-200"
                        />
                        <TextInput
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                        />
                        <InputError
                            :message="form.errors.name"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                        />
                    </div>

                    <!-- program Code Field -->
                    <div class="mb-4">
                        <InputLabel
                            for="language"
                            value="program Language"
                            class="block mb-1 dark:text-gray-200"
                        />
                        <TextInput
                            id="language"
                            type="text"
                            v-model="form.language"
                            required
                            autocomplete="language"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                        />
                        <InputError
                            :message="form.errors.language"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                        />
                    </div>

                    <!-- Description Field -->
                    <div class="mb-4">
                        <InputLabel
                            for="description"
                            value="Description"
                            class="block mb-1 dark:text-gray-200"
                        />
                        <TextInput
                            id="description"
                            type="text"
                            v-model="form.description"
                            autocomplete="description"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                        />
                        <InputError
                            :message="form.errors.description"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                        />
                    </div>
                    <!-- Phone Field -->
                    <div class="mb-4">
                        <InputLabel
                            for="study"
                            value="Study"
                            class="block mb-1 dark:text-gray-200"
                        />
                        <TextInput
                            id="study"
                            type="text"
                            v-model="form.study"
                            autocomplete="study"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                        />
                        <InputError
                            :message="form.errors.study"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                        />
                    </div>
                    
                    <!-- Submit Button -->
                    <PrimaryButton
                        v-if="!form.processing"
                        class="w-full dark:bg-gray-200 dark:text-gray-800"
                        >Submit</PrimaryButton
                    >
                </form>
            </div>
        </div>
    </AppLayout>
</template>
