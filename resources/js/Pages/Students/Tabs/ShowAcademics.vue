<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    CogIcon,
    PencilSquareIcon} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import { Listbox } from "primevue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});

</script>

<template>
    <div class="flex items-center justify-between mb-4">
        <h2
            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
        >
            Academic Information
        </h2>
        
            
        <div class="flex justify-center mb-6">
            <button
                @click="router.visit(route('students.profile', { student: student.id }))"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                Complete Registration
            </button>
        </div> 
    </div>

    <div class="overflow-x-auto">
        <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
    
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
                <div v-if="student.department" class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Department</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        {{ student.department.name }}
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
                </div> <!-- Closing the div for Academic Year -->
                <div v-if="student.semester" class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Semester</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        {{ student.semester.name }}
                    </span>
                </div> <!-- Closing the div for Semester -->
            </div>
        </div> <!-- Closing the div for Academic Information -->
    </div> <!-- Closing the div for overflow-x-auto -->

</template>
