<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import Select from "primevue/select";
import Listbox from "primevue/listbox";
import { defineProps, ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import {
    CogIcon,
    PencilSquareIcon,
    XMarkIcon,
    PlusCircleIcon,
    MinusCircleIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    student: { type: Object, required: true },
    courses: { type: Array, required: true },
    studyModes: { type: Array, required: false, default: () => [] },
});

const unAddableCourses = computed(() => props.student.courses.filter(course => course.studentStatus == "Enrolled" || course.studentStatus == "Completed").map(c => c.id));
const filteredCoursesList = computed(() => props.courses.filter(course => ! unAddableCourses.value.includes(course.id)));


const showAddModal = ref(false);
const showDropModal = ref(false);

const addForm = useForm({ course_id: "", section_id: "" });
const dropForm = useForm({});

const enrolledIds = computed(
    () => new Set(props.student.courses.filter((c) => c.studenStatus == "enrolled").map(c => c.id))
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
        selected?.sections?.filter((section) =>
            section.courses
                .map((course) => course.id)
                .includes(addForm.course_id)
        ) || []
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
    console.log(addForm.section_id);

    addForm.post(route("courses-student.add", { student: props.student.id }), {
        onSuccess: () => {
            Swal.fire("Added!", "Course added successfully.", "success");
            showAddModal.value = false;
            resetAdd();
        },
    });
};

const dropCourse = (courseId) => {
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
                route("courses-student.drop", { student: props.student.id }),
                { course_id: courseId },
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

watch(showAddModal, (v) => {
    if (v) resetAdd();
});
watch(showDropModal, (v) => {
    if (v) resetDrop();
});
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

        <div v-if="!student.courses" class="text-center">
            <p class="text-gray-500 dark:text-gray-400">
                No course information available.
            </p>
        </div>

        <div v-else-if="student.courses" class="flex flex-col">
            <div
                class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <span class="text-sm text-gray-500 dark:text-gray-400"
                    >Enrolled Courses</span
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
                            v-for="(course, index) in student.courses"
                            :key="course.id"
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
                                            course: course.id,
                                        })
                                    "
                                >
                                    {{ course.name }}
                                </Link>
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ course.code }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                {{ course.credit_hours }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm font-bold text-gray-600 dark:text-gray-300"
                            >
                                {{
                                    student.grades?.find(
                                        (g) => g.course_id === course.id
                                    )?.grade_letter ?? "Not Graded"
                                }}
                            </td>
                            <td
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                <span
                                    :class="
                                        course.studentStatus === 'Enrolled'
                                            ? 'text-green-500'
                                            : 'text-red-500'
                                    "
                                >
                                    {{ course.studentStatus }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <button
                                    @click="dropCourse(course.id)"
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

        <!-- Add Course Modal -->
        <!-- Add Course Modal -->
        <Modal :show="showAddModal" @close="showAddModal = false">
            <div class="px-8 py-6">
                <h3 class="text-lg font-semibold mb-4">
                    Enroll {{ student.name }} in a New Course
                </h3>

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
                    placeholder="This course is available in..."
                    empty-message="no available section!"
                    option-label="name"
                    append-to="self"
                    option-value="id"
                    class="w-full mb-4 px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />

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
