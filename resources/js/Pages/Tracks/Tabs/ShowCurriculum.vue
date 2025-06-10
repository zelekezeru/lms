<script setup>
import { defineProps, ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
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

const { t } = useI18n();

// Group Curriculas to YearLevels and semeseters

const buildGroupedCurricula = (selectedStudyModeId) => {
    const groups = [];

    for (let i = 1; i <= programDuration; i++) {
        const semesterGroups = [];
        for (let j = 1; j <= 3; j++) {
            const semesterCurricula = props.track.curricula.filter(
                (curriculum) =>
                    curriculum.semesterLevel == j &&
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
                        semesterCurricula[k]?.course.credit_hours +
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
    semester_level: "",
    description: "",
});

watch(selectedStudyModeId, () => {
    groupedCurricula.value = buildGroupedCurricula(selectedStudyModeId.value);
    curriculaForm.reset();
    editSemisterCurricula.value = false;
});

// when ever a user clicks on a tree node
const treeNodeSelected = (node) => {
    if (node.isSemesterNode) {
        editSemisterCurricula.value = true;
        curriculaForm.courses = node.children.map((child) => child.courseId);
        curriculaForm.year_level = node.yearLevel;
        curriculaForm.semester_level = node.semester;
    } else if (node.isCourseNode) {
        router.get(route("courses.show", { course: node.courseId }));
    }
};

// controll expanded nodes
const expandedKeys = ref({
    1: true,
    "1-1": true,
});

function expandAll(nodes) {
    let _expandedKeys = {};
    const expandNode = (node) => {
        console.log(node.children);
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
                t("programs.tracks.curriculum.assigned_title"),
                t("programs.tracks.curriculum.assigned_text"),
                "success"
            );
        },
    });
};

const allCoursesAreAssigned = ref(
    props.courses.every((course) =>
        props.track.curricula.some(
            (curriculum) => curriculum.course.id === course.id
        )
    )
);

