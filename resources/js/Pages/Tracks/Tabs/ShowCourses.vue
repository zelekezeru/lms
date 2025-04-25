<script setup>
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    BookOpenIcon,
} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import { Listbox } from "primevue";
import InputError from "@/Components/InputError.vue";

// Define the props for the track
const props = defineProps({
    track: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="">
        <div class="overflow-x-auto">
            <div
                class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Courses
                    </h2>
                </div>
                <!-- track courses list -->
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    No.
                                </th>
                                <th
                                    class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Name
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Credit Hours
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(course, index) in track.courses"
                                :key="course.id"
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                                class="border-b border-gray-300 dark:border-gray-600"
                            >
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ index + 1 }}
                                </td>

                                <td
                                    class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <Link
                                        :href="
                                            route('courses.show', {
                                                course: course.id,
                                            })
                                        "
                                    >
                                        {{ course.name }}
                                    </Link>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ course.creditHours }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
