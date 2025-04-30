<script setup>
import { defineProps, ref, watch } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import { Listbox, Select, Tree } from "primevue";
import {
    AcademicCapIcon,
    BookOpenIcon,
    CalendarDaysIcon,
} from "@heroicons/vue/24/solid";
import { MinusIcon, PlusIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    track: Object,
    courses: Array,
    curriculums: Array,
});

const showModal = ref(false);
const programDuration = props.track.program.duration;
const yearLevels = [];
const studyModes = props.track.program.studyModes;

for (let i = 1; i <= programDuration; i++) {
    yearLevels.push(i);
}

const form = useForm({
    study_mode_id: "",
    track_id: props.track.id,
    courses: [],
    year_level: "",
    semester: "",
    description: "",
});

const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const createCurricula = () => {
    form.post(route("curricula.store"), {
        onSuccess: () => {
            Swal.fire(
                "Assigned!",
                "Curriculum updated successfully.",
                "success"
            );
            closeModal();
        },
    });
};

// Group Curriculas to YearLevels and semeseters

const buildGroupedCurricula = (selectedStudyModeId) => {
    const groups = [];

    for (let i = 1; i <= programDuration; i++) {
        const semesterGroups = [];
        for (let j = 1; j <= 3; j++) {
            const semesterCurricula = props.track.curricula.filter(
                (curriculum) =>
                    curriculum.semester == j &&
                    curriculum.yearLevel == i &&
                    curriculum.studyMode.id == selectedStudyModeId
            );
            const courseGroups = [];

            for (let k = 0; k < semesterCurricula.length; k++) {
                const courseGroup = {
                    key: i + "-" + j + "-" + k,
                    isCourseNode: true,
                    courseId: semesterCurricula[k]?.course.id,
                    label:
                        semesterCurricula[k]?.course.name +
                        " (" +
                        semesterCurricula[k]?.course.creditHours +
                        " Credit Hrs. )",
                };

                courseGroups.push(courseGroup);
            }

            const semesterGroup = {
                key: i + "-" + j,
                studyMode: selectedStudyModeId,
                yearLevel: i,
                semester: j,
                isSemesterNode: true,
                label: "Semester " + j,
                children: courseGroups,
            };

            semesterGroups.push(semesterGroup);
        }

        const yearGroup = {
            key: i,
            isYearNode: true,
            label: "Year " + i,
            children: semesterGroups,
        };

        groups.push(yearGroup);
    }

    return groups;
};

const getIcon = (node) => {
    if (node.isYearNode) return AcademicCapIcon;
    if (node.isSemesterNode) return CalendarDaysIcon;
    if (node.isCourseNode) return BookOpenIcon;
    return BookOpenIcon;
};

const selectedStudyModeId = ref(props.track.program.studyModes[0]?.id);
const editSemisterCurricula = ref(false);
const groupedCurricula = ref(buildGroupedCurricula(selectedStudyModeId.value));

watch(
    () => props.track.curricula,
    () => {
        groupedCurricula.value = buildGroupedCurricula(
            selectedStudyModeId.value
        );
    }
);

const curriculaForm = useForm({
    study_mode_id: selectedStudyModeId,
    track_id: props.track.id,
    courses: [],
    year_level: "",
    semester: "",
    description: "",
});

watch(selectedStudyModeId, () => {
    groupedCurricula.value = buildGroupedCurricula(selectedStudyModeId.value);
    curriculaForm.reset();
    editSemisterCurricula.value = false;
});

const treeNodeSelected = (node) => {
    if (node.isSemesterNode) {
        editSemisterCurricula.value = true;
        curriculaForm.courses = node.children.map((child) => child.courseId);
        curriculaForm.year_level = node.yearLevel;
        curriculaForm.semester = node.semester;
    } else if (node.isCourseNode) {
        router.get(route("courses.show", { course: node.courseId }));
    }
};

const expandedKeys = ref({
    1: true,
    "1-1": true,
});

function expandAll(nodes) {
    let _expandedKeys = {};
    const expandNode = (node) => {
        if (node.children && node.children.length) {
            _expandedKeys[node.key] = true;
            node.children.forEach((child) => expandNode(child));
        }
    };
    nodes.forEach((node) => expandNode(node));
    expandedKeys.value = _expandedKeys;
}

function collapseAll() {
    expandedKeys.value = {};
}

const saveCurricula = () => {
    curriculaForm.post(route("curricula.store"), {
        onSuccess: () => {
            Swal.fire(
                "Assigned!",
                "Curriculum updated successfully.",
                "success"
            );
        },
    });
};

const allCoursesAreAssigned = ref(
    props.courses.every((course) =>
        props.track.curricula
            .some(
                (curriculum) =>
                    curriculum.course.id === course.id &&
                    (curriculum.yearLevel != curriculaForm.year_level ||
                        curriculaForm.semester != curriculum.semester)
            )
    )
);
</script>

