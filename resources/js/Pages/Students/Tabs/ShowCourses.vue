<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import Select from "primevue/select";
import Listbox from "primevue/listbox";
import { defineProps, ref, computed, watch, onMounted, nextTick } from "vue";
import { useForm } from "@inertiajs/vue3";
import {
    CogIcon,
    PencilSquareIcon,
    XMarkIcon,
    PlusCircleIcon,
    MinusCircleIcon,
} from "@heroicons/vue/24/solid";
import {
    BookmarkIcon,
    CheckCircleIcon,
    ClockIcon,
    EyeIcon,
    XCircleIcon,
} from "@heroicons/vue/24/outline";
import Form from "@/Pages/Centers/Form.vue";

const props = defineProps({
    student: { type: Object, required: true },
    courses: { type: Array, required: true },
    studyModes: { type: Array, required: false, default: () => [] },
    activeSemester: { type: Object, required: true },
});

const unAddableCourses = computed(() =>
    props.student.enrollments
        .filter(
            (enrollment) =>
                enrollment.status == "enrolled" ||
                enrollment.status == "completed"
        )
        .map((e) => e.course.id)
);

const filteredCoursesList = computed(() =>
    props.courses.filter(
        (course) => !unAddableCourses.value.includes(course.id)
    )
);

const showAddModal = ref(false);
const showDropModal = ref(false);

const addForm = useForm({ course_id: "", section_id: "" });
const dropForm = useForm({});

const enrolledIds = computed(
    () =>
        new Set(
            props.student.enrollments
                .filter((e) => e.status == "enrolled")
                .map((e) => e.course.id)
        )
);
const availableCourses = computed(() =>
    props.courses
        .filter((c) => !enrolledIds.value.has(c.id))
        .map((c) => ({ ...c, label: c.name }))
);

const selectedStudyModeId = ref(props.studyModes[0].id || null);

const sectionOptions = computed(() => {
    const selected = props.studyModes.find(
        (sm) => sm.id === selectedStudyModeId.value
    );

    return (
        selected?.sections?.filter((section) => {
            const courses = Array.isArray(section.courses)
                ? section.courses
                : [];
            return courses
                .map((course) => course.id)
                .includes(addForm.course_id);
        }) || []
    );
});

const resetAdd = () => {
    addForm.reset();
    addForm.clearErrors();
};
const resetDrop = () => {
    dropForm.reset();
    dropForm.clearErrors();
};

const submitAdd = () => {
    addForm.post(
        route("enrollments-student.add", { student: props.student.id }),
        {
            onSuccess: () => {
                Swal.fire("Added!", "Course added successfully.", "success");
                showAddModal.value = false;
                resetAdd();
            },
        }
    );
};

const addEnrollmentBack = (enrollment) => {
    addForm.course_id = enrollment.course.id;
    showAddModal.value = true;
};