const sortedCourses = computed(() => {
    return [...props.track.courses].sort((a, b) =>
        a.name.localeCompare(b.name)
    );
});
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
                    {{ track.program.name }}
                    {{ t("programs.tracks.in_language") }} {{ track.name }}
                    {{ t("programs.tracks.curriculum.with") }}
                    {{
                        studyModes.find(
                            (studyMode) => studyMode.id == selectedStudyModeId
                        )?.name
                    }}
                    {{ t("programs.tracks.curriculum.mode") }}
                </h2>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2">
                <button
                    @click="openModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                    <PlusIcon class="w-5 h-5" />
                    {{ t("programs.tracks.curriculum.create") }}
                </button>
            </div>

            <div
                class="flex flex-col mb-3 sm:flex-row items-stretch sm:items-center gap-4"
            >
                <div class="flex-1">
                    <Select
                        v-model="selectedStudyModeId"
                        :options="studyModes"
                        option-label="name"
                        option-value="id"
                        :placeholder="
                            t('programs.tracks.curriculum.select_study_mode')
                        "
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
                            {{ t("programs.tracks.curriculum.expand_all") }}
                        </button>
                        <button
                            @click="collapseAll"
                            class="inline-flex items-center gap-2 bg-gray-400 hover:bg-gray-300 dark:bg-gray-700 hover:dark:bg-gray-600 px-4 py-2 text-white rounded-lg shadow"
                        >
                            <MinusIcon class="w-5 h-5" />
                            {{ t("programs.tracks.curriculum.collapse_all") }}
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
                        <div :key="curriculaForm.semester_level">
                            <div
                                v-if="
                                    editSemisterCurricula &&
                                    curriculaForm.study_mode_id
                                "
                                class="flex flex-col h-full"
                            >
                                <h1 class="text-xl font-extralight mb-4">
                                    {{ t("programs.tracks.curriculum.edit") }}
                                    <span class="font-extrabold">
                                        {{
                                            t("programs.tracks.curriculum.year")
                                        }}
                                        {{ curriculaForm.year_level }} –
                                        {{
                                            t(
                                                "programs.tracks.curriculum.semester"
                                            )
                                        }}
                                        {{ curriculaForm.semester_level }}
                                    </span>
                                    {{
                                        t(
                                            "programs.tracks.curriculum.curricula"
                                        )
                                    }}
                                </h1>
                                <p
                                    class="text-red-600"
                                    v-if="allCoursesAreAssigned"
                                >
                                    {{
                                        t(
                                            "programs.tracks.curriculum.all_assigned"
                                        )
                                    }}
                                </p>
                                <Listbox
                                    v-model="curriculaForm.courses"
                                    :options="sortedCourses"
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
                                                        curriculaForm.semester_level !=
                                                            curriculum.semesterLevel)
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
                                                                curriculaForm.semester_level !=
                                                                    curriculum.semesterLevel)
                                                    )
                                                "
                                                class="text-xs text-gray-500 dark:text-gray-200"
                                            >
                                                {{
                                                    t(
                                                        "programs.tracks.curriculum.already_in"
                                                    )
                                                }}
                                                {{
                                                    t(
                                                        "programs.tracks.curriculum.year"
                                                    )
                                                }}
                                                {{
                                                    props.track.curricula.find(
                                                        (curriculum) =>
                                                            curriculum.course
                                                                .id ===
                                                                slotProps.option
                                                                    .id &&
                                                            (curriculum.yearLevel !=
                                                                curriculaForm.year_level ||
                                                                curriculaForm.semester_level !=
                                                                    curriculum.semesterLevel)
                                                    ).yearLevel
                                                }}
                                                {{
                                                    t(
                                                        "programs.tracks.curriculum.semester"
                                                    )
                                                }}
                                                {{
                                                    props.track.curricula.find(
                                                        (curriculum) =>
                                                            curriculum.course
                                                                .id ===
                                                                slotProps.option
                                                                    .id &&
                                                            (curriculum.yearLevel !=
                                                                curriculaForm.year_level ||
                                                                curriculaForm.semester_level !=
                                                                    curriculum.semesterLevel)
                                                    ).semesterLevel
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
                                        {{ t("common.cancel", "Cancel") }}
                                    </button>
                                    <button
                                        @click="saveCurricula"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-green-400"
                                    >
                                        {{
                                            t("programs.tracks.curriculum.save")
                                        }}
                                    </button>
                                </div>
                            </div>

                            <div
                                class="h-[400px] flex items-center justify-center"
                                v-else
                            >
                                <h1 class="text-center">
                                    {{
                                        t(
                                            "programs.tracks.curriculum.choose_study_mode"
                                        )
                                    }}
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
                    {{ track.name }}
                    {{ t("programs.tracks.curriculum.design") }}
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
                            {{ t("programs.tracks.curriculum.year") }}
                        </label>
                        <Select
                            id="yearLevelsList"
                            v-model="form.year_level"
                            :options="yearLevels"
                            appendTo="self"
                            :placeholder="
                                t('programs.tracks.curriculum.select_year')
                            "
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
                            {{ t("programs.tracks.curriculum.semester") }}
                        </label>
                        <Select
                            id="semestersList"
                            v-model="form.semester_level"
                            :options="[1, 2, 3]"
                            appendTo="self"
                            :placeholder="
                                t('programs.tracks.curriculum.select_semester')
                            "
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
                            {{ t("programs.tracks.curriculum.study_mode") }}
                        </label>
                        <Select
                            id="studyModesList"
                            v-model="form.study_mode_id"
                            :options="track.program.studyModes"
                            option-value="id"
                            option-label="name"
                            appendTo="self"
                            :placeholder="
                                t('programs.tracks.curriculum.select_mode')
                            "
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
                            {{ t("programs.tracks.curriculum.description") }}
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
                            {{ t("programs.tracks.curriculum.status") }}
                        </label>
                        <select
                            id="status"
                            v-model="form.status"
                            class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                        >
                            <option value="" disabled>
                                {{
                                    t(
                                        "programs.tracks.curriculum.select_status"
                                    )
                                }}
                            </option>
                            <option value="active">
                                {{ t("programs.tracks.curriculum.active") }}
                            </option>
                            <option value="inactive">
                                {{ t("programs.tracks.curriculum.inactive") }}
                            </option>
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
                        {{ t("programs.tracks.curriculum.select_courses") }}
                    </label>
                    <Listbox
                        v-model="form.courses"
                        :options="sortedCourses"
                        option-label="name"
                        option-value="id"
                        appendTo="self"
                        filter
                        checkmark
                        multiple
                        :placeholder="
                            t(
                                'programs.tracks.curriculum.search_select_courses'
                            )
                        "
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
                    {{
                        form.processing
                            ? t("programs.tracks.curriculum.assigning")
                            : t("programs.tracks.curriculum.assign")
                    }}
                </button>
                <button
                    type="button"
                    @click="closeModal"
                    class="ml-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-4 py-2 rounded-md focus:outline-none"
                >
                    {{ t("common.cancel", "Cancel") }}
                </button>
            </div>
        </form>
    </Modal>
</template>
