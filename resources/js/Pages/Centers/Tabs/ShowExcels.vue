<script setup>
import { defineProps } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { EyeIcon } from "@heroicons/vue/24/outline";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

// Props from parent
const props = defineProps({
    center: {
        type: Object,
        required: true,
    },
    coordinator: {
        type: Object,
        required: true,
    },
});

// Form state using useForm (Inertia.js)
const form = useForm({
    center_id: props.center.id,
    file: null,
});

// Handle file selection
function handleFileUpload(e) {
    form.file = e.target.files[0];
}

// Submit the import request
function submit() {
    form.post(route("centerStudents.import"), {
        forceFormData: true,
    });
}
</script>

<template>
    <div class="overflow-x-auto">
        <!-- Make me two divs with two half columns -->

        <div class="flex items-center justify-center mb-12">
            <div class="relative inline-block">
                <span
                    class="absolute -top-4 -left-8 w-16 h-16 bg-gradient-to-tr from-blue-200 via-blue-400 to-blue-600 opacity-30 rounded-full blur-2xl"
                ></span>
                <span
                    class="absolute -bottom-4 -right-8 w-16 h-16 bg-gradient-to-br from-blue-200 via-blue-400 to-blue-600 opacity-30 rounded-full blur-2xl"
                ></span>
                <h2
                    class="text-3xl md:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 via-blue-500 to-blue-400 drop-shadow-lg text-center px-8 py-4 rounded-xl border-b-4 border-blue-400 shadow-xl"
                >
                    <span class="inline-block align-middle mr-3 animate-bounce">
                        <svg
                            class="w-8 h-8 text-blue-500"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16 17l-4 4m0 0l-4-4m4 4V3"
                            />
                        </svg>
                    </span>
                    Students Excel Import &amp; Export Data
                    <span
                        class="inline-block align-middle ml-3 animate-bounce animation-delay-200"
                    >
                        <svg
                            class="w-8 h-8 text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8 7l4-4m0 0l4 4m-4-4v18"
                            />
                        </svg>
                    </span>
                </h2>
            </div>
        </div>
        <!-- Section Details -->
        <div
            class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-6 pb-6 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg"
        >
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-6 text-gray-800 dark:text-gray-200"
            >
                <div
                    class="flex flex-col items-start bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-blue-100 dark:border-gray-700"
                >
                    <span
                        class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1"
                        >Section:</span
                    >
                    <span class="text-lg font-semibold">
                        {{ props.center.name }}</span
                    >
                </div>
                <div
                    class="flex flex-col items-start bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-blue-100 dark:border-gray-700"
                >
                    <span
                        class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1"
                        >Code</span
                    >
                    <span class="text-lg font-semibold">{{
                        props.center.code
                    }}</span>
                </div>
                <div
                    class="flex flex-col items-start bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-blue-100 dark:border-gray-700"
                >
                    <span
                        class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1"
                        >Coordinator</span
                    >
                    <span class="text-lg font-semibold"
                        >{{ props.coordinator.name }}
                    </span>
                </div>
            </div>
        </div>

        <div
            class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
        >
            <div
                class="flex flex-col md:flex-row items-stretch justify-between gap-8 mb-4 px-8"
            >
                <!-- Export Section -->
                <div
                    class="relative bg-gradient-to-br from-green-50 via-green-100 to-green-200 dark:from-green-900 dark:via-green-800 dark:to-green-700 rounded-3xl shadow-2xl border-2 border-green-200 dark:border-green-700 w-full md:w-1/2 px-8 py-8 flex flex-col items-center transition-transform hover:scale-105 duration-300"
                >
                    <div
                        class="absolute -top-6 left-1/2 -translate-x-1/2 bg-green-600 dark:bg-green-500 rounded-full p-3 shadow-lg"
                    >
                        <svg
                            class="w-8 h-8 text-white"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                    </div>
                    <span
                        class="text-2xl font-extrabold text-green-900 dark:text-green-100 mb-4 tracking-wide text-center"
                    >
                        Export Section Students
                    </span>
                    <p
                        class="text-green-800 dark:text-green-200 text-sm mb-6 text-center"
                    >
                        Download all students in this center as an Excel file.
                    </p>
                    <a
                        :href="route('centerStudents.export', props.center.id)"
                        class="bg-gradient-to-r from-green-500 to-green-700 hover:from-green-600 hover:to-green-800 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition duration-300 ease-in-out text-lg flex items-center gap-2"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4v12m0 0l-4-4m4 4l4-4"
                            />
                        </svg>
                        Export Students
                    </a>
                </div>

                <!-- Import Section -->
                <div
                    class="relative bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 dark:from-blue-900 dark:via-blue-800 dark:to-blue-700 rounded-3xl shadow-2xl border-2 border-blue-200 dark:border-blue-700 w-full md:w-1/2 px-8 py-8 flex flex-col items-center transition-transform hover:scale-105 duration-300"
                >
                    <div
                        class="absolute -top-6 left-1/2 -translate-x-1/2 bg-blue-600 dark:bg-blue-500 rounded-full p-3 shadow-lg"
                    >
                        <svg
                            class="w-8 h-8 text-white"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 20V8m0 0l-4 4m4-4l4 4"
                            />
                        </svg>
                    </div>
                    <span
                        class="text-2xl font-extrabold text-blue-900 dark:text-blue-100 mb-4 tracking-wide text-center"
                    >
                        Import Section Students
                    </span>
                    <p
                        class="text-blue-800 dark:text-blue-200 text-sm mb-6 text-center"
                    >
                        Upload an Excel file to add students to this center.
                    </p>
                    <form
                        @submit.prevent="submit"
                        enctype="multipart/form-data"
                        class="flex flex-col items-center gap-4 w-full"
                    >
                        <input
                            type="hidden"
                            name="center_id"
                            :value="form.center_id"
                        />
                        <div class="w-full">
                            <label
                                for="file"
                                class="block text-sm font-semibold text-blue-700 dark:text-blue-300 mb-2"
                            >
                                Choose Excel File
                            </label>
                            <input
                                id="file"
                                type="file"
                                name="file"
                                @change="handleFileUpload"
                                required
                                class="block w-full text-sm text-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition duration-200"
                                accept=".xlsx,.xls,.csv"
                            />
                            <p
                                v-if="form.errors.file"
                                class="text-sm text-red-500 mt-1"
                            >
                                {{ form.errors.file }}
                            </p>
                        </div>
                        <button
                            type="submit"
                            class="bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition duration-300 ease-in-out text-lg flex items-center gap-2"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 4v12m0 0l-4-4m4 4l4-4"
                                />
                            </svg>
                            Import Students
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
