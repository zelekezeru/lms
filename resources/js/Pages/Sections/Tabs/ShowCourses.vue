<script setup>
import { computed, defineProps, nextTick, ref, Teleport } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    CogIcon,
    PencilSquareIcon,
    XMarkIcon,
    PlusCircleIcon,
} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import { Button, Listbox, Popover } from "primevue";
import InputError from "@/Components/InputError.vue";
import {
    ArrowsRightLeftIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/outline";
import PrimaryButton from "@/Components/PrimaryButton.vue";
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
    currentYearLevel: {
        type: Number,
        required: false,
    },
    currentSemesterLevel: {
        type: Number,
        required: false,
    },
});

const selectedYearLevel = ref(props.currentYearLevel);
const selectedSemester = ref(props.currentSemesterLevel);

const showUnassignedOnly = ref(false);

const filteredCourses = computed(() => {
    let courses = props.section.courses;

    if (showUnassignedOnly.value) {
        // Show only unassigned courses
        courses = courses.filter(
            (course) => course.yearLevel === null && course.semester === null
        );
    } else {
        // Show courses based on selected filters
        courses = courses.filter(
            (course) =>
                course.yearLevel === selectedYearLevel.value &&
                course.semester === selectedSemester.value
        );
    }

    return courses;
});

const assignInstructor = ref(false);
const assignCourses = ref(false);
const assignToCourse = ref({});

const elegibleInstructorsList = ref([]);

const courseAssignmentForm = useForm({
    courses: props.section.courses.map((course) => course.id),
});

const closeCourseAssignment = () => {
    assignCourses.value = false;
    courseAssignmentForm.reset();
    courseAssignmentForm.clearErrors();
};

const submitCourseAssignment = () => {
    courseAssignmentForm.post(
        route("courses-section.assign", { section: props.section.id }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Successful!",
                    "Courses assigned successfully.",
                    "success"
                );
                assignCourses.value = false;
                courseAssignmentForm.reset();
                courseAssignmentForm.courses = props.section.courses.map(
                    (course) => course.id
                );
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
    elegibleInstructorsList.value = props.instructors.filter((instructor) =>
        instructor.courses.some((c) => c.id == assignToCourse.value.id)
    );
    instructorAssignmentForm.instructor_id = course.instructor
        ? course.instructor.id
        : "";
};

const closeInstructorAssignemnt = () => {
    assignInstructor.value = false;
    instructorAssignmentForm.reset();
    instructorAssignmentForm.clearErrors();
};

const submitInstructorAssignment = () => {
    instructorAssignmentForm.post(
        route("instructor-courseSection.assign", {
            section: props.section.id,
            course: assignToCourse.value.id,
        }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Successful!",
                    "Instructors assigned successfully.",
                    "success"
                );
                assignInstructor.value = false;
                instructorAssignmentForm.reset();
            },
        }
    );
};

// Popover state
const popOverRef = ref(null);
const selectedCourse = ref(null);
const targetEvent = ref(null);
const targetYear = ref(null);
const targetSemester = ref(null);

function openMovePopover(event, course) {
    selectedCourse.value = course;
    targetEvent.value = event;
    targetYear.value = course.yearLevel;
    targetSemester.value = course.semester;
    // show popover next to the clicked element
    popOverRef.value.show(event);
}

const form = useForm({
    course_id: null,
    year: null,
    semester: null,
});

function submitMove() {
    if (!selectedCourse.value) return;

    form.course_id = selectedCourse.value.id;
    form.year = targetYear.value;
    form.semester = targetSemester.value;

    form.post(route("update-section-course", { section: props.section.id }), {
        onSuccess: () => {
            Swal.fire("Success", `Course moved successfully.`, "success");
            popOverRef.value.hide();
            selectedCourse.value = null;
        },
        onError: () => {
            Swal.fire(
                "Error",
                `There was a problem moving the course.`,
                "error"
            );
        },
    });
}
</script>

