<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { defineProps, ref, reactive, watch } from "vue";
import {  } from '@inertiajs/vue3'
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, MultiSelect } from "primevue";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    PencilSquareIcon,
    PlusCircleIcon,
    BookOpenIcon,
    HomeModernIcon,
} from "@heroicons/vue/24/solid";


const showVerifyModal = ref(false);

// Initial data
const props = defineProps({
    student: Object,
    updateRoute: String // No longer directly used, but can be kept for other purposes if needed.
})

// Clone and track the student
const student = ref({ ...props.student })

// Use Inertia's useForm to manage submission
const form = useForm({
    id: student.value.id,
    is_active: student.value.is_active,
    is_approved: student.value.is_approved,
    is_completed: student.value.is_completed,
    is_verified: student.value.is_verified,
    is_enrolled: student.value.is_enrolled,
    is_graduated: student.value.is_graduated,
    is_scholarship: student.value.is_scholarship,
    is_scholarship_approved: student.value.is_scholarship_approved,
    is_scholarship_verified: student.value.is_scholarship_verified,
    _method: 'PATCH', // Consider if this is still needed.  POST is usually used for new data.
})

// Submit updated status
function submitStatusChange(field, value) {
    // form[field] = value;  // No longer update the form, we send data directly.

    // Prepare the data to send.  Include the student's ID and the specific field being updated.
    const postData = {
        studentId: student.value.id, // Explicitly include student ID
        [field]: value, // e.g., is_active: true
    };

    router.post(
        route('students.verify', { student: student.value.id }), // Use the named route, include student ID as route parameter.
        postData, // Send the data object
        {
            preserveScroll: true,
            onSuccess: () => {
                console.log(`${field} updated to ${value}`);
            },
            onError: (errors) => {
                console.error("Update failed", errors);
            },
        }
    );
}

function updateAndSubmit(field, value) {
    student.value[field] = value; // Directly update the student ref
    submitStatusChange(field, value);
}

</script>

