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

// Define the props for the instructor
const props = defineProps({
    instructor: {
        type: Object,
        required: true,
    },
    courses: {
        type: Object,
        required: true,
    },
});

const assignCourses = ref(false);

const assignToCourse = ref({});

const courseAssignmentForm = useForm({
    courses: props.instructor.courses.map(course => course.id),
});

const closeCourseAssignment = () => {
    assignCourses.value = false;
    courseAssignmentForm.reset();
    courseAssignmentForm.clearErrors();
};

const submitCourseAssignment = () => {
    courseAssignmentForm.post(
        route('courses-instructor.assign', { instructor: props.instructor.id }),
        {
            onSuccess: () => {
                Swal.fire(
                    'Successful!',
                    'Courses assigned successfully.',
                    'success'
                );
                assignCourses.value = false;
                courseAssignmentForm.reset();
                courseAssignmentForm.courses = props.instructor.courses.map(course => course.id);
            },
        }
    );
};

const imageLoaded = ref(false);

</script>

<template>
    <div class="">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Courses
            </h2>
            <button
                @click="assignCourses = !assignCourses"
                class="flex items-center text-indigo-600 hover:text-indigo-800"
            >
                Assign Course
            </button>
        </div>
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
                <!-- Instructor courses list -->
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
                                <th
                                    class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Sections
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(course, index) in instructor.courses"
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

                                <td
                                    class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                <Link v-for="(section, index) in instructor.sections.filter((section) => section.course.id == course.id)" :href="route('sections.show', {section: section.id})" :key="section.id">
                                    {{ section.name }}{{ (index + 1) == instructor.sections.filter((section) => section.course.id == course.id).length ? "" : ", " }}</Link>
                                </td>
                                <!-- Course Assessments -->
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <Link
                                        class="text-green-500 hover:text-green-700"
                                    >
                                        <CogIcon class="w-5 h-5 inline-block" />
                                        <span class="inline-block"
                                            >Assessments</span
                                        >
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                    {{ courseAssignmentForm.processing ? "Assign..." : "Assign"}}
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
