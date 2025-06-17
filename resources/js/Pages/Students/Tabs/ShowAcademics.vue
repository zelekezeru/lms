<script setup>
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { CogIcon, PencilIcon, EyeIcon, PencilSquareIcon, XMarkIcon, PlusCircleIcon } from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import { Listbox } from "primevue";
import { TrashIcon } from "@heroicons/vue/24/solid";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { AcademicCapIcon, CheckBadgeIcon, PresentationChartBarIcon } from "@heroicons/vue/24/outline";
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
    activeSemester: {
        type: Object,
        required: true,
    },
});

// Multi nav header options
const selectedTab = ref('academics');

const tabs = [
    { key: 'academics', label: 'Academic Info', icon: AcademicCapIcon },
    { key: 'semesters', label: 'Semesters', icon: CogIcon },
    { key: 'Results', label: 'results', icon: PresentationChartBarIcon },
    { key: 'grades', label: 'Grades', icon: CheckBadgeIcon },

];
// Assign Student to Section

const createSection = ref(false);

const sectionForm = useForm({
    student_id: props.student.id,
    section_id: "",
});

const addSection = () => {
    sectionForm.post(
        route("students-section.assign"),
        {
            redirectTo: route("students.show", { student: props.student.id }),
            params: { student: props.student.id, section: sectionForm.section_id },
            onSuccess: () => {
                Swal.fire("Assigned!", "Section Assigned successfully.", "success");
                createSection.value = false;
                sectionForm.reset();
            },
            onError: () => {
                Swal.fire("Error!", "There was an error adding the section.", "error");
            },
        }
    );
};
</script>

<template>

    <nav class="flex justify-center space-x-4 overflow-x-auto pb-2 mb-6 border-b border-gray-200 dark:border-gray-700">
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
        class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700">
        
        <div v-if="selectedTab === 'academics'" class="overflow-x-auto">
            
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 text-center w-full">
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
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Program</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.program.name }}
                        </span>
                    </div>
                    <div v-if="student.track" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Track</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.track.name }}
                        </span>
                    </div>
                    <div v-if="student.year" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
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
                        <span class="text-sm text-gray-500 dark:text-gray-400"
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
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Section</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.section.name }} - {{ student.program.name }}
                        </span>
                    </div>

                    <div v-else class="flex flex-col">
                        
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Section</span
                        >
                        <td>
                            <!-- If the student has no section assigned, show a button to assign a section -->
                            <span
                                class="text-lg font-medium text-gray-900 dark:text-gray-100 col"
                            >
                                No Section Assigned

                                <button
                                    @click="createSection = !createSection"
                                    class="flex text-indigo-600 hover:text-indigo-800"
                                >
                                    <component
                                        :is="createSection ? XMarkIcon : PlusCircleIcon"
                                        class="w-8 h-8"
                                    />
                                    Assign Section
                                </button>
                            </span>
                            <!-- Assign student to section if the Auth::user() has section-assign permission -->
                        </td>
                    </div>
                    <!-- Default Password -->
                    <div v-if="student.oldId" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
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
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Old ID</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.center.name }}
                        </span>
                    </div>
                    <!-- Default Password -->
                    <div v-if="userCan('default-password') && student.user.default_password" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Default Password</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.user.default_password }}
                        </span>
                    </div>
                    <!-- Closing the div for Default Password -->

                            <!-- Create New Section Row -->
                    <transition
                        enter-active-class="transition duration-300 ease-out"
                        enter-from-class="opacity-0 -translate-y-2"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 -translate-y-2">
                        <tr
                            v-if="createSection"
                        >
                        <!-- Select a section from the list of Sections -->
                            <td class="w-full px-4 py-2 text-sm text-gray-600 dark:text-gray-300 ">
                                <select
                                    v-model="sectionForm.section_id"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100"
                                >
                                    <option value="">Select Section</option>
                                    <option v-for="section in sections" :key="section.id" :value="section.id">
                                        {{ section.name }} - {{ section.program.name }}
                                    </option>
                                </select>
                            </td>

                            <td class="flex justify-between">
                                <PrimaryButton
                                    class="px-4 py-1 h-9 bg-green-500 text-white rounded-md hover:bg-green-600"
                                    @click="addSection"
                                >
                                    Save
                                </PrimaryButton>
                            </td>
                        </tr>
                    </transition>
                </div>
            </div>
            <!-- Closing the div for Academic Information -->
        </div>
        <ShowSemesters
            v-else-if="selectedTab === 'semesters'"
            :student="student"
            :semesters="semesters"
            :activeSemester="activeSemester"
        />
        <ShowResults
            v-else-if="selectedTab === 'results'"
            :student="student"
            :section="student.section"
            :courses="student.courses"
            :activeSemester="student.semester"
        />
        <ShowGrades
            v-else-if="selectedTab === 'grades'"
            :student="student"
            :semesters="semesters"
            :activeSemester="activeSemester"
        />

        </div>
    </transition>  
    <!-- Closing the div for overflow-x-auto -->
</template>
