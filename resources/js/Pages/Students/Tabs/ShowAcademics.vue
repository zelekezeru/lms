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
    EyeSlashIcon,
    CalendarIcon,
    BookmarkIcon,
    ClockIcon,
    LockClosedIcon,
    HomeIcon,
    IdentificationIcon,
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
    deletedGrades: {
        type: Array,
        default: () => [],
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
            <div v-if="selectedTab === 'academics'" class="space-y-6">
                <!-- Academic Header Info -->
                <div class="border-b border-gray-100 dark:border-gray-700/60 pb-4">
                    <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">
                        Academic Profile
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Program tracking, section alignment, and transfer records for {{ student.firstName }} {{ student.lastName }}.
                    </p>
                </div>

                <!-- Academic Grid System -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <!-- Program Card -->
                    <div v-if="student.program" class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition duration-300">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                            <AcademicCapIcon class="w-5 h-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Program</p>
                            <p class="text-sm font-bold text-gray-950 dark:text-gray-50 mt-1 truncate">
                                <Link :href="route('programs.show', { program: student.program.id })" class="hover:text-indigo-600 hover:underline">
                                    {{ student.program.name }}
                                </Link>
                            </p>
                        </div>
                    </div>

                    <!-- Track Card -->
                    <div v-if="student.track" class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition duration-300">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 shrink-0">
                            <BookmarkIcon class="w-5 h-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Track / Major</p>
                            <p class="text-sm font-bold text-gray-950 dark:text-gray-50 mt-1 truncate">
                                <Link :href="route('tracks.show', { track: student.track.id })" class="hover:text-purple-600 hover:underline">
                                    {{ student.track.name }}
                                </Link>
                            </p>
                        </div>
                    </div>

                    <!-- Study Mode Card -->
                    <div v-if="student.studyMode" class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition duration-300">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                            <ClockIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Study Mode</p>
                            <p class="text-sm font-bold text-gray-950 dark:text-gray-50 mt-1">
                                {{ student.studyMode.name }}
                            </p>
                        </div>
                    </div>

                    <!-- Academic Year Card -->
                    <div v-if="student.year" class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition duration-300">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                            <CalendarIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Academic Year</p>
                            <p class="text-sm font-bold text-gray-950 dark:text-gray-50 mt-1">
                                {{ student.year.name }}
                            </p>
                        </div>
                    </div>

                    <!-- Semester Card -->
                    <div v-if="student.semester" class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition duration-300">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center text-sky-600 dark:text-sky-400 shrink-0">
                            <PresentationChartBarIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Semester</p>
                            <p class="text-sm font-bold text-gray-950 dark:text-gray-50 mt-1">
                                {{ student.semester.name }}
                            </p>
                        </div>
                    </div>

                    <!-- Center Card -->
                    <div v-if="student.center" class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition duration-300">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
                            <HomeIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Learning Center</p>
                            <p class="text-sm font-bold text-gray-950 dark:text-gray-50 mt-1">
                                {{ student.center.name }}
                            </p>
                        </div>
                    </div>

                    <!-- Section Card -->
                    <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex flex-col justify-between gap-3 shadow-sm hover:shadow-md transition duration-300 md:col-span-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400 shrink-0">
                                    <IdentificationIcon class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Assigned Section</p>
                                    <p class="text-sm font-bold text-gray-950 dark:text-gray-50 mt-1">
                                        <template v-if="student.section">
                                            <Link :href="route('sections.show', { section: student.section.id })" class="hover:text-teal-600 hover:underline">
                                                {{ student.section.name }}
                                            </Link>
                                        </template>
                                        <template v-else>
                                            No Section Assigned
                                        </template>
                                    </p>
                                </div>
                            </div>
                            <!-- Actions -->
                            <button
                                @click="createSection = !createSection"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-indigo-600 dark:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition"
                            >
                                <component :is="createSection ? XMarkIcon : PencilSquareIcon" class="w-3.5 h-3.5" />
                                {{ student.section ? 'Change Section' : 'Assign Section' }}
                            </button>
                        </div>

                        <!-- Dropdown assign alignment -->
                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-2"
                        >
                            <div v-if="createSection" class="pt-2 border-t border-gray-150 dark:border-gray-800 flex flex-col sm:flex-row items-center gap-2">
                                <select
                                    v-model="sectionForm.section_id"
                                    class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 py-2 px-3"
                                >
                                    <option value="">Select Section</option>
                                    <option
                                        v-for="section in sections"
                                        :key="section.id"
                                        :value="section.id"
                                    >
                                        {{ section.name }} ({{ section.program?.name || 'N/A' }})
                                    </option>
                                </select>
                                <div class="flex gap-2 w-full sm:w-auto shrink-0 justify-end">
                                    <button
                                        @click="createSection = false"
                                        class="px-4 py-2 text-xs font-bold bg-gray-100 dark:bg-gray-750 text-gray-700 dark:text-gray-300 hover:bg-gray-200 rounded-xl transition"
                                    >
                                        Cancel
                                    </button>
                                    <PrimaryButton
                                        class="px-4 py-2 text-xs font-bold bg-green-600 text-white rounded-xl hover:bg-green-700 transition"
                                        @click="addSection"
                                    >
                                        Assign
                                    </PrimaryButton>
                                </div>
                            </div>
                        </transition>
                    </div>

                    <!-- Old ID Card (Optional) -->
                    <div v-if="student.oldId" class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3 shadow-sm hover:shadow-md transition duration-300">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-400 shrink-0">
                            <IdentificationIcon class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Old Student ID</p>
                            <p class="text-sm font-bold text-gray-950 dark:text-gray-50 mt-1">
                                {{ student.oldId }}
                            </p>
                        </div>
                    </div>

                    <!-- Default Password Card -->
                    <div v-if="userCan('default-password') && student.user?.default_password" class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-sm hover:shadow-md transition duration-300">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                                <LockClosedIcon class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Portal Access Password</p>
                                <p class="text-sm font-mono font-bold text-gray-900 dark:text-white mt-1">
                                    <span v-if="showPassword">{{ student.user.default_password }}</span>
                                    <span v-else>{{ hashPassword(student.user.default_password) }}</span>
                                </p>
                            </div>
                        </div>
                        <button
                            @click="showPassword = !showPassword"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition"
                        >
                            <EyeSlashIcon v-if="showPassword" class="w-3.5 h-3.5" />
                            <EyeIcon v-else class="w-3.5 h-3.5" />
                            {{ showPassword ? 'Hide' : 'Reveal' }}
                        </button>
                    </div>

                    <!-- Transfer Credits Card -->
                    <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex flex-col justify-between gap-3 shadow-sm hover:shadow-md transition duration-300 md:col-span-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 shrink-0">
                                    <CheckBadgeIcon class="w-5 h-5" />
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Transfer Credits</p>
                                    <p class="text-sm font-bold text-gray-950 dark:text-gray-50 mt-1">
                                        {{ student.transferCredits !== null && student.transferCredits !== undefined && student.transferCredits !== '' ? student.transferCredits + ' credits' : 'No transfer credits recorded' }}
                                    </p>
                                </div>
                            </div>
                            
                            <button
                                v-if="!editingTransferCredits && userCan('update-students')"
                                @click="editingTransferCredits = true; transferCreditsForm.transfer_credits = student.transferCredits ?? ''"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-indigo-600 dark:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition"
                            >
                                <PencilSquareIcon class="w-3.5 h-3.5" />
                                Edit Credits
                            </button>
                        </div>

                        <!-- Expand Edit view -->
                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-2"
                        >
                            <div v-if="editingTransferCredits" class="pt-2 border-t border-gray-150 dark:border-gray-800 flex flex-col sm:flex-row items-center gap-2">
                                <input
                                    v-model.number="transferCreditsForm.transfer_credits"
                                    type="number"
                                    min="0"
                                    max="999"
                                    step="1"
                                    class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 py-2 px-3"
                                    placeholder="Enter transfer credit hours"
                                />
                                <div class="flex gap-2 w-full sm:w-auto shrink-0 justify-end">
                                    <button
                                        @click="editingTransferCredits = false; transferCreditsForm.reset()"
                                        class="px-4 py-2 text-xs font-bold bg-gray-100 dark:bg-gray-755 text-gray-700 dark:text-gray-300 hover:bg-gray-200 rounded-xl transition"
                                    >
                                        Cancel
                                    </button>
                                    <PrimaryButton
                                        class="px-4 py-2 text-xs font-bold bg-green-600 text-white rounded-xl hover:bg-green-700 transition"
                                        @click="saveTransferCredits"
                                        :disabled="transferCreditsForm.processing"
                                    >
                                        Save
                                    </PrimaryButton>
                                </div>
                            </div>
                        </transition>
                        <p v-if="transferCreditsForm.errors.transfer_credits" class="text-xs text-red-500 mt-1">
                            {{ transferCreditsForm.errors.transfer_credits }}
                        </p>
                    </div>

                </div>
            </div>
            <ShowGrades
                v-else-if="selectedTab === 'grades'"
                :student="student"
                :semesters="semesters"
                :grades="grades"
                :deletedGrades="deletedGrades"
                :activeSemester="activeSemester"
            />
        </div>
    </transition>
    <!-- Closing the div for overflow-x-auto -->
</template>
