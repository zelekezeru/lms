<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    PencilSquareIcon, 
    PlusCircleIcon
} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import { formToJSON } from "axios";
import { Listbox, MultiSelect } from "primevue";
import InputLabel from "@/Components/InputLabel.vue";
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

const selectedTab = ref('details');
const assignCourses = ref(false);
const assignInstructor = ref(false);
const assignToCourse = ref({});

const tabs = [
    { key: 'details', label: 'Details', icon: CogIcon },
    { key: 'courses', label: 'Courses', icon: AcademicCapIcon },
    { key: 'students', label: 'Students', icon: UsersIcon },
];


const students = ref({
    data: [],
    meta: {
        current_page: 1,
        per_page: 50,
    },
});
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
        route('section-courses.attach', { section: props.section.id }),
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
    course_id: ""
});

const opneInstructorAssignemnt = (course) => {
    assignInstructor.value = true;
    assignToCourse.value = course;
    instructorAssignmentForm.course_id = course.id;
    instructorAssignmentForm.instructor_id = course.instructor.id;
};

const closeInstructorAssignemnt = () => {
    assignInstructor.value = false;
    instructorAssignmentForm.reset();
    instructorAssignmentForm.clearErrors();
};

const submitInstructorAssignment = () => {
    instructorAssignmentForm.post(
        route('section-course-instructor.attach', { section: props.section.id }),
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

const deletesection = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
    }).then(result => {
        if (result.isConfirmed) {
            router.delete(route('sections.destroy', { section: id }), {
                onSuccess: () => {
                    Swal.fire(
                        'Deleted!',
                        'The section entry has been successfully deleted.',
                        'success'
                    );
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ section.name }} Section
            </h1>

            <nav
                class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    :class="[
                        'flex items-center px-4 py-2 space-x-2 text-sm font-medium transition',
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
                    ]"
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            <div
                class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
            >
                <!-- Details Panel -->

                <div
                    v-show="selectedTab === 'details'"
                    class="grid grid-cols-2 gap-4"
                >
                    <!-- Section Code -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Code</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.code }}
                        </span>
                    </div>

                    <!-- Section Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.name }}
                        </span>
                    </div>

                    <!-- Section Director -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Director</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.user.name }}
                        </span>
                    </div>

                    <!-- Program name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Program name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.program.name }}
                        </span>
                    </div>

                    <!-- Department  -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Department</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.department.name }}
                        </span>
                    </div>

                    <!-- Year name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Academic Year</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.year.name }}
                        </span>
                    </div>

                    <!-- Semester name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Semester</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.semester.name }}
                        </span>
                    </div>

                    <!-- Edit and Delete Buttons -->

                    <div class="flex justify-end col-span-2 mt-4">
                        <Link
                            v-if="userCan('update-sections')"
                            :href="
                                route('sections.edit', { section: section.id })
                            "
                            class="inline-flex items-center space-x-2 text-blue-500 hover:text-blue-700"
                        >
                            <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                        </Link>
                        <button
                            v-if="userCan('delete-sections')"
                            @click="deletesection(section.id)"
                            class="ml-4 inline-flex items-center space-x-2 text-red-500 hover:text-red-700"
                        >
                            <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
                        </button>
                    </div>

                    <!-- Assign Courses to This section -->
                </div>

                <!-- Courses Panel -->
                <div v-show="selectedTab === 'courses'" class="">
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
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
                                                class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                course, index
                                            ) in section.courses"
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
                                                    index +
                                                    1 +
                                                    (students.meta
                                                        .current_page -
                                                        1) *
                                                        students.meta.per_page
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
                                                class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                {{ course.creditHour }}
                                            </td>

                                            <td
                                                class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                <span v-if="course.instructor" class="flex justify-between">
                                                    {{course.instructor.name}}
                                                        <PencilSquareIcon @click="opneInstructorAssignemnt(course)" class="w-5 text-green-600 cursor-pointer"/>
                                                </span>
                                                <span v-else>
                                                    <button
                                                        @click="opneInstructorAssignemnt(course)"
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
                                                                section:
                                                                    section.id,
                                                            }
                                                        )
                                                    "
                                                    class="text-green-500 hover:text-green-700"
                                                >
                                                    <CogIcon
                                                        class="w-5 h-5 inline-block"
                                                    />
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

                <!-- Students Panel -->
                <div v-show="selectedTab === 'students'" class="">
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Students
                        </h2>
                        <button
                            class="flex items-center text-indigo-600 hover:text-indigo-800"
                        >
                            Add Student
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
                                    Students
                                </h2>
                            </div>
                            <!-- Section Students list -->
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
                                                ID Number
                                            </th>
                                            <th
                                                class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                Department
                                            </th>
                                            <th
                                                class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                            >
                                                Assesment
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                student, index
                                            ) in section.students"
                                            :key="student.id"
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
                                                    index +
                                                    1 +
                                                    (students.meta
                                                        .current_page -
                                                        1) *
                                                        students.meta.per_page
                                                }}
                                            </td>
                                            <td
                                                class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                <Link
                                                    :href="
                                                        route('students.show', {
                                                            student: student.id,
                                                        })
                                                    "
                                                >
                                                    {{ student.student_name }}
                                                    {{ student.father_name }}
                                                    {{
                                                        student.grand_father_name
                                                    }}
                                                </Link>
                                            </td>
                                            <td
                                                class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                {{ student.id_no }}
                                            </td>
                                            <td
                                                class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                {{ section.department.name }}
                                            </td>

                                            <!-- Course Assessments -->
                                            <td
                                                class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                <Link
                                                    :href="
                                                        route(
                                                            'assessments.section_student',
                                                            {
                                                                student:
                                                                    student.id,
                                                                section:
                                                                    section.id,
                                                            }
                                                        )
                                                    "
                                                    class="text-green-500 hover:text-green-700"
                                                >
                                                    <CogIcon
                                                        class="w-5 h-5 inline-block"
                                                    />
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
            </div>
        </div>
    </AppLayout>

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
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                >
                    Assign
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
                optionLabel="name"
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