<template>
    <div class="flex items-center justify-between mb-4">
        <h2
            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
        >
            Registration Details
        </h2>

    </div>

    <div class="overflow-x-auto">
        <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
            <div class="grid gap-4">

                <div  class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                          >Verifications</span
                    >

                    <table
                        class="w-full table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th
                                class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                            >
                                No.
                            </th>
                            <th
                                class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                            >
                                Title
                            </th>
                            <th
                                class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                            >
                                Responsible Person
                            </th>
                            <th
                                class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                            >
                                Current State
                            </th>
                            <th
                                class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                            >
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                1
                            </td>

                            <td class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">

                                Status
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                {{ student.created_by_name }}
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <span class="text-lg mx-4" :class="{
                                    'text-green-500 dark:text-green-400': student.is_active,
                                    'text-red-500 dark:text-red-400': !student.is_active
                                }">
                                    {{ student.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <label class="mx-2 relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="student.is_active"
                                        @change="updateAndSubmit('is_active', student.is_active)"
                                        class="sr-only peer"/>

                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                        :class="{
                                            'bg-green-500': student.is_active,
                                            'bg-red-500': !student.is_active
                                        }"></div>

                                    <div
                                        class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                        :class="{ 'translate-x-full': student.is_active }"></div>
                                </label>
                            </td>
                        </tr>
                        <tr :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'">
                            <td class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                2
                            </td>

                            <td class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">

                                Approval
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                {{ student.approved_by_name }}
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <span class="text-lg mx-4" :class="{
                                    'text-green-500 dark:text-green-400': student.is_approved,
                                    'text-red-500 dark:text-red-400': !student.is_approved
                                }">
                                    {{ student.is_approved ? 'Approved' : 'Not Approved' }}
                                </span>
                            </td>
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <label class="mx-2 relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="student.is_approved"
                                        @change="updateAndSubmit('is_approved', student.is_approved)"
                                        class="sr-only peer"/>

                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                        :class="{
                                            'bg-green-500': student.is_approved,
                                            'bg-red-500': !student.is_approved
                                        }"></div>

                                    <div
                                        class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                        :class="{ 'translate-x-full': student.is_approved }"></div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                3
                            </td>

                            <td class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">

                                Completion
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                {{ student.completed_by_name }}
                            </td>
                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <span class="text-lg mx-4" :class="{
                                    'text-green-500 dark:text-green-400': student.is_completed,
                                    'text-red-500 dark:text-red-400': !student.is_completed
                                }">
                                    {{ student.is_completed ? 'Completed' : 'Not Completed' }}
                                </span>
                            </td>
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <label class="mx-2 relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="student.is_completed"
                                        @change="updateAndSubmit('is_completed', student.is_completed)"
                                        class="sr-only peer"/>

                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                        :class="{
                                            'bg-green-500': student.is_completed,
                                            'bg-red-500': !student.is_completed
                                        }"></div>

                                    <div
                                        class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                        :class="{ 'translate-x-full': student.is_completed }"></div>
                                </label>
                            </td>
                        </tr>
                        <tr :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'">
                            <td class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                4
                            </td>

                            <td class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">

                                Verification
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                {{ student.verified_by_name }}
                            </td>
                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <span class="text-lg mx-4" :class="{
                                    'text-green-500 dark:text-green-400': student.is_verified,
                                    'text-red-500 dark:text-red-400': !student.is_verified
                                }">
                                    {{ student.is_verified ? 'Verified' : 'Not Verified' }}
                                </span>
                            </td>
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <label class="mx-2 relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="student.is_verified"
                                        @change="updateAndSubmit('is_verified', student.is_verified)"
                                        class="sr-only peer"/>

                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                        :class="{
                                            'bg-green-500': student.is_verified,
                                            'bg-red-500': !student.is_verified
                                        }"></div>

                                    <div
                                        class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                        :class="{ 'translate-x-full': student.is_verified }"></div>
                                </label>
                            </td>
                        </tr>
                            <!-- Toggle tr for Enrollment -->
                        <tr>
                            <td class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                5
                            </td>

                            <td class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">

                                Enrollment
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                {{ student.enrolled_by_name }}
                            </td>
                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <span class="text-lg mx-4" :class="{
                                    'text-green-500 dark:text-green-400': student.is_enrolled,
                                    'text-red-500 dark:text-red-400': !student.is_enrolled
                                }">
                                    {{ student.is_enrolled ? 'Enrolled' : 'Not Enrolled' }}
                                </span>
                            </td>
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <label class="mx-2 relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="student.is_enrolled"
                                        @change="updateAndSubmit('is_enrolled', student.is_enrolled)"
                                        class="sr-only peer"/>

                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                        :class="{
                                            'bg-green-500': student.is_enrolled,
                                            'bg-red-500': !student.is_enrolled
                                        }"></div>

                                    <div
                                        class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                        :class="{ 'translate-x-full': student.is_enrolled }"></div>
                                </label>
                            </td>
                        </tr>
                        <tr :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'">
                            <td class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                6
                            </td>

                            <td class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">

                                Graduation
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                {{ student.graduated_by_name }}
                            </td>
                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <span class="text-lg mx-4" :class="{
                                    'text-green-500 dark:text-green-400': student.is_graduated,
                                    'text-red-500 dark:text-red-400': !student.is_graduated
                                }">
                                    {{ student.is_graduated ? 'Graduated' : 'Not Graduated' }}
                                </span>
                            </td>
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <label class="mx-2 relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="student.is_graduated"
                                        @change="updateAndSubmit('is_graduated', student.is_graduated)"
                                        class="sr-only peer"/>

                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                        :class="{
                                            'bg-green-500': student.is_graduated,
                                            'bg-red-500': !student.is_graduated
                                        }"></div>

                                    <div
                                        class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                        :class="{ 'translate-x-full': student.is_graduated }"></div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                7
                            </td>

                            <td class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">

                                Scholarship
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                {{ student.scholarship_by_name }}
                            </td>
                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <span class="text-lg mx-4" :class="{
                                    'text-green-500 dark:text-green-400': student.is_scholarship,
                                    'text-red-500 dark:text-red-400': !student.is_scholarship
                                }">
                                    {{ student.is_scholarship ? 'Scholarship' : 'Not Scholarship' }}
                                </span>
                            </td>
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <label class="mx-2 relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="student.is_scholarship"
                                        @change="updateAndSubmit('is_scholarship', student.is_scholarship)"
                                        class="sr-only peer"/>

                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                        :class="{
                                            'bg-green-500': student.is_scholarship,
                                            'bg-red-500': !student.is_scholarship
                                        }"></div>

                                    <div
                                        class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                        :class="{ 'translate-x-full': student.is_scholarship }"></div>
                                </label>
                            </td>
                        </tr>
                        <tr :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'">
                            <td class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                8
                            </td>

                            <td class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">

                                Scholarship Approval
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                {{ student.scholarship_approved_by_name }}
                            </td>
                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <span class="text-lg mx-4" :class="{
                                    'text-green-500 dark:text-green-400': student.is_scholarship_approved,
                                    'text-red-500 dark:text-red-400': !student.is_scholarship_approved
                                }">
                                    {{ student.is_scholarship_approved ? 'Approved' : 'Not Approved' }}
                                </span>
                            </td>
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <label class="mx-2 relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="student.is_scholarship_approved"
                                        @change="updateAndSubmit('is_scholarship_approved', student.is_scholarship_approved)"
                                        class="sr-only peer"/>

                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                        :class="{
                                            'bg-green-500': student.is_scholarship_approved,
                                            'bg-red-500': !student.is_scholarship_approved
                                        }"></div>

                                    <div
                                        class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                        :class="{ 'translate-x-full': student.is_scholarship_approved }"></div>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                9
                            </td>

                            <td class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">

                                Scholarship Verification
                            </td>

                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                {{ student.scholarship_verified_by_name }}
                            </td>
                            <td class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <span class="text-lg mx-4" :class="{
                                    'text-green-500 dark:text-green-400': student.is_scholarship_verified,
                                    'text-red-500 dark:text-red-400': !student.is_scholarship_verified
                                }">
                                    {{ student.is_scholarship_verified ? 'Verified' : 'Not Verified' }}
                                </span>
                            </td>
                            <td
                                class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                <label class="mx-2 relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="student.is_scholarship_verified"
                                        @change="updateAndSubmit('is_scholarship_verified', student.is_scholarship_verified)"
                                        class="sr-only peer"/>

                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                        :class="{
                                            'bg-green-500': student.is_scholarship_verified,
                                            'bg-red-500': !student.is_scholarship_verified
                                        }"></div>

                                    <div
                                        class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                        :class="{ 'translate-x-full': student.is_scholarship_verified
                                        }"></div>
                                </label>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                    
                </div>
                
            </div> <!-- Closing the div for Registrations list -->
        </div>
    </div>

</template>