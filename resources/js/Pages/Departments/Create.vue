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
    programs: {
        type: Object,
    },
    users: {
        type: Object,
    },
});

// Initialize the form with the department fields
const form = useForm({
    name: "",
    code: "",
    description: "",
    duration: "",
    user_id: "",
    program_id: "",
});

const submit = () => {
    form.post(route("departments.store")); // Assuming this is the correct route for storing the department
};
</script>

<template>
    <AppLayout>
        <div class="flex justify-center">
            <div
                class="bg-gray-200 dark:bg-gray-800 shadow-lg rounded-md p-6 max-w-lg w-full"
            >
                <h2 class="text-xl font-semibold mb-4 dark:text-gray-200">
                    Department Creation Form
                </h2>
                <form @submit.prevent="submit">


                    <!-- Program Director Dropdown -->
                    <div class="mb-4">
                    <InputLabel
                        for="program_id"
                        value="Select Program Director"
                        class="block mb-1 text-gray-800 dark:text-gray-200"
                    />
                    <select
                        id="program_id"
                        v-model="form.program_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                    >
                        <option value="">Select Program</option>
                        <option
                        v-for="program in programs"
                        :key="program.id"
                        :value="program.id"
                        >
                        {{ program.name }} in {{ program.language }}
                        </option>
                    </select>
                    <InputError
                        :message="form.errors.program_id"
                        class="mt-2 text-sm text-red-500"
                    />
                    </div>

                    <!-- Department Name Field -->
                    <div class="mb-4">
                        <InputLabel
                            for="name"
                            value="Department Name"
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

                    <!-- user Director Dropdown -->
                    <div class="mb-4">
                    <InputLabel
                        for="user_id"
                        value="Select Department Head"
                        class="block mb-1 text-gray-800 dark:text-gray-200"
                    />
                    <select
                        id="user_id"
                        v-model="form.user_id"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                    >
                        <option value="">Select Head</option>
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

                    <!-- duration Field -->
                    <div class="mb-4">
                        <InputLabel
                            for="duration"
                            value="duration"
                            class="block mb-1 dark:text-gray-200"
                        />
                        <TextInput
                            id="duration"
                            type="number"
                            v-model="form.duration"
                            autocomplete="duration"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                        />
                        <InputError
                            :message="form.errors.duration"
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
