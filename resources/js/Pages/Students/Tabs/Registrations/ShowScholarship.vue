<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import {} from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { AcademicCapIcon, CheckIcon, ClockIcon, CogIcon, XCircleIcon } from "@heroicons/vue/24/outline";

// Initial data
const props = defineProps({
    student: Object,
    status: Object,
});

// Clone and track the status
const student = ref({ ...props.student });

// Use Inertia's useForm to manage submission

// Submit updated status
function submitStatusChange(field, value) {
    // Prepare the data to send.  Include the student's ID and the specific field being updated.
    const postData = {
        studentId: student.value.id, // Explicitly include student ID
        [field]: value, // e.g., is_active: true
    };

    router.post(
        route("students.verify", { student: student.value.id }), // Use the named route, include student ID as route parameter.
        postData, // Send the data object
        {
            onSuccess: () => {
                Swal.fire(
                    "Validated!",
                    "Student Validation updated successfully.",
                    "success"
                );
            },
            onError: (errors) => {
                Swal.fire(
                    "Failed!",
                    Object.values(usePage().props.errors).join("\n") ??
                        "An unexpected error occurred while updating the student validation.",
                    "error"
                );
            },
        }
    );
}

const updateAndSubmit = (field) => {
    student.value = {
        ...student.value,
        [field]: student.value[field] === 1 ? 0 : 1, // Toggle the value
    };
    submitStatusChange(field, student.value[field]);
};

const status = ref({ ...props.status });

// Request scholarship
function requestScholarship() {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to request a scholarship for this student.",
        icon: "warning",
        input: "textarea",
        inputLabel: "Reason for Scholarship Request",
        inputPlaceholder: "Enter the reason for requesting the scholarship...",
        inputAttributes: {
            required: true,
            maxlength: 500,
        },
        inputValidator: (value) => {
            if (!value) {
                return "Reason is required!";
            }
        },
        showCancelButton: true,
        confirmButtonText: "Yes, request it!",
        cancelButtonText: "No, keep it",
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            router.post(
                route('students.request-scholarship', { student: student.value.id }),
                { scholarship_reason: result.value }
            );
        }
    });
}

function approveScholarship() {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to approve the scholarship request.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, approve it!",
        cancelButtonText: "No, keep it",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('students.approve-scholarship', { student: student.value.id }));
        }
    });
}

// Decline scholarship
function rejectScholarship() {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to decline the scholarship request.",
        icon: "warning",
        input: "textarea",
        inputLabel: "Reason for Rejection",
        inputPlaceholder: "Enter the reason for rejecting the scholarship...",
        inputAttributes: {
            required: true,
            maxlength: 500,
        },
        inputValidator: (value) => {
            if (!value) {
                return "Reason is required!";
            }
        },
        showCancelButton: true,
        confirmButtonText: "Yes, decline it!",
        cancelButtonText: "No, keep it",
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            router.post(
                route('students.reject-scholarship', { student: student.value.id }),
                { scholarship_rejected_reason: result.value }
            );
        }
    });
}

</script>

<template>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Scholarship Details
        </h2>
    </div>
    
    <div
        class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-6 pb-6 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg"
    >
            <!-- The cards go here (move the 3 card divs inside this grid) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-gray-800 dark:text-gray-200">
            <div v-show="userCanAny(['view-scholarships'])"

                class="flex flex-col items-center bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-blue-100 dark:border-gray-700 w-full h-full">
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1 text-center">Student Scholarship Status</span>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold justify-center w-full"
                    :class="status.is_scholarship
                        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 border border-green-300 dark:border-green-700'
                        : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 border border-red-300 dark:border-red-700'"
                >
                    <svg v-if="status.is_scholarship" class="w-4 h-4 mr-1 text-green-500 dark:text-green-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    
                    <svg v-else class="w-4 h-4 mr-1 text-red-500 dark:text-red-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    {{ status.is_scholarship ? 'Scholarship Student' : 'Not a Scholarship Student' }}
                </span>
                <span v-if="status.scholarship_requested_by_name" class="flex items-center justify-center mt-2">
                    <div class="flex flex-col gap-2 my-4 w-full">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gradient-to-r text-white font-bold shadow-lg border-2 border-green-200 dark:border-green-400">
                            <AcademicCapIcon class="w-4 h-4" />
                            Requested by:
                            <span class="ml-1">
                                {{ status.scholarship_requested_by_name || 'N/A' }}
                            </span>
                        </span>
                    </div>
                </span>
            </div>
            <div v-show="userCanAny(['request-scholarship', 'approve-scholarship'])"
                    class="flex flex-col items-center justify-center bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-blue-100 dark:border-gray-700 w-full h-full">
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-3 text-center">Scholarship Request</span>
                <template v-if="status.is_scholarship_requested">
                    <span class="text-sm text-green-500 dark:text-green-400">Scholarship request already submitted.</span>
                    <div class="w-full mt-2 p-3 bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-400 rounded">
                        <span class="font-semibold text-yellow-800 dark:text-yellow-200">Reason:</span>
                        <span v-if="status.scholarship_reason" class="text-sm text-yellow-700 dark:text-yellow-100 ml-2">
                            {{ status.scholarship_reason }}
                        </span>
                    </div>
                </template>
                <template v-else>
                    <button
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all duration-200"
                        @click="requestScholarship"
                    >
                        <AcademicCapIcon class="w-5 h-5" />
                        <span>Request Scholarship</span>
                    </button>
                </template>
            </div>
            <div v-show="userCanAny(['approve-scholarship'])"
                v-if="status.is_scholarship_requested"
                class="flex flex-col items-center bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-blue-100 dark:border-gray-700">
                <span class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1 text-center">Scholarship Approval</span>
                <div class="flex flex-col w-full gap-2">
                    <div  v-if="status.is_scholarship_approved">
                        <button v-if="!status.is_scholarship_rejected"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 via-red-600 to-red-700 text-white font-semibold rounded-lg shadow-lg hover:from-red-600 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-200"
                            @click="rejectScholarship"
                        >
                            <CogIcon class="w-5 h-5" />
                            <span>Reject Scholarship</span>
                        </button>
                        
                        <span v-if="status.is_scholarship_approved" class="text-sm text-green-500 dark:text-green-400">
                            <span class="text-sm text-green-500 dark:text-green-400">
                                <CheckIcon class="w-5 h-5 inline" /> Approved by: {{ status.scholarship_approved_by_name }}
                                <br>
                                <ClockIcon class="w-5 h-5 inline" /> Approved on: {{ new Date(status.scholarship_approved_at).toLocaleDateString() }}
                            </span>
                        </span>
                    </div>
                    <div v-else>
                        <button
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-500 via-green-600 to-green-700 text-white font-semibold rounded-lg shadow-lg hover:from-green-600 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition-all duration-200"
                            @click="approveScholarship"
                        >
                            <AcademicCapIcon class="w-5 h-5" />
                            <span>Approve Scholarship</span>
                        </button>
                        <span v-if="status.is_scholarship_rejected" class="text-sm text-red-500 dark:text-red-400">
                            <span class="text-sm text-green-500 dark:text-green-400">
                                <span class="text-sm text-red-500 dark:text-red-400">
                                    <XCircleIcon class="w-5 h-5 inline" /> Rejected by: {{ status.scholarship_rejected_by_name }}
                                    <br>
                                    <ClockIcon class="w-5 h-5 inline" /> Rejected on: {{ new Date(status.scholarship_rejected_at).toLocaleDateString() }}
                                </span>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</template>