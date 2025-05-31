<script setup>
import { defineProps } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { EyeIcon } from "@heroicons/vue/24/outline";

// Props from parent
const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    courses: {
        type: Object,
        required: false,
    },
    instructors: {
        type: Object,
        required: false,
    },
});

// Form state using useForm (Inertia.js)
const form = useForm({
    section_id: props.section.id,
    file: null,
});

// Handle file selection
function handleFileUpload(e) {
    form.file = e.target.files[0];
}

// Submit the import request
function submit() {
    form.post(route("sectionStudents.import"), {
        forceFormData: true,
        onSuccess: () => {
            alert("Import successful!");
            form.reset("file");
        },
        onError: () => {
            alert("Import failed. Please check your file and try again.");
        },
    });
}
</script>

<template>
    <div class="overflow-x-auto">
        <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Students
                </h2>
            </div>

            <!-- Export & Import Section -->
            <div class="flex flex-col md:flex-row md:items-center mb-4 md:space-x-12 space-y-4 md:space-y-0">
                <!-- Export Button -->
                <a
                    :href="route('sectionStudents.export', props.section.id)"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded block text-center"
                >
                    Export Students
                </a>

                <!-- Import Form -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col md:flex-row items-stretch md:items-center space-y-3 md:space-y-0 md:space-x-4 border border-gray-200 dark:border-gray-700 w-full md:w-auto">
                    <form @submit.prevent="submit" enctype="multipart/form-data" class="flex flex-col space-y-3 md:space-y-0 md:flex-row md:items-center md:space-x-4 w-full">
                        <!-- Hidden Section ID -->
                        <input type="hidden" name="section_id" :value="form.section_id" />

                        <!-- File Upload -->
                        <div class="flex-1">
                            <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Choose Excel File</label>
                            <input
                                id="file"
                                type="file"
                                name="file"
                                @change="handleFileUpload"
                                required
                                class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 dark:bg-gray-800 dark:text-white"
                                accept=".xlsx,.xls,.csv"
                            />
                            <p v-if="form.errors.file" class="text-sm text-red-500 mt-1">{{ form.errors.file }}</p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap"
                        >
                            Import Students
                        </button>
                    </form>
                </div>
            </div>

            <!-- Students Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border border-gray-300 dark:border-gray-600">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">No.</th>
                            <th class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">Name</th>
                            <th class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">ID Number</th>
                            <th class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">Track</th>
                            <th class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(student, index) in [...props.section.students].sort((a, b) => a.firstName.localeCompare(b.firstName))"
                            :key="student.id"
                            :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'"
                            class="border-b border-gray-300 dark:border-gray-600"
                        >
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">{{ index + 1 }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">
                                <Link :href="route('students.show', { student: student.id })">
                                    {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
                                </Link>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">
                                {{ student.idNo }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">
                                {{ props.section.track?.name }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 text-center">
                                <Link :href="route('students.show', { student: student.id })" class="text-blue-500 hover:text-blue-700">
                                    <EyeIcon class="w-5 h-5 inline" />
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
