<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

// Define the props for this component
defineProps({
    departments: {
        type: Object,
    },
});

const selectedModes = ref([]);

// Initialize the form with the program fields
const form = useForm({
    name: "",
    description: "",
    language: "",
    department_id: "",
    studyModes: [],
});

watch(selectedModes, (newVal, oldValue) => {
    // Add new modes if not already in form.studyModes
    newVal.forEach((mode) => {
        if (!form.studyModes.find((studyMode) => studyMode.mode === mode)) {
            form.studyModes.push({ mode: mode, duration: null, fees: null });
        }
    });

    // Remove study modes that are no longer selected
    form.studyModes = form.studyModes.filter((studyMode) =>
        newVal.includes(studyMode.mode)
    );
});

const submit = () => {
    form.post(route("programs.store")); // Assuming this is the correct route for storing the program
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <!-- Centered and Enhanced Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Program Creation Form
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to create a new program.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-gray-200 dark:bg-gray-800 shadow-lg rounded-md p-6">
                <form @submit.prevent="submit">
                    <!-- Two-column grid layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column: Program Fields -->
                        <section class="space-y-6">
                            <div>
                                <InputLabel
                                    for="name"
                                    value="Program Name"
                                    class="block mb-1 dark:text-gray-200"
                                />
                                <TextInput
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                                />
                                <InputError
                                    :message="form.errors.name"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="language"
                                    value="Program Language"
                                    class="block mb-1 dark:text-gray-200"
                                />
                                <TextInput
                                    id="language"
                                    type="text"
                                    v-model="form.language"
                                    required
                                    autocomplete="language"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                                />
                                <InputError
                                    :message="form.errors.language"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400"
                                />
                            </div>

                            <div>
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
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                                />
                                <InputError
                                    :message="form.errors.description"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="department_id"
                                    value="Select Department"
                                    class="block mb-1 dark:text-gray-200"
                                />
                                <select
                                    id="department_id"
                                    v-model="form.department_id"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                                >
                                    <option disabled value="">
                                        Select Department
                                    </option>
                                    <option
                                        v-for="department in departments"
                                        :key="department.id"
                                        :value="department.id"
                                        class="dark:text-gray-200"
                                    >
                                        {{ department.name }}
                                    </option>
                                </select>
                                <InputError
                                    :message="form.errors.department_id"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400"
                                />
                            </div>

                            <!-- Study Modes Selection using Checkboxes -->
                            <div>
                                <InputLabel
                                    for="studyModes"
                                    value="Select Study Modes (Check all that apply)"
                                    class="block mb-1 dark:text-gray-200"
                                />
                                <div class="flex flex-col space-y-2">
                                    <label class="inline-flex items-center dark:text-gray-200 transition-colors duration-200 hover:text-indigo-500">
                                        <input
                                            type="checkbox"
                                            value="REGULAR"
                                            v-model="selectedModes"
                                            class="form-checkbox h-4 w-4 text-blue-600 dark:bg-gray-700 dark:border-gray-600"
                                        />
                                        <span class="ml-2">REGULAR</span>
                                    </label>
                                    <label class="inline-flex items-center dark:text-gray-200 transition-colors duration-200 hover:text-indigo-500">
                                        <input
                                            type="checkbox"
                                            value="EXTENSION"
                                            v-model="selectedModes"
                                            class="form-checkbox h-4 w-4 text-blue-600 dark:bg-gray-700 dark:border-gray-600"
                                        />
                                        <span class="ml-2">EXTENSION</span>
                                    </label>
                                    <label class="inline-flex items-center dark:text-gray-200 transition-colors duration-200 hover:text-indigo-500">
                                        <input
                                            type="checkbox"
                                            value="DISTANCE"
                                            v-model="selectedModes"
                                            class="form-checkbox h-4 w-4 text-blue-600 dark:bg-gray-700 dark:border-gray-600"
                                        />
                                        <span class="ml-2">DISTANCE</span>
                                    </label>
                                </div>
                                <InputError
                                    :message="form.errors.studyModes"
                                    class="mt-2 text-sm text-red-600 dark:text-red-400"
                                />
                            </div>
                        </section>

                        <!-- Right Column: Study Mode Details (Scrollable) -->
                        <section class="space-y-6">
                            <!-- Wrap the study mode details in a scrollable container -->
                            <div class="max-h-[400px] overflow-y-auto">
                                <div
                                    v-if="selectedModes.length === 0"
                                    class="p-4 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 rounded-md border border-gray-200 dark:border-gray-700"
                                >
                                    You have not selected any study mode just yet.
                                </div>
                                <!-- Use Transition Group for smooth fade transitions -->
                                <transition-group name="fade" tag="div">
                                    <div
                                        v-for="(studyMode, index) in form.studyModes"
                                        :key="studyMode.mode"
                                        class="p-4 bg-white dark:bg-gray-900 rounded-md shadow border border-gray-200 dark:border-gray-700 mb-4 transition-all duration-300"
                                    >
                                        <h2 class="text-lg font-semibold mb-2 dark:text-gray-200">
                                            {{ studyMode.mode }} Study Mode Details
                                        </h2>
                                        <div class="mb-4">
                                            <InputLabel
                                                for="duration"
                                                value="Duration in Years"
                                                class="block mb-1 dark:text-gray-200"
                                            />
                                            <TextInput
                                                id="duration"
                                                type="number"
                                                v-model="studyMode.duration"
                                                required
                                                autocomplete="duration"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                                            />
                                            <InputError
                                                :message="form.errors.duration"
                                                class="mt-2 text-sm text-red-600 dark:text-red-400"
                                            />
                                        </div>
                                        <div>
                                            <InputLabel
                                                for="fees"
                                                value="Tuition Fee"
                                                class="block mb-1 dark:text-gray-200"
                                            />
                                            <TextInput
                                                id="fees"
                                                type="number"
                                                v-model="studyMode.fees"
                                                required
                                                autocomplete="fees"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
                                            />
                                            <InputError
                                                :message="form.errors.fees"
                                                class="mt-2 text-sm text-red-600 dark:text-red-400"
                                            />
                                        </div>
                                    </div>
                                </transition-group>
                            </div>
                        </section>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <PrimaryButton
                            :disabled="form.processing"
                            class="w-full dark:bg-gray-200 dark:text-gray-800"
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


<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.fade-enter-to, .fade-leave-from {
  opacity: 1;
}
</style>
