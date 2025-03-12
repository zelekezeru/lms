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

// Define props for the program and units (though units may not be needed for programs)
defineProps({
    program: {
        type: Object,
        required: true,
    },
    departments: {
        type: Object,
        required: true,
    },
});

console.log(usePage().props.program.name);

const form = useForm({
    name: usePage().props.program.name,
    language: usePage().props.program.language,
    description: usePage().props.program.description,
    department_id: usePage().props.program.department.id,
});

// Submit form function
const submit = (id) => {
    form.patch(route("programs.update", { program: id }), {
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

                    <!-- Department Selection -->
                    <div>
                        <InputLabel
                            for="department"
                            value="Change Department"
                            class="block mb-1 text-gray-700 dark:text-gray-200"
                        />
                        <select
                            id="department"
                            v-model="form.department_id"
                            required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
                        >
                            <option disabled value="">Select Department</option>
                            <option
                                v-for="department in departments"
                                :key="department.id"
                                :value="department.id"
                                class="text-gray-900 dark:text-gray-200"
                                :selected="
                                    department.id == program.department.id
                                "
                            >
                                {{ department.name }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.department_id"
                            class="mt-2 text-sm text-red-600 dark:text-red-400"
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