<template>
    <div class="">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Courses Of Year {{ selectedYearLevel }}
                <span class="text-indigo-600"
                    >Semester {{ selectedSemester }}</span
                >
                {{
                    selectedYearLevel == currentYearLevel &&
                    selectedSemester == currentSemesterLevel
                        ? "(Current)"
                        : ""
                }}
            </h2>
            <button
                @click="assignCourses = !assignCourses"
                class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800"
            >
                <component
                    :is="assignCourses ? XMarkIcon : PlusCircleIcon"
                    class="w-6 h-6"
                />
                <span class="font-medium">
                    {{ assignCourses ? "Close" : "Assign Courses" }}
                </span>
            </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-end gap-4 mb-2">
            <div class="flex flex-col">
                <label
                    for="year-select"
                    class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Year Level
                </label>
                <select
                    id="year-select"
                    v-model="selectedYearLevel"
                    class="w-32 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option
                        v-for="i in section.track.duration"
                        :key="i"
                        :value="i"
                    >
                        Year {{ i }}
                    </option>
                </select>
            </div>

            <div class="flex flex-col">
                <label
                    for="semester-select"
                    class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Semester
                </label>
                <select
                    id="semester-select"
                    v-model="selectedSemester"
                    class="w-32 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option
                        v-for="i in section.track.duration"
                        :key="i"
                        :value="i"
                    >
                        Semester {{ i }}
                    </option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <input
                    type="checkbox"
                    id="unassignedOnly"
                    v-model="showUnassignedOnly"
                    class="rounded text-indigo-600"
                />
                <label
                    for="unassignedOnly"
                    class="text-sm text-gray-700 dark:text-gray-300"
                >
                    Show Only Courses Not Assigned to Any Curricula
                </label>
            </div>
        </div>

        <div class="overflow-x-auto">
            <div
                class="mt-4 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <!-- Section courses list -->
                <div class="overflow-x-auto">
                    <transition
                        mode="out-in"
                        enter-active-class="transition duration-300 ease-out"
                        enter-from-class="opacity-0 scale-75"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-75"
                    >
                        <table
                            class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                            :key="filteredCourses"
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
                                    <th
                                        class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                    >
                                        Operations
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="(course, index) in filteredCourses"
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
                                        {{ course.credit_hours }}
                                    </td>

                                    <td
                                        class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        <span
                                            v-if="course.instructor"
                                            class="flex justify-between"
                                        >
                                            <Link
                                                :href="
                                                    route('instructors.show', {
                                                        instructor:
                                                            course.instructor,
                                                    })
                                                "
                                            >
                                                {{ course.instructor.name }}
                                            </Link>
                                            <PencilSquareIcon
                                                @click="
                                                    openInstructorAssignemnt(
                                                        course
                                                    )
                                                "
                                                class="w-5 text-green-600 cursor-pointer"
                                            />
                                        </span>
                                        <span v-else>
                                            <button
                                                @click="
                                                    openInstructorAssignemnt(
                                                        course
                                                    )
                                                "
                                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-1 rounded-lg shadow-md transition mr-5"
                                            >
                                                Assign
                                            </button>
                                        </span>
                                    </td>
                                    <!-- Course Assessments showed if instructors are assigned-->
                                    <td>
                                        <Link
                                            v-if="course.instructor"
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
                                            <CogIcon
                                                class="w-5 h-5 inline-block"
                                            />
                                            <span class="inline-block"
                                                >Assessments</span
                                            >
                                        </Link>
                                        <span
                                            v-else
                                            class="text-red-500 cursor-not-allowed"
                                            ><InformationCircleIcon
                                                class="w-5 h-5 inline-block"
                                            />
                                            <span class="inline-block">
                                                No Instructor</span
                                            >
                                        </span>
                                    </td>

                                    <!-- Course Grades -->
                                    <td
                                        class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        <span
                                            :class="
                                                section.grades &&
                                                section.grades.filter(
                                                    (grade) =>
                                                        grade.course_id ===
                                                        course.id
                                                ).length > 0
                                                    ? 'text-green-500 cursor-not-allowed'
                                                    : 'text-red-500 cursor-not-allowed'
                                            "
                                        >
                                            {{
                                                section.grades &&
                                                section.grades.filter(
                                                    (grade) =>
                                                        grade.course_id ===
                                                        course.id
                                                ).length > 0
                                                    ? "Grade Submitted"
                                                    : "Not Submitted"
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        <PrimaryButton
                                            type="button"
                                            class="!bg-green-500"
                                            @click="
                                                (e) =>
                                                    openMovePopover(e, course)
                                            "
                                        >
                                            <ArrowsRightLeftIcon class="w-5" />
                                            Move
                                        </PrimaryButton>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </transition>
                </div>
            </div>
        </div>
    </div>
    <Popover ref="popOverRef">
        <div v-if="selectedCourse" class="space-y-4 w-64">
            <h3 class="font-semibold">
                Move
                {{ selectedCourse.name }}
            </h3>
            <div>
                <label class="block text-sm">Year</label>
                <select
                    v-model="targetYear"
                    class="mt-1 block w-full border rounded"
                >
                    <option
                        v-for="y in section.track.duration"
                        :key="y"
                        :value="y"
                    >
                        Year {{ y }}
                    </option>
                </select>
            </div>
            <div>
                <label class="block text-sm">Semester</label>
                <select
                    v-model="targetSemester"
                    class="mt-1 block w-full border rounded"
                >
                    <option
                        v-for="s in section.track.duration"
                        :key="s"
                        :value="s"
                    >
                        Semester {{ s }}
                    </option>
                </select>
            </div>
            <div class="flex justify-end">
                <Button label="Confirm" @click="submitMove" />
            </div>
        </div>
    </Popover>

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
                    {{
                        courseAssignmentForm.processing
                            ? "Assigning..."
                            : "Assign"
                    }}
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
                Select Instructor You Would like To Assign To The Course "{{
                    assignToCourse.name
                }}" In Section {{ section.name }}
            </h1>

            <h2>Eligible Instructors List</h2>
            <Listbox
                id="cousesList"
                v-model="instructorAssignmentForm.instructor_id"
                :options="elegibleInstructorsList"
                :optionLabel="
                    (option) => `${option.name} - (${option.specialization})`
                "
                option-value="id"
                appendTo="self"
                checkmark
                filter
                empty-message="There Are No Eligible Instructors to teach this course. First Assign Instructors Who Can Teach This Course"
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
                    {{
                        instructorAssignmentForm.processing
                            ? "Assigning..."
                            : "Assign"
                    }}
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
