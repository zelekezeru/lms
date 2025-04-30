<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, MultiSelect } from "primevue";
import { defineProps, ref } from "vue";
import {  useForm } from "@inertiajs/vue3";
import {
    CogIcon,
    PencilSquareIcon, XMarkIcon, PlusCircleIcon} from "@heroicons/vue/24/solid";
const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    courses: {
        type: Array,
        required: true,
    },
});

const assignCourses = ref(false);
const courseAssignmentForm = useForm({
    courses: props.student.courses.map(course => course.id),
});

const closeCourseAssignment = () => {
    assignCourses.value = false;
    courseAssignmentForm.reset();
    courseAssignmentForm.clearErrors();
};

const submitCourseAssignment = () => {
    courseAssignmentForm.post(
        route('courses-student.assign', { student: props.student.id }),
        {
            onSuccess: () => {
                Swal.fire(
                    'Successful!',
                    'Courses assigned successfully.',
                    'success'
                );
                assignCourses.value = false;
                courseAssignmentForm.reset();
                courseAssignmentForm.courses = props.section.courses.map(course => course.id);
            },
        }
    );
};

</script>


<template>
    <div class="">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Courses
            </h2>
            <button
                @click="assignCourses = !assignCourses"
                class="flex text-indigo-600 hover:text-indigo-800"
            >
                <component
                    :is="assignCourses ? XMarkIcon : PlusCircleIcon"
                    class="w-8 h-8"
                />
                Assign Section
            </button>
        </div>

        <div class="overflow-x-auto">
            <div class="pb-4">

                <!-- Make sure Courses is not null -->
                <div v-if="!student.courses" class="text-center">
                    <p class="text-gray-500 dark:text-gray-400">
                        No course information available.
                    </p>
                </div>
                <!-- Student courses list -->
                <div v-else-if="student.courses" class="flex flex-col">
                    <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Enrolled Courses</span
                        >
                        
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
                                            class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            Course Code
                                        </th>
                                        <th
                                            class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            Credit Hours
                                        </th>
                                        <th
                                            class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            Status
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(course, index) in student.courses"
                                        :key="course.id"
                                        :class="
                                            index % 2 === 0
                                                ? 'bg-white dark:bg-gray-800'
                                                : 'bg-gray-50 dark:bg-gray-700'
                                        "
                                        class="border-b border-gray-300 dark:border-gray-600">
                                        <td
                                            class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            {{
                                                index + 1
                                            }}
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
                                            {{ course.code }}
                                        </td>
                                        <td
                                            class="w-20 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            {{ course.credit_hours }}
                                        </td>

                                        
                                        <!-- Course Assessments -->
                                        <td
                                            class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                        >
                                        <!-- If Status is 1 Active in Green and if Status is 0 Inactive in red -->
                                            <span
                                                :class="
                                                    course.status === 1
                                                        ? 'text-green-500'
                                                        : 'text-red-500'
                                                "
                                                >{{ course.status === 1
                                                    ? 'Active'
                                                    : 'Inactive'
                                                }}</span
                                            >
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-else class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >No Courses Enrolled</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.name }}
                            has not enrolled in any courses yet.
                        </span>
                    </div>
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
                Pick Courses You Would like To Assign To This Section
            </h1>

            <Listbox
                id="cousesList"
                v-model="courseAssignmentForm.courses"
                :options="courses"
                optionLabel="name"
                option-value="id"
                appendTo="self"
                filter
                checkmark
                multiple
                list-style="max-height: 500px"
                placeholder="Select Courses"
                :maxSelectedLabels="3"
                class="w-full"
            />

            <InputError
                :message="courseAssignmentForm.errors.programs"
                class="mt-2 text-sm text-red-500"
            />
            <div class="flex justify-end mt-4">
                <button
                    @click="submitCourseAssignment"
                    :disabled="courseAssignmentForm.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                >
                    {{ courseAssignmentForm.processing ? "Assigning..." : "Assign" }}
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
</template>