const dropEnrollment = (enrollmentId) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This action will drop the course from the student!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, drop it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                route("enrollments-student.drop", {
                    student: props.student.id,
                }),
                { enrollment_id: enrollmentId },
                {
                    onSuccess: () => {
                        Swal.fire(
                            "Dropped!",
                            "Course dropped successfully.",
                            "success"
                        );
                    },
                }
            );
        }
    });
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Courses
            </h2>
            <div class="space-x-4">
                <button
                    @click="showAddModal = true"
                    class="flex items-center text-green-600 hover:text-green-800"
                >
                    <PlusCircleIcon class="w-6 h-6 mr-1" /> Add Course
                </button>
            </div>
        </div>

        <!-- Courses waiting for student to finish payment -->
        <div
            class="mb-8 border-t border-gray-300 dark:border-gray-600 pt-4 pb-4"
        >
            <div class="flex items-center gap-3 text-green-600">
                <ClockIcon class="w-6 h-6" />
                <h2 class="text-xl font-semibold dark:text-white text-gray-900">
                    Pending Courses For {{ activeSemester.year.name }} Semester
                    {{ activeSemester.level }}
                </h2>
            </div>
            <div
                v-if="
                    student.enrollments.filter(
                        (enrollment) => enrollment.status == 'pending'
                    ).length == 0
                "
                class="text-center"
            >
                <p class="text-gray-500 dark:text-gray-400">
                    No Pending Courses For This Semester.
                </p>
            </div>

            <div
                v-else-if="
                    student.enrollments.filter(
                        (enrollment) => enrollment.status == 'pending'
                    ).length > 0
                "
                class="flex flex-col"
            >
                <div
                    class="mt-10 min-w-[100px] overflow-auto border-t border-gray-300 dark:border-gray-600 pt-4 pb-4"
                >
                    <table
                        class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    No.
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Name
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Course Code
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Credit Hours
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Grade
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(
                                    enrollment, index
                                ) in student.enrollments.filter(
                                    (enrollment) =>
                                        enrollment.status == 'pending'
                                )"
                                :key="enrollment.id"
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                            >
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    <Link
                                        :href="
                                            route('courses.show', {
                                                course: enrollment.course.id,
                                            })
                                        "
                                    >
                                        {{ enrollment.course.name }}
                                    </Link>
                                </td>
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    {{ enrollment.course.code }}
                                </td>
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    {{ enrollment.course.credit_hours }}
                                </td>
                                <td
                                    class="px-4 py-2 text-sm font-bold text-gray-600 dark:text-gray-300"
                                >
                                    {{
                                        student.grades?.find(
                                            (g) =>
                                                g.course_id ===
                                                enrollment.course.id
                                        )?.grade_letter ?? "Not Graded"
                                    }}
                                </td>
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    <span
                                        :class="
                                            enrollment.status === 'enrolled'
                                                ? 'text-green-500'
                                                : 'text-red-500'
                                        "
                                    >
                                        {{ enrollment.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <button
                                        @click="dropEnrollment(enrollment.id)"
                                        class="flex items-center text-red-600 hover:text-red-800"
                                    >
                                        <MinusCircleIcon class="w-4 h-4 mr-1" />
                                        Drop
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Enrolled Courses  -->
        <div class="border-t border-gray-300 dark:border-gray-600 pt-4 pb-4">
            <div class="flex items-center gap-3 text-green-600">
                <BookmarkIcon class="w-6" />
                <h2 class="text-xl font-semibold dark:text-white text-gray-900">
                    Enrolled Courses For {{ activeSemester.year.name }} Semester
                    {{ activeSemester.level }}
                </h2>
            </div>
            <div
                v-if="
                    student.enrollments.filter(
                        (enrollment) => enrollment.status == 'enrolled'
                    ).length == 0
                "
                class="text-center"
            >
                <p class="text-gray-500 dark:text-gray-400">
                    No Enrolled Courses For This Semester.
                </p>
            </div>

            <div
                v-else-if="
                    student.enrollments.filter(
                        (enrollment) => enrollment.status == 'enrolled'
                    ).length > 0
                "
                class="flex flex-col"
            >
                <div
                    class="mt-5 min-w-[100px] overflow-auto border-gray-300 dark:border-gray-600 pt-4 pb-4"
                >
                    <table
                        class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    No.
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Name
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Course Code
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Credit Hours
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Grade
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(
                                    enrollment, index
                                ) in student.enrollments.filter(
                                    (enrollment) =>
                                        enrollment.status == 'enrolled'
                                )"
                                :key="enrollment.id"
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                            >
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    <Link
                                        :href="
                                            route('courses.show', {
                                                course: enrollment.course.id,
                                            })
                                        "
                                    >
                                        {{ enrollment.course.name }}
                                    </Link>
                                </td>
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    {{ enrollment.course.code }}
                                </td>
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    {{ enrollment.course.credit_hours }}
                                </td>
                                <td
                                    class="px-4 py-2 text-sm font-bold text-gray-600 dark:text-gray-300"
                                >
                                    {{
                                        student.grades?.find(
                                            (g) =>
                                                g.course_id ===
                                                enrollment.course.id
                                        )?.grade_letter ?? "Not Graded"
                                    }}
                                </td>
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    <span
                                        :class="
                                            enrollment.status === 'enrolled'
                                                ? 'text-green-500'
                                                : 'text-red-500'
                                        "
                                    >
                                        {{ enrollment.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <button
                                        @click="dropEnrollment(enrollment.id)"
                                        class="flex items-center text-red-600 hover:text-red-800"
                                    >
                                        <MinusCircleIcon class="w-4 h-4 mr-1" />
                                        Drop
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Dropped Courses List -->
        <div class="flex items-center gap-3 mt-8 text-green-600">
            <XCircleIcon class="w-6 h-6" />
            <h2 class="text-xl font-semibold dark:text-white text-gray-900">
                Dropped Courses
            </h2>
        </div>
        <div
            v-if="
                student.enrollments.filter(
                    (enrollment) => enrollment.status == 'dropped'
                ).length == 0
            "
            class="text-center"
        >
            <p class="text-gray-500 dark:text-gray-400">No Dropped Courses.</p>
        </div>

        <div
            v-else-if="
                student.enrollments.filter(
                    (enrollment) => enrollment.status == 'dropped'
                )
            "
            class="flex flex-col"
        >
            <div
                class="mt-8 min-w-[100px] overflow-auto border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <table
                    class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                >
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                No.
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Name
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Course Code
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Credit Hours
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Grade
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(
                                enrollment, index
                            ) in student.enrollments.filter(
                                (enrollment) => enrollment.status == 'dropped'
                            )"
                            :key="enrollment.id"
                            :class="
                                index % 2 === 0
                                    ? 'bg-white dark:bg-gray-800'
                                    : 'bg-gray-50 dark:bg-gray-700'
                            "
                        >
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ index + 1 }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                <Link
                                    :href="
                                        route('courses.show', {
                                            course: enrollment.course.id,
                                        })
                                    "
                                >
                                    {{ enrollment.course.name }}
                                </Link>
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ enrollment.course.code }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ enrollment.course.credit_hours }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm font-bold text-gray-600 dark:text-gray-300"
                            >
                                {{
                                    student.grades?.find(
                                        (g) =>
                                            g.course_id === enrollment.course.id
                                    )?.grade_letter ?? "Not Graded"
                                }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                <span
                                    :class="
                                        enrollment.status === 'enrolled'
                                            ? 'text-green-500'
                                            : 'text-red-500'
                                    "
                                >
                                    {{ enrollment.status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <button
                                    @click="addEnrollmentBack(enrollment)"
                                    class="flex items-center text-green-600 hover:text-green-700"
                                >
                                    <PlusCircleIcon class="w-4 h-4 mr-1" />
                                    Add Back
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- completed Courses List -->
        <div class="flex items-center gap-3 mt-8 mb-6 text-green-600">
            <CheckCircleIcon class="w-6 h-6" />
            <h2 class="text-xl font-semibold dark:text-white text-gray-900">
                Completed Courses
            </h2>
        </div>
        <div
            v-if="
                student.enrollments.filter(
                    (enrollment) => enrollment.status == 'completed'
                ).length == 0
            "
            class="text-center"
        >
            <p class="text-gray-500 dark:text-gray-400">
                No completed Courses.
            </p>
        </div>

        <div
            v-else-if="
                student.enrollments.filter(
                    (enrollment) => enrollment.status == 'completed'
                )
            "
            class="flex flex-col"
        >
            <div
                class="mt-8 min-w-[100px] overflow-auto border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <table
                    class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                >
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                No.
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Name
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Course Code
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Credit Hours
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Grade
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(
                                enrollment, index
                            ) in student.enrollments.filter(
                                (enrollment) => enrollment.status == 'completed'
                            )"
                            :key="enrollment.id"
                            :class="
                                index % 2 === 0
                                    ? 'bg-white dark:bg-gray-800'
                                    : 'bg-gray-50 dark:bg-gray-700'
                            "
                        >
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ index + 1 }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                <Link
                                    :href="
                                        route('courses.show', {
                                            course: enrollment.course.id,
                                        })
                                    "
                                >
                                    {{ enrollment.course.name }}
                                </Link>
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ enrollment.course.code }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ enrollment.course.credit_hours }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm font-bold text-gray-600 dark:text-gray-300"
                            >
                                {{
                                    student.grades?.find(
                                        (g) =>
                                            g.course_id === enrollment.course.id
                                    )?.grade_letter ?? "Not Graded"
                                }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                <span
                                    :class="
                                        enrollment.status === 'enrolled'
                                            ? 'text-green-500'
                                            : 'text-red-500'
                                    "
                                >
                                    {{ enrollment.status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <button
                                    class="flex items-center text-green-600 hover:text-green-800 dark:text-green-300 dark:hover:text-green-400"
                                >
                                    <EyeIcon class="w-4 h-4 mr-1" />
                                    View Results
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Course Modal -->
        <Modal
            :show="showAddModal"
            @close="showAddModal = false"
            :max-width="'4xl'"
        >
            <div class="px-8 py-6">
                <h3 class="text-lg text-center font-light mb-4">
                    Enroll
                    <span class="font-bold"
                        >{{ student.firstName }} {{ student.lastName }}</span
                    >
                    to
                    <span class="font-bold underline">
                        {{
                            addForm.course_id
                                ? courses.find(
                                      (course) => course.id == addForm.course_id
                                  )?.name
                                : "New Course"
                        }}
                    </span>
                </h3>

                <p class="text-red-400">{{ addForm.errors.section_id }}</p>
                <p class="text-red-400">{{ addForm.errors.course_id }}</p>
                <!-- Course Selection -->
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
                >
                    Course
                </label>
                <p class="text-sm text-gray-500 mb-2">
                    Choose a course the student hasn’t taken yet.
                </p>
                <Listbox
                    v-model="addForm.course_id"
                    :options="filteredCoursesList"
                    checkmark
                    optionLabel="name"
                    option-value="id"
                    filter
                    placeholder="Select Course"
                    class="w-full mb-4"
                />

                <!-- Study Mode Selection -->
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
                >
                    Study Mode
                </label>
                <p class="text-sm text-gray-500 mb-2">
                    Select how the course will be attended (e.g. regular,
                    weekend).
                </p>
                <Select
                    v-model="selectedStudyModeId"
                    :options="props.studyModes"
                    placeholder="Select Study Mode"
                    option-label="name"
                    append-to="self"
                    option-value="id"
                    class="w-full mb-4 px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />

                <!-- Section Selection -->
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
                >
                    Section
                </label>
                <p class="text-sm text-gray-500 mb-2">
                    Pick the section where the student will take the given
                    course in the given study mode.
                </p>

                <Select
                    v-model="addForm.section_id"
                    :options="sectionOptions"
                    :placeholder="`This course is available in (${sectionOptions.length})`"
                    empty-message="no available section!"
                    option-label="name"
                    append-to="self"
                    option-value="id"
                    class="w-full mb-4 px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                >
                    <template #option="slotProps">
                        {{ slotProps.option.name }} In
                        {{ slotProps.option.studyMode.name }}
                        <span class="text-sm">
                            ({{ slotProps.option.program.name }} in
                            {{ slotProps.option.track.name }})
                        </span>
                    </template>
                </Select>

                <!-- Action Buttons -->
                <div class="flex justify-end">
                    <button
                        @click="submitAdd"
                        :disabled="addForm.processing"
                        class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded mr-3"
                    >
                        {{ addForm.processing ? "Adding..." : "Add" }}
                    </button>
                    <button
                        @click="showAddModal = false"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>
