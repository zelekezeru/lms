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

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    sections: {
        type: Array,
        required: true,
    },
});

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
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Academic Information {{ student.program.name }}
        </h2>
        
    </div>

    <div class="flex items-center justify-between mb-4">

        <div class="flex justify-center mb-6">
            <button
                @click="
                    router.visit(
                        route('students.profile', { student: student.id })
                    )
                "
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                Complete Registrations
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
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
                        >Section</span
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
    <!-- Closing the div for overflow-x-auto -->
</template>
