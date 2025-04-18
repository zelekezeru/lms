<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    program: {
        type: Object,
        required: true,
    },
    department: {
        type: Object,
        required: true,
    },
    year: {
        type: Object,
        required: true,
    },
    semester: {
        type: Object,
        required: true,
    },
    semester: {
        type: Object,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
});

const imageLoaded = ref(false);

const deleteStudent = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("students.destroy", { student: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The student has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Student Details" />
    <AppLayout>
        <div class="max-w-8xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                Student Details
            </h1>
            <div
                class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
            >
                <!-- Profile Image -->
                <div class="flex justify-center mb-8">
                    <div
                        v-if="!imageLoaded"
                        class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                    ></div>
                    <img
                        v-show="imageLoaded"
                        class="rounded-full w-44 h-44 object-contain bg-gray-400"
                        :src="student.profileImg"
                        :alt="`profile image of ` + student.name"
                        @load="imageLoaded = true"
                    />
                </div>

                <!-- Student Profile route Button-->
                <div class="flex justify-center mb-6">
                    <Link
                        :href="
                            route('students.profile', { student: student.id })
                        "
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Complete Registration
                    </Link>
                </div>

                <!-- Personal Details Grid -->
                <div class="grid sm:grid-cols-2 gap-4">
                    <!-- Student ID -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            ID Number</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.id_no }}
                        </span>
                    </div>
                    <!-- Full Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Full Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.student_name }}
                            {{ student.father_name }}
                            {{ student.grand_father_name }}
                        </span>
                    </div>
                    <!-- Email -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Email</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.user.email }}
                        </span>
                    </div>
                    <!-- Mobile Phone -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Mobile Phone</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.mobile_phone }}
                        </span>
                    </div>
                    <!-- Office Phone -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Office Phone</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.office_phone }}
                        </span>
                    </div>
                    <!-- Date of Birth -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Date of Birth</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.date_of_birth }}
                        </span>
                    </div>
                    <!-- Marital Status -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Marital Status</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.marital_status }}
                        </span>
                    </div>
                    <!-- Sex -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Sex</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.sex }}
                        </span>
                    </div>
                </div>

                <!-- Academic & Church Details Grid -->
                <div class="grid sm:grid-cols-2 gap-4 mt-6">
                    <!-- Academic Year -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Academic Year</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ year.name }}
                        </span>
                    </div>
                    <!-- Semester -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Semester</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.name }}
                        </span>
                    </div>
                    <!-- Program -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Program</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.name }}
                        </span>
                    </div>
                    <!-- Department -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Department</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.name }}
                        </span>
                    </div>
                    <!-- Pastor's Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Pastor's Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.pastor_name }}
                        </span>
                    </div>
                    <!-- Pastor's Phone -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Pastor's Phone</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.pastor_phone }}
                        </span>
                    </div>
                    <!-- Address -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Address</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.address }}
                        </span>
                    </div>
                    <!-- Position/Denomination -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Position/Denomination</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.position_denomination }}
                        </span>
                    </div>
                    <!-- Church Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Church Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.church_name }}
                        </span>
                    </div>
                    <!-- Church Address -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Church Address</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.church_address }}
                        </span>
                    </div>
                    <!-- Office Use Notes (Full Width) -->
                    <div class="flex flex-col sm:col-span-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Office Use Notes</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.office_use_notes }}
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <!-- Edit Button -->
                    <div v-if="userCan('update-students')">
                        <Link
                            :href="
                                route('students.edit', {
                                    student: student.id,
                                })
                            "
                            class="flex items-center space-x-1 text-blue-500 hover:text-blue-700"
                        >
                            <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                        </Link>
                    </div>
                    <!-- Delete Button -->
                    <div v-if="userCan('delete-students')">
                        <button
                            @click="deleteStudent(student.id)"
                            class="flex items-center space-x-1 text-red-500 hover:text-red-700"
                        >
                            <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
