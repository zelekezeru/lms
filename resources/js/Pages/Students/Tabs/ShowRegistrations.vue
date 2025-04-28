<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import {} from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

const showVerifyModal = ref(false);

// Initial data
const props = defineProps({
    student: Object,
    updateRoute: String,
    status: Object,
});

// Array list of Status elaments
// const $statuses = [
//            'Status'=> 'active',
//            'Approval'=> 'verified',
//            'Document_Submittion' => 'completed',
//             '''verified',
//             'enrolled',
//             'graduated',
//             'scholarship',
//             'scholarship_approved',
//             'scholarship_verified'
//         ];

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
</script>

<template>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Registration Details
        </h2>
    </div>

    <div class="overflow-x-auto">
        <div
            class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
        >
            <div class="grid gap-4">
                <div class="flex flex-col">
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
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    1
                                </td>

                                <td
                                    class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Status
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ student.status.created_by_name }}
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <span
                                        class="text-lg mx-4"
                                        :class="{
                                            'text-green-500 dark:text-green-400':
                                                student.status.is_active,
                                            'text-red-500 dark:text-red-400':
                                                !student.status.is_active,
                                        }"
                                    >
                                        {{
                                            student.status.is_active
                                                ? "Active"
                                                : "Inactive"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <label
                                        class="mx-2 relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="student.status.is_active"
                                            @change="updateAndSubmit('is_active')"
                                            class="sr-only peer"
                                        />

                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                            :class="{
                                                'bg-green-500':
                                                    student.status.is_active,
                                                'bg-red-500':
                                                    !student.status.is_active,
                                            }"
                                        ></div>

                                        <div
                                            class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                            :class="{
                                                'translate-x-full':
                                                    student.status.is_active,
                                            }"
                                        ></div>
                                    </label>
                                </td>
                            </tr>
                            <tr
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                            >
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    2
                                </td>

                                <td
                                    class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Approval
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ status.approved_by_name }}
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <span
                                        class="text-lg mx-4"
                                        :class="{
                                            'text-green-500 dark:text-green-400':
                                                status.is_approved,
                                            'text-red-500 dark:text-red-400':
                                                !status.is_approved,
                                        }"
                                    >
                                        {{
                                            status.is_approved
                                                ? "Approved"
                                                : "Not Approved"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <label
                                        class="mx-2 relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="status.is_approved"
                                            @change="
                                                updateAndSubmit(
                                                    'is_approved',
                                                    status.is_approved
                                                )
                                            "
                                            class="sr-only peer"
                                        />

                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                            :class="{
                                                'bg-green-500':
                                                    status.is_approved,
                                                'bg-red-500':
                                                    !status.is_approved,
                                            }"
                                        ></div>

                                        <div
                                            class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                            :class="{
                                                'translate-x-full':
                                                    status.is_approved,
                                            }"
                                        ></div>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    3
                                </td>

                                <td
                                    class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Completion
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ status.completed_by_name }}
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <span
                                        class="text-lg mx-4"
                                        :class="{
                                            'text-green-500 dark:text-green-400':
                                                status.is_completed,
                                            'text-red-500 dark:text-red-400':
                                                !status.is_completed,
                                        }"
                                    >
                                        {{
                                            status.is_completed
                                                ? "Completed"
                                                : "Not Completed"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <label
                                        class="mx-2 relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="
                                                status.is_completed
                                            "
                                            @change="
                                                updateAndSubmit(
                                                    'is_completed',
                                                    status.is_completed
                                                )
                                            "
                                            class="sr-only peer"
                                        />

                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                            :class="{
                                                'bg-green-500':
                                                    status.is_completed,
                                                'bg-red-500':
                                                    !status
                                                        .is_completed,
                                            }"
                                        ></div>

                                        <div
                                            class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                            :class="{
                                                'translate-x-full':
                                                    status.is_completed,
                                            }"
                                        ></div>
                                    </label>
                                </td>
                            </tr>
                            <tr
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                            >
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    4
                                </td>

                                <td
                                    class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Verification
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ status.verified_by_name }}
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <span
                                        class="text-lg mx-4"
                                        :class="{
                                            'text-green-500 dark:text-green-400':
                                                status.is_verified,
                                            'text-red-500 dark:text-red-400':
                                                !status.is_verified,
                                        }"
                                    >
                                        {{
                                            status.is_verified
                                                ? "Verified"
                                                : "Not Verified"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <label
                                        class="mx-2 relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="status.is_verified"
                                            @change="
                                                updateAndSubmit(
                                                    'is_verified',
                                                    status.is_verified
                                                )
                                            "
                                            class="sr-only peer"
                                        />

                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                            :class="{
                                                'bg-green-500':
                                                    status.is_verified,
                                                'bg-red-500':
                                                    !status.is_verified,
                                            }"
                                        ></div>

                                        <div
                                            class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                            :class="{
                                                'translate-x-full':
                                                    status.is_verified,
                                            }"
                                        ></div>
                                    </label>
                                </td>
                            </tr>
                            <!-- Toggle tr for Enrollment -->
                            <tr>
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    5
                                </td>

                                <td
                                    class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Enrollment
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ status.enrolled_by_name }}
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <span
                                        class="text-lg mx-4"
                                        :class="{
                                            'text-green-500 dark:text-green-400':
                                                status.is_enrolled,
                                            'text-red-500 dark:text-red-400':
                                                !status.is_enrolled,
                                        }"
                                    >
                                        {{
                                            status.is_enrolled
                                                ? "Enrolled"
                                                : "Not Enrolled"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <label
                                        class="mx-2 relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="status.is_enrolled"
                                            @change="
                                                updateAndSubmit(
                                                    'is_enrolled',
                                                    status.is_enrolled
                                                )
                                            "
                                            class="sr-only peer"
                                        />

                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                            :class="{
                                                'bg-green-500':
                                                    status.is_enrolled,
                                                'bg-red-500':
                                                    !status.is_enrolled,
                                            }"
                                        ></div>

                                        <div
                                            class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                            :class="{
                                                'translate-x-full':
                                                    status.is_enrolled,
                                            }"
                                        ></div>
                                    </label>
                                </td>
                            </tr>
                            <tr
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                            >
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    6
                                </td>

                                <td
                                    class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Graduation
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ status.graduated_by_name }}
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <span
                                        class="text-lg mx-4"
                                        :class="{
                                            'text-green-500 dark:text-green-400':
                                                status.is_graduated,
                                            'text-red-500 dark:text-red-400':
                                                !status.is_graduated,
                                        }"
                                    >
                                        {{
                                            status.is_graduated
                                                ? "Graduated"
                                                : "Not Graduated"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <label
                                        class="mx-2 relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="
                                                status.is_graduated
                                            "
                                            @change="
                                                updateAndSubmit(
                                                    'is_graduated',
                                                    status.is_graduated
                                                )
                                            "
                                            class="sr-only peer"
                                        />

                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                            :class="{
                                                'bg-green-500':
                                                    status.is_graduated,
                                                'bg-red-500':
                                                    !status
                                                        .is_graduated,
                                            }"
                                        ></div>

                                        <div
                                            class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                            :class="{
                                                'translate-x-full':
                                                    status.is_graduated,
                                            }"
                                        ></div>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    7
                                </td>

                                <td
                                    class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Scholarship
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ status.scholarship_by_name }}
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <span
                                        class="text-lg mx-4"
                                        :class="{
                                            'text-green-500 dark:text-green-400':
                                                status.is_scholarship,
                                            'text-red-500 dark:text-red-400':
                                                !status.is_scholarship,
                                        }"
                                    >
                                        {{
                                            status.is_scholarship
                                                ? "Scholarship"
                                                : "Not Scholarship"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <label
                                        class="mx-2 relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="
                                                status.is_scholarship
                                            "
                                            @change="
                                                updateAndSubmit(
                                                    'is_scholarship',
                                                    status
                                                        .is_scholarship
                                                )
                                            "
                                            class="sr-only peer"
                                        />

                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                            :class="{
                                                'bg-green-500':
                                                    status
                                                        .is_scholarship,
                                                'bg-red-500':
                                                    !status
                                                        .is_scholarship,
                                            }"
                                        ></div>

                                        <div
                                            class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                            :class="{
                                                'translate-x-full':
                                                    status
                                                        .is_scholarship,
                                            }"
                                        ></div>
                                    </label>
                                </td>
                            </tr>
                            <tr
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                            >
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    8
                                </td>

                                <td
                                    class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Scholarship Approval
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{
                                        status
                                            .scholarship_approved_by_name
                                    }}
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <span
                                        class="text-lg mx-4"
                                        :class="{
                                            'text-green-500 dark:text-green-400':
                                                status
                                                    .is_scholarship_approved,
                                            'text-red-500 dark:text-red-400':
                                                !status
                                                    .is_scholarship_approved,
                                        }"
                                    >
                                        {{
                                            status
                                                .is_scholarship_approved
                                                ? "Approved"
                                                : "Not Approved"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <label
                                        class="mx-2 relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="
                                                status
                                                    .is_scholarship_approved
                                            "
                                            @change="
                                                updateAndSubmit(
                                                    'is_scholarship_approved',
                                                    status
                                                        .is_scholarship_approved
                                                )
                                            "
                                            class="sr-only peer"
                                        />

                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                            :class="{
                                                'bg-green-500':
                                                    status
                                                        .is_scholarship_approved,
                                                'bg-red-500':
                                                    !status
                                                        .is_scholarship_approved,
                                            }"
                                        ></div>

                                        <div
                                            class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                            :class="{
                                                'translate-x-full':
                                                    status
                                                        .is_scholarship_approved,
                                            }"
                                        ></div>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    9
                                </td>

                                <td
                                    class="w-80 px-4 py-2 text-lg text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Scholarship Verification
                                </td>

                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{
                                        status
                                            .scholarship_verified_by_name
                                    }}
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <span
                                        class="text-lg mx-4"
                                        :class="{
                                            'text-green-500 dark:text-green-400':
                                                status
                                                    .is_scholarship_verified,
                                            'text-red-500 dark:text-red-400':
                                                !status
                                                    .is_scholarship_verified,
                                        }"
                                    >
                                        {{
                                            status
                                                .is_scholarship_verified
                                                ? "Verified"
                                                : "Not Verified"
                                        }}
                                    </span>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 text-center dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <label
                                        class="mx-2 relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="
                                                status
                                                    .is_scholarship_verified
                                            "
                                            @change="
                                                updateAndSubmit(
                                                    'is_scholarship_verified',
                                                    status
                                                        .is_scholarship_verified
                                                )
                                            "
                                            class="sr-only peer"
                                        />

                                        <div
                                            class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200"
                                            :class="{
                                                'bg-green-500':
                                                    status
                                                        .is_scholarship_verified,
                                                'bg-red-500':
                                                    !status
                                                        .is_scholarship_verified,
                                            }"
                                        ></div>

                                        <div
                                            class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-200 transform"
                                            :class="{
                                                'translate-x-full':
                                                    status
                                                        .is_scholarship_verified,
                                            }"
                                        ></div>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Closing the div for Registrations list -->
        </div>
    </div>
</template>
