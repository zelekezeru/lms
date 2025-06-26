<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import {} from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import { Listbox } from "primevue";
import { TrashIcon } from "@heroicons/vue/24/solid";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ShowScholarship from "./Registrations/ShowScholarship.vue";
import ShowRegisteredSemesters from "./Registrations/ShowRegisteredSemesters.vue";
import {
    AcademicCapIcon,
    CheckBadgeIcon,
    CogIcon,
} from "@heroicons/vue/24/outline";

// Initial data
const props = defineProps({
    student: Object,
    updateRoute: String,
    status: Object,
    semesters: Array,
    activeSemester: Object,
});

// Clone and track the status
const student = ref({ ...props.student });

// Use Inertia's useForm to manage submission

// Submit updated status
// function submitStatusChange(field, value) {
//     // Prepare the data to send.  Include the student's ID and the specific field being updated.
//     const postData = {
//         studentId: student.value.id, // Explicitly include student ID
//         [field]: value, // e.g., is_active: true
//     };

//     router.post(
//         route("students.verify", { student: student.value.id }), // Use the named route, include student ID as route parameter.
//         postData, // Send the data object
//         {
//             onSuccess: () => {
//                 Swal.fire(
//                     "Validated!",
//                     "Student Validation updated successfully.",
//                     "success"
//                 );
//             },
//             onError: (errors) => {
//                 Swal.fire(
//                     "Failed!",
//                     Object.values(usePage().props.errors).join("\n") ??
//                         "An unexpected error occurred while updating the student validation.",
//                     "error"
//                 );
//             },
//         }
//     );
// }

// const updateAndSubmit = (field) => {
//     student.value = {
//         ...student.value,
//         [field]: student.value[field] === 1 ? 0 : 1, // Toggle the value
//     };
//     submitStatusChange(field, student.value[field]);
// };

const status = ref({ ...props.status });

// Multi nav header options
const selectedTab = ref("semesters");

const tabs = [
    { key: "semesters", label: "Semester Registrations", icon: CogIcon },
    { key: "scholarship", label: "Scholarship Status", icon: CheckBadgeIcon },
];
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

    <!-- scholarship Panel -->
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
            <!-- Church Panel -->
            <ShowRegisteredSemesters
                v-if="selectedTab === 'semesters'"
                :student="student"
                :status="status"
                :semesters="semesters"
                :active-semester="activeSemester"
                :update-route="updateRoute"
            />
            <!-- Documents Panel -->
            <ShowScholarship
                v-else-if="selectedTab === 'scholarship'"
                :student="student"
                :status="status"
            />
        </div>
    </transition>
</template>
