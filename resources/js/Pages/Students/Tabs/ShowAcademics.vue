<script setup>
import { defineProps, ref, computed } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    CogIcon,
    PencilIcon,
    EyeIcon,
    PencilSquareIcon,
    XMarkIcon,
    PlusCircleIcon,
} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import { Listbox } from "primevue";
import { TrashIcon } from "@heroicons/vue/24/solid";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {
    AcademicCapIcon,
    CheckBadgeIcon,
    PresentationChartBarIcon,
    EyeSlashIcon
} from "@heroicons/vue/24/outline";
import ShowSemesters from "./ShowSemesters.vue";
import ShowResults from "./ShowResults.vue";
import ShowGrades from "./ShowGrades.vue";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    sections: {
        type: Array,
        required: true,
    },
    semesters: {
        type: Array,
        required: true,
    },
    grades: {
        type: Array,
        required: true,
    },
});

// Multi nav header options
const selectedTab = ref("academics");

const tabs = [
    { key: "academics", label: "Academic Info", icon: AcademicCapIcon },
    { key: "grades", label: "Grades", icon: CheckBadgeIcon },
];
// Assign Student to Section

const createSection = ref(false);

const sectionForm = useForm({
    student_id: props.student.id,
    section_id: "",
});

const addSection = () => {
    sectionForm.post(route("students-section.assign"), {
        redirectTo: route("students.show", { student: props.student.id }),
        params: { student: props.student.id, section: sectionForm.section_id },
        onSuccess: () => {
            Swal.fire("Assigned!", "Section Assigned successfully.", "success");
            createSection.value = false;
            sectionForm.reset();
        },
        onError: () => {
            Swal.fire(
                "Error!",
                "There was an error adding the section.",
                "error"
            );
        },
    });
};

const showPassword = ref(false);

function hashPassword(password) {
    // Simple hash for display (not secure, just for obfuscation)
    if (!password) return "";
    return "*".repeat(password.length);
}

// ── Transfer Credits ──────────────────────────────────────────────────
const editingTransferCredits = ref(false);
const transferCreditsForm = useForm({
    transfer_credits: props.student.transferCredits ?? '',
});

const saveTransferCredits = () => {
    transferCreditsForm.patch(
        route('students.transfer-credits', { student: props.student.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                editingTransferCredits.value = false;
            },
        }
    );
};
</script>

