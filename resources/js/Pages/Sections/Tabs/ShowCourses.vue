<script setup>
import { defineProps, ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    CogIcon,
    PencilSquareIcon, XMarkIcon, PlusCircleIcon} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import { Listbox } from "primevue";
import InputError from "@/Components/InputError.vue";
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

const assignInstructor = ref(false);
const assignCourses = ref(false);
const assignToCourse = ref({});
const courseAssignmentForm = useForm({
    courses: props.section.courses.map(course => course.id),
});

const closeCourseAssignment = () => {
    assignCourses.value = false;
    courseAssignmentForm.reset();
    courseAssignmentForm.clearErrors();
};

const submitCourseAssignment = () => {
    courseAssignmentForm.post(
        route('courses-section.assign', { section: props.section.id }),
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

const instructorAssignmentForm = useForm({
    instructor_id: "",
});

const openInstructorAssignemnt = (course) => {
    assignInstructor.value = true;
    assignToCourse.value = course;
    instructorAssignmentForm.instructor_id = course.instructor.id;
};

const closeInstructorAssignemnt = () => {
    assignInstructor.value = false;
    instructorAssignmentForm.reset();
    instructorAssignmentForm.clearErrors();
};

const submitInstructorAssignment = () => {
    instructorAssignmentForm.post(
        route('instructor-courseSection.assign', { section: props.section.id, course: assignToCourse.value.id}),
        {
            onSuccess: () => {
                Swal.fire(
                    'Successful!',
                    'Instructors assigned successfully.',
                    'success'
                );
                assignInstructor.value = false;
                instructorAssignmentForm.reset();
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
                Assign Courses
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
                <!-- Section courses list -->
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
                                    class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Name
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Course Code
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Credit Hours
                                </th>
                                <th
                                    class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Instructor
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Actions
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(course, index) in section.courses"
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
                                    {{
                                        index + 1
                                    }}
                                </td>

                                <td
                                    class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
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
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ course.creditHour }}
                                </td>

                                <td
                                    class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <span
                                        v-if="course.instructor"
                                        class="flex justify-between"
                                    >
                                        <Link :href="route('instructors.show', {instructor: course.instructor})">
                                            {{ course.instructor.name }}
                                        </Link>
                                        <PencilSquareIcon
                                            @click="
                                                openInstructorAssignemnt(course)
                                            "
                                            class="w-5 text-green-600 cursor-pointer"
                                        />
                                    </span>
                                    <span v-else>
                                        <button
                                            @click="
                                                openInstructorAssignemnt(course)
                                            "
                                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-1 rounded-lg shadow-md transition mr-5"
                                        >
                                            Assign
                                        </button>
                                    </span>
                                </td>
                                <!-- Course Assessments -->
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'assessments.section_course',
                                                {
                                                    course: course.id,
                                                    section: section.id,
                                                }
                                            )
                                        "
                                        class="text-green-500 hover:text-green-700"
                                    >
                                        <CogIcon class="w-5 h-5 inline-block" />
                                        <span class="inline-block"
                                            >Assessments</span
                                        >
                                    </Link>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ course.status }}
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

    <Modal
        :show="assignInstructor"
        @close="closeInstructorAssignemnt"
        :maxWidth="'6xl'"
        class="p-24 h-full"
    >
        <div class="w-full px-16 py-8">
            <h1 class="text-lg mb-5">
                Select Instructor You Would like To Assign To The Course "{{ assignToCourse.name }}" In Section {{ section.name }}
            </h1>

            <Listbox
                id="cousesList"
                v-model="instructorAssignmentForm.instructor_id"
                :options="instructors"
                :optionLabel="(option) => `${option.name} - (${option.specialization})`"
                option-value="id"
                appendTo="self"
                checkmark
                filter
                list-style="max-height: 500px"
                placeholder="Select Instructor"
                :maxSelectedLabels="3"
                class="w-full"
            />

            <InputError
                :message="instructorAssignmentForm.errors.programs"
                class="mt-2 text-sm text-red-500"
            />
            <div class="flex justify-end mt-4">
                <button
                    @click="submitInstructorAssignment"
                    :disabled="instructorAssignmentForm.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                >
                    {{ instructorAssignmentForm.processing ? "Assigning..." : "Assign" }}
                </button>

                <button
                    @click="closeInstructorAssignemnt"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                >
                    Close
                </button>
            </div>
        </div>
    </Modal>
</template>