<template>
    <div class="">
        <div class="p-4">
            <!-- Header -->
            <div
                class="flex flex-col mb-3 sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <h2
                    class="text-2xl font-semibold text-gray-900 dark:text-gray-100"
                >
                    {{ track.program.name }} in {{ track.name }} With
                    {{
                        studyModes.find(
                            (studyMode) => studyMode.id == selectedStudyModeId
                        )?.mode
                    }}
                    Mode - Curriculum
                </h2>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2">
                <button
                    @click="openModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                    <PlusIcon class="w-5 h-5" />
                    Create Curriculum
                </button>
            </div>

            <div
                class="flex flex-col mb-3 sm:flex-row items-stretch sm:items-center gap-4"
            >
                <div class="flex-1">
                    <Select
                        v-model="selectedStudyModeId"
                        :options="studyModes"
                        option-label="mode"
                        option-value="id"
                        placeholder="Select Study Mode"
                        class="w-full"
                    />
                </div>
            </div>

            <div class="flex flex-col lg:flex-row items-start gap-6">
                <div
                    class="flex-1 rounded-lg shadow p-4 overflow-auto max-h-[500px] border border-gray-700 dark:border-gray-500"
                >
                    <div>
                        <button
                            @click="expandAll(groupedCurricula)"
                            class="inline-flex items-center gap-2 mr-3 bg-gray-400 hover:bg-gray-300 dark:bg-gray-700 hover:dark:bg-gray-600 px-4 py-2 text-white rounded-lg shadow"
                        >
                            <PlusIcon class="w-5 h-5" />
                            Expand All
                        </button>
                        <button
                            @click="collapseAll"
                            class="inline-flex items-center gap-2 bg-gray-400 hover:bg-gray-300 dark:bg-gray-700 hover:dark:bg-gray-600 px-4 py-2 text-white rounded-lg shadow"
                        >
                            <MinusIcon class="w-5 h-5" />
                            Collapse All
                        </button>
                    </div>
                    <Tree
                        :value="groupedCurricula"
                        selection-mode="single"
                        v-model:expanded-keys="expandedKeys"
                        @node-select="treeNodeSelected"
                        filter
                        class="w-full bg-inherit dark:bg-inherit"
                    >
                        <template #default="{ node }">
                            <div
                                class="flex items-center gap-2"
                                :class="{
                                    'cursor-pointer text-blue-300 dark:text-blue-400':
                                        node.isCourseNode,
                                    'text-gray-700 dark:text-gray-300':
                                        !node.isCourseNode,
                                }"
                            >
                                <component
                                    :is="getIcon(node)"
                                    class="w-5 h-5"
                                />
                                <span class="truncate">{{ node.label }}</span>
                            </div>
                        </template>
                    </Tree>
                </div>

                <!-- Courses Selection Panel -->
                <div
                    class="flex-1 rounded-lg shadow p-4 max-h-[500px] border border-gray-700 dark:border-gray-500"
                >
                    <transition
                        mode="out-in"
                        enter-active-class="transition duration-300 ease-out"
                        enter-from-class="opacity-0 scale-75"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-75"
                    >
                        <div :key="curriculaForm.semester">
                            <div
                                v-if="editSemisterCurricula"
                                class="flex flex-col h-full"
                            >
                                <h1 class="text-xl font-extralight mb-4">
                                    Edit
                                    <span class="font-extrabold">
                                        Year {{ curriculaForm.year_level }} –
                                        Semester {{ curriculaForm.semester }}
                                    </span>
                                    Curricula
                                </h1>
                                <!-- Message if all courses has been assigned to a curricula for tommorow. -->
                                <p
                                    class="text-red-600"
                                    v-if="allCoursesAreAssigned"
                                >
                                    It Appears All Courses Are Assigned To Their
                                    Curricula.
                                </p>
                                <Listbox
                                    v-model="curriculaForm.courses"
                                    :options="props.track.courses"
                                    option-label="name"
                                    option-value="id"
                                    multiple
                                    filter
                                    checkmark
                                    :option-disabled="
                                        (option) =>
                                            props.track.curricula.some(
                                                (curriculum) =>
                                                    curriculum.course.id ===
                                                        option.id &&
                                                    (curriculum.yearLevel !=
                                                        curriculaForm.year_level ||
                                                        curriculaForm.semester !=
                                                            curriculum.semester)
                                            )
                                    "
                                    placeholder="Select Courses"
                                    class="w-full flex-1 bg-inherit dark:bg-inherit"
                                    list-style="max-height: 300px"
                                >
                                    <template #option="slotProps">
                                        <div class="flex flex-col">
                                            <span>{{
                                                slotProps.option.name
                                            }}</span>
                                            <small
                                                v-if="
                                                    props.track.curricula.some(
                                                        (curriculum) =>
                                                            curriculum.course
                                                                .id ===
                                                                slotProps.option
                                                                    .id &&
                                                            (curriculum.yearLevel !=
                                                                curriculaForm.year_level ||
                                                                curriculaForm.semester !=
                                                                    curriculum.semester)
                                                    )
                                                "
                                                class="text-xs text-gray-500 dark:text-gray-200"
                                            >
                                                Already in: Year
                                                {{
                                                    props.track.curricula.find(
                                                        (curriculum) =>
                                                            curriculum.course
                                                                .id ===
                                                                slotProps.option
                                                                    .id &&
                                                            (curriculum.yearLevel !=
                                                                curriculaForm.year_level ||
                                                                curriculaForm.semester !=
                                                                    curriculum.semester)
                                                    ).yearLevel
                                                }}
                                                Semester
                                                {{
                                                    props.track.curricula.find(
                                                        (curriculum) =>
                                                            curriculum.course
                                                                .id ===
                                                                slotProps.option
                                                                    .id &&
                                                            (curriculum.yearLevel !=
                                                                curriculaForm.year_level ||
                                                                curriculaForm.semester !=
                                                                    curriculum.semester)
                                                    ).semester
                                                }}
                                            </small>
                                        </div>
                                    </template>
                                </Listbox>

                                <div class="mt-4 flex justify-end gap-2">
                                    <button
                                        @click="cancelEdit"
                                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg shadow"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="saveCurricula"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-green-400"
                                    >
                                        Save Curricula
                                    </button>
                                </div>
                            </div>

                            <div
                                class="h-[400px] flex items-center justify-center"
                                v-else
                            >
                                <h1 class="text-center">
                                    Click On A Semester To Edit Its Curricula
                                </h1>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <Modal :show="showModal" @close="closeModal" :maxWidth="'6xl'">
        <form @submit.prevent="createCurricula" class="flex flex-col h-full">
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-6 py-4"
            >
                <h2
                    class="text-2xl font-semibold text-gray-900 dark:text-gray-100"
                >
                    {{ track.name }} Curriculum Design
                </h2>
                <button
                    type="button"
                    @click="closeModal"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6 overflow-auto">
                <!-- Row 1: Year, Semester, Study Mode -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Year -->
                    <div>
                        <label
                            for="yearLevelsList"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Year
                        </label>
                        <Select
                            id="yearLevelsList"
                            v-model="form.year_level"
                            :options="yearLevels"
                            appendTo="self"
                            placeholder="Select Year"
                            class="w-full"
                        />
                        <InputError :message="form.errors.year" class="mt-2" />
                    </div>

                    <!-- Semester -->
                    <div>
                        <label
                            for="semestersList"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Semester
                        </label>
                        <Select
                            id="semestersList"
                            v-model="form.semester"
                            :options="[1, 2, 3]"
                            appendTo="self"
                            placeholder="Select Semester"
                            class="w-full"
                        />
                        <InputError
                            :message="form.errors.semester"
                            class="mt-2"
                        />
                    </div>

                    <!-- Study Mode -->
                    <div>
                        <label
                            for="studyModesList"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Study Mode
                        </label>
                        <Select
                            id="studyModesList"
                            v-model="form.study_mode_id"
                            :options="track.program.studyModes"
                            option-value="id"
                            option-label="mode"
                            appendTo="self"
                            placeholder="Select Mode"
                            class="w-full"
                        />
                        <InputError
                            :message="form.errors.studyMode"
                            class="mt-2"
                        />
                    </div>
                </div>

                <!-- Row 2: Description, Status, Course Selection -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Description -->
                    <div>
                        <label
                            for="description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Curriculum Description
                        </label>
                        <input
                            id="description"
                            type="text"
                            v-model="form.description"
                            class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                        />
                        <InputError
                            :message="form.errors.description"
                            class="mt-2"
                        />
                    </div>

                    <!-- Status -->
                    <div>
                        <label
                            for="status"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Curriculum Status
                        </label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                        >
                            <option value="" disabled>Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <InputError
                            :message="form.errors.status"
                            class="mt-2"
                        />
                    </div>

                    <!-- Course Selection -->
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                        Select Courses
                    </label>
                    <Listbox
                        v-model="form.courses"
                        :options="track.courses"
                        option-label="name"
                        option-value="id"
                        appendTo="self"
                        filter
                        checkmark
                        multiple
                        placeholder="Search and select courses"
                        class="w-full"
                        list-style="max-height: 400px"
                    />
                    <InputError :message="form.errors.courses" class="mt-2" />
                </div>
            </div>

            <!-- Footer -->
            <div
                class="flex items-center justify-end border-t border-gray-200 dark:border-gray-700 px-6 py-4"
            >
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-md focus:outline-none disabled:opacity-50"
                >
                    {{ form.processing ? "Assigning..." : "Assign" }}
                </button>
                <button
                    type="button"
                    @click="closeModal"
                    class="ml-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-4 py-2 rounded-md focus:outline-none"
                >
                    Cancel
                </button>
            </div>
        </form>
    </Modal>
</template>