<template>
    <nav
        class="flex justify-center space-x-4 overflow-x-auto pb-2 mb-6 border-b border-gray-200 dark:border-gray-700"
    >
        <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="selectedTab = tab.key"
            class="flex-shrink-0 flex items-center px-4 py-2 space-x-2 text-sm font-medium transition whitespace-nowrap"
            :class="
                selectedTab === tab.key
                    ? 'border-b-2 border-indigo-500 text-indigo-600'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200'
            "
        >
            <component :is="tab.icon" class="w-5 h-5" />
            <span>{{ tab.label }}</span>
        </button>
    </nav>

    <!-- Details Panel -->
    <transition
        mode="out-in"
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 scale-75"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-75"
    >
        <div
            :key="selectedTab"
            class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
        >
            <div v-if="selectedTab === 'academics'" class="overflow-x-auto">
                <div class="flex items-center justify-between mb-4">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100 text-center w-full"
                    >
                        Academic Information of -
                        {{ student.firstName }}
                        {{ student.middleName }}
                        {{ student.lastName }}
                    </h2>
                </div>
                
                <div
                    class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
                >
                    <!-- Student details -->
                    <div class="grid grid-cols-2 gap-4">
                        <div v-if="student.program" class="flex flex-col">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                                >Program</span
                            >
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                <Link
                                    :href="route('programs.show', { program: student.program.id })"
                                    class="text-indigo-600 hover:underline"
                                >
                                    {{ student.program.name }}
                                </Link>
                            </span>
                        </div>
                        <div v-if="student.track" class="flex flex-col">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                                >Track</span
                            >
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                <Link
                                    :href="route('tracks.show', { track: student.track.id })"
                                    class="text-indigo-600 hover:underline"
                                >
                                    {{ student.track.name }}
                                </Link>
                            </span>
                        </div>
                        <div v-if="student.studyMode" class="flex flex-col">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                                >Study Mode</span
                            >
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                {{ student.studyMode.name }}
                            </span>
                        </div>
                        <div v-if="student.year" class="flex flex-col">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                                >Academic Year</span
                            >
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                {{ student.year.name }}
                            </span>
                        </div>
                        <!-- Closing the div for Academic Year -->
                        <div v-if="student.semester" class="flex flex-col">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                                >Semester</span
                            >
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                {{ student.semester.name }}
                            </span>
                        </div>
                        <!-- Closing the div for Semester -->

                        <div v-if="student.section" class="flex flex-col">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                                >Section</span
                            >
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2"
                            >
                                <Link
                                    :href="route('sections.show', { section: student.section.id })"
                                    class="text-indigo-600 hover:underline"
                                >
                                    {{ student.section.name }}
                                </Link>
                                <button
                                    @click="createSection = !createSection"
                                    class="ml-2 text-indigo-600 hover:text-indigo-800 flex items-center"
                                    title="Edit Section"
                                >
                                    <PencilSquareIcon class="w-5 h-5" />
                                </button>
                            </span>
                        </div>
                        <div v-else class="flex flex-col">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                                >Section</span
                            >
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-gray-100 col"
                            >
                                No Section Assigned
                                <button
                                    @click="createSection = !createSection"
                                    class="flex text-indigo-600 hover:text-indigo-800"
                                >
                                    <component
                                        :is="
                                            createSection
                                                ? XMarkIcon
                                                : PlusCircleIcon
                                        "
                                        class="w-8 h-8"
                                    />
                                    Assign Section
                                </button>
                            </span>
                        </div>

                        <!-- Default Password -->
                        <div v-if="student.oldId" class="flex flex-col">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                                >Old ID</span
                            >
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                {{ student.oldId }}
                            </span>
                        </div>
                        {{ student.center }}
                        <!-- Default Password -->
                        <div v-if="student.center" class="flex flex-col">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                                >Old ID</span
                            >
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                                {{ student.center.name }}
                            </span>
                        </div>
                        <!-- Default Password -->
                        <div
                            v-if="
                                userCan('default-password') &&
                                student.user.default_password
                            "
                            class="flex flex-col"
                        >
                            <span
                                class="text-sm text-gray-500 dark:text-gray-400"
                                >Default Password</span
                            >
                            <span class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <span v-if="showPassword">
                                    {{ student.user.default_password }}
                                </span>
                                <span v-else>
                                    {{ hashPassword(student.user.default_password) }}
                                </span>
                                <button
                                    @click="showPassword = !showPassword"
                                    class="ml-2 px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600"
                                    type="button"
                                >
                                    <EyeSlashIcon v-if="showPassword" class="w-4 h-4 inline-block" />
                                    <EyeIcon v-else class="w-4 h-4 inline-block" />
                                    {{ showPassword ? 'Hide' : 'Show' }}
                                </button>
                            </span>
                        </div>
                        <!-- Closing the div for Default Password -->

                        <!-- Transfer Credits -->
                        <div class="flex flex-col col-span-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Transfer Credits</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span
                                    v-if="!editingTransferCredits"
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                >
                                    {{ student.transferCredits !== null && student.transferCredits !== undefined ? student.transferCredits : '—' }}
                                </span>
                                <template v-if="editingTransferCredits">
                                    <input
                                        v-model.number="transferCreditsForm.transfer_credits"
                                        type="number"
                                        min="0"
                                        max="9999"
                                        step="1"
                                        class="w-28 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-1 text-sm dark:bg-gray-800 dark:text-gray-100 focus:ring focus:ring-indigo-500"
                                        placeholder="0"
                                    />
                                    <PrimaryButton
                                        @click="saveTransferCredits"
                                        :disabled="transferCreditsForm.processing"
                                        class="px-3 py-1 h-8 text-sm bg-green-500 hover:bg-green-600"
                                    >
                                        Save
                                    </PrimaryButton>
                                    <button
                                        @click="editingTransferCredits = false; transferCreditsForm.reset()"
                                        class="px-3 py-1 h-8 text-sm bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300"
                                    >
                                        Cancel
                                    </button>
                                    <p v-if="transferCreditsForm.errors.transfer_credits" class="text-xs text-red-500">
                                        {{ transferCreditsForm.errors.transfer_credits }}
                                    </p>
                                </template>
                                <button
                                    v-if="!editingTransferCredits && userCan('update-students')"
                                    @click="editingTransferCredits = true; transferCreditsForm.transfer_credits = student.transferCredits ?? ''"
                                    class="ml-1 text-indigo-600 hover:text-indigo-800"
                                    title="Edit Transfer Credits"
                                >
                                    <PencilSquareIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <!-- Closing the div for Transfer Credits -->

                        <!-- Create New Section Row -->
                        <transition
                            enter-active-class="transition duration-300 ease-out"
                            enter-from-class="opacity-0 -translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-200 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-2"
                        >
                            <div v-if="createSection" class="col-span-2 flex items-center gap-4 mt-2">
                                <select
                                    v-model="sectionForm.section_id"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100"
                                >
                                    <option value="">Select Section</option>
                                    <option
                                        v-for="section in sections"
                                        :key="section.id"
                                        :value="section.id"
                                    >
                                        {{ section.name }} -
                                        {{ section.program.name }}
                                    </option>
                                </select>
                                <PrimaryButton
                                    class="px-4 py-1 h-9 bg-green-500 text-white rounded-md hover:bg-green-600"
                                    @click="addSection"
                                >
                                    Save
                                </PrimaryButton>
                                <button
                                    @click="createSection = false"
                                    class="ml-2 px-4 py-1 h-9 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400"
                                >
                                    Cancel
                                </button>
                            </div>
                        </transition>
                    </div>
                </div>
                <!-- Closing the div for Academic Information -->
            </div>
            <ShowGrades
                v-else-if="selectedTab === 'grades'"
                :student="student"
                :semesters="semesters"
                :grades="grades"
                :activeSemester="activeSemester"
            />
        </div>
    </transition>
    <!-- Closing the div for overflow-x-auto -->
</template>
