<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
    program: { type: Object, required: true },
    users: { type: Object, required: true },
});

// Initialize form data
const form = useForm({
    name: props.program.name || "",
    language: props.program.language || "",
    description: props.program.description || "",
    address: props.program.address || "",
    user_id: props.program.user_id || "",
    _method: "PUT", // Change to PUT
});

// Submit form function
const submit = (id) => {
    form.post(route("programs.update", { program: id }), {
        onSuccess: () => {
            Swal.fire(
                "Updated",
                "The program has been updated successfully",
                "success"
            );
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="flex justify-center min-h-screen items-center">
            <div
                class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 max-w-lg w-full"
            >
                <h2
                    class="text-3xl font-extrabold text-gray-800 dark:text-gray-800 mb-6 text-center tracking-wide"
                >
                    Edit Program
                </h2>

                <!-- Form -->
                <form @submit.prevent="submit(program.id)" class="space-y-6">
                    <!-- Program Name -->
                    <div>
                        <InputLabel
                            for="name"
                            value="Program Name"
                            class="block mb-1 text-gray-700 dark:text-gray-200"
                        />
                        <TextInput
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                        />
                        <InputError
                            :message="form.errors.name"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                        />
                    </div>

                    <!-- Program Language -->
                    <div>
                        <InputLabel
                            for="language"
                            value="Program Language"
                            class="block mb-1 text-gray-700 dark:text-gray-200"
                        />
                        <TextInput
                            id="language"
                            type="text"
                            v-model="form.language"
                            required
                            autocomplete="language"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                        />
                        <InputError
                            :message="form.errors.language"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                        />
                    </div>

                    <!-- Description -->
                    <div>
                        <InputLabel
                            for="description"
                            value="Description"
                            class="block mb-1 text-gray-700 dark:text-gray-200"
                        />
                        <TextInput
                            id="description"
                            type="text"
                            v-model="form.description"
                            autocomplete="description"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                        />
                        <InputError
                            :message="form.errors.description"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
                        />
                    </div>

                    <div>
                        <InputLabel
                            for="user_id"
                            value="Select Program Director"
                            class="block mb-1 text-gray-800 dark:text-gray-200"
                        />
                        <select
                            id="user_id"
                            v-model="form.user_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                        >
                            <option disabled value="">
                                Select Program Director
                            </option>
                            <option
                                v-for="user in users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.user_id"
                            class="mt-2 text-sm text-red-500"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <PrimaryButton
                            :disabled="form.processing"
                            class="w-56 px-10 py-4 text-xl font-bold text-center bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition rounded-lg flex items-center justify-center"
                        >
                            <span v-if="!form.processing">Submit</span>
                            <span v-else>Submitting...</span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
