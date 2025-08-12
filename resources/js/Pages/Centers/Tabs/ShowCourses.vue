<script setup>
import { defineProps, ref, computed } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    EyeIcon,
    PlusCircleIcon
} from "@heroicons/vue/24/outline";
import Modal from "@/Components/Modal.vue";
import { Listbox } from "primevue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    center: {
        type: Object,
        required: true,
    },
    allCourses: {
        type: Array,
        required: true,
    },
    courses: {
        type: Array,
        required: true,
    },

});

const sortedCourses = computed(() => {
    return Array.isArray(props.courses)
        ? [...props.courses].sort((a, b) => (a.name || '').localeCompare(b.name || ''))
        : [];
});

const assignCourses = ref(false);
const courseAssignmentForm = useForm({
    courses: Array.isArray(props.courses) ? props.courses.map(course => course.id) : [],
});

const closeCourseAssignment = () => {
    assignCourses.value = false;
    courseAssignmentForm.reset();
    courseAssignmentForm.clearErrors();
};

const submitCourseAssignment = () => {
    courseAssignmentForm.post(
        route('center-courses.assign', { center: props.center.id }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Courses Assigned",
                    "Courses have been assigned to this center.",
                    'success'
                );
                assignCourses.value = false;
                courseAssignmentForm.reset();
                courseAssignmentForm.courses = props.courses.map(course => course.id);
            },
        }
    );
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Center Courses
            </h2>
            <button
                @click="assignCourses = !assignCourses"
                class="flex items-center text-indigo-600 hover:text-indigo-800"
            >
                <PlusCircleIcon class="w-8 h-8" />
                <span class="hidden sm:inline">Assign Courses</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Center Courses
                    </h2>
                </div>
                
                <!-- Center courses list -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border border-gray-300 dark:border-gray-600">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                                    No.
                                </th>
                                <th class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                                    Name
                                </th>
                                <th class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                                    Number of Students
                                </th>
                                <th class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Credit Hours
                                </th>
                                <th class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(course, index) in courses"
                                :key="course.id"
                                :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'"
                                class="border-b border-gray-300 dark:border-gray-600"
                            >
                                <td class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                    {{ index + 1 }}
                                </td>
                                <td class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                    <Link :href="route('courses.show', { course: course.id })">
                                        {{ course.name }}
                                    </Link>
                                </td>
                                <td class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                    {{ course.grade_count }}
                                </td>
                                <td class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                    {{ course.credit_hours }}
                                </td>
                                <td class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                    <div v-if="userCan('view-courses')">
                                        <Link :href="route('courses.show', { course: course.id })" class="text-blue-500 hover:text-blue-700">
                                            <EyeIcon class="w-5 h-5" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal
            :show="assignCourses"
            @close="assignCourses = !assignCourses"
            :maxWidth="'6xl'"
            class="p-24 h-full"
        >
            <div class="w-full px-16 py-8">
                <h1 class="text-lg mb-5">
                    Pick Courses for Center
                </h1>
                <!-- Select All Checkbox -->
                <div class="mb-3 flex items-center">
                    <input
                        type="checkbox"
                        id="selectAllCourses"
                        :checked="allCourses.length > 0 && courseAssignmentForm.courses.length === allCourses.length"
                        :indeterminate.prop="courseAssignmentForm.courses.length > 0 && courseAssignmentForm.courses.length < allCourses.length"
                        @change="e => {
                            if (e.target.checked) {
                                courseAssignmentForm.courses = allCourses.map(c => c.id)
                            } else {
                                courseAssignmentForm.courses = []
                            }
                        }"
                        class="w-5 h-5 text-indigo-600 border-gray-300 rounded"
                    />
                    <label for="selectAllCourses" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Select All Courses
                    </label>
                </div>
                <Listbox
                    id="coursesList"
                    v-model="courseAssignmentForm.courses"
                    :options="allCourses"
                    optionLabel="name"
                    option-value="id"
                    appendTo="self"
                    filter
                    checkmark
                    multiple
                    list-style="max-height: 500px"
                    :placeholder="'Select Courses'"
                    :maxSelectedLabels="3"
                    class="w-full"
                />

                <InputError
                    :message="courseAssignmentForm.errors.courses"
                    class="mt-2 text-sm text-red-500"
                />
                <div class="flex justify-end mt-4">
                    <button
                        @click="submitCourseAssignment"
                        :disabled="courseAssignmentForm.processing"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                    >
                        {{ courseAssignmentForm.processing ? 'Assigning...' : 'Assign' }}
                    </button>

                    <button
                        @click="closeCourseAssignment"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                    >
                        Close
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>
