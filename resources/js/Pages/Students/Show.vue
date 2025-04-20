<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
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

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});

// Multi nav header options
const selectedTab = ref('details');


const tabs = [
    { key: 'details', label: 'Details', icon: CogIcon },
    { key: 'academics', label: 'Academics', icon: BookOpenIcon },
    { key: 'courses', label: 'Courses', icon: AcademicCapIcon },
    { key: 'registrations', label: 'Registration', icon: UsersIcon },
    { key: 'church', label: 'Church', icon: HomeModernIcon },
];

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
                {{ student.student_name }}
                {{ student.father_name }}
            </h1>

            <nav
                class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    :class="[
                        'flex items-center px-4 py-2 space-x-2 text-sm font-medium transition',
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
                    ]"
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            <div
                class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
            >

                <!-- Details Panel -->
                <div v-show="selectedTab === 'details'" >

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
                    
                    <div class="grid grid-cols-2 gap-4">
                    
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Student ID</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.profileImg }}
                        </span>
                    </div>
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
                    <div v-if="student.user.email" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Email</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.user.email }}
                        </span>
                    </div>
                    <div v-if="student.mobile_phone" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Mobile Phone</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.mobile_phone }}
                        </span>
                    </div> <!-- Closing the div for Mobile Phone -->
                    <div v-if="student.office_phone" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Office Phone</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.office_phone }}
                        </span>
                    </div> <!-- Closing the div for Office Phone -->
                    <div v-if="student.date_of_birth" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Date of Birth</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.date_of_birth }}
                        </span>
                    </div> <!-- Closing the div for Date of Birth -->
                    
                    <!-- Marital Status -->
                    <div v-if="student.marital_status" class="flex flex-col">
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
                    <div v-if="student.sex" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Sex</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.sex }}
                        </span>
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
                </div> <!-- Closing the div for Details Panel -->

                <!-- Registration Button -->

                <!-- Academic Panel -->
                <div v-show="selectedTab === 'academics'">
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
                </div> <!-- Closing the div for Academic Panel -->

                <!-- Enrolled courses list Pannel -->
                <div v-show="selectedTab === 'courses'">     
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Course Enrolments
                        </h2>
                        
                            
                        <div class="flex justify-center mb-6">
                            <button
                                @click="router.visit(route('students.profile', { student: student.id }))"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Assign to Courses
                            </button>
                        </div> 
                    </div>

                    <div class="overflow-x-auto">
                        <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">           
                    
                            <div class="grid grid-cols-2 gap-4">

                                <div v-if="student.courses" class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >Enrolled Courses</span
                                    >
                                    <ul class="list-disc pl-5">
                                        <li
                                            v-for="course in student.courses"
                                            :key="course.id"
                                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {{ course.name }}
                                        </li>
                                    </ul>
                                </div>
                                <div v-else class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >No Courses Enrolled</span
                                    >
                                    <span
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ student.name }}
                                        has not enrolled in any courses yet.
                                    </span>
                                </div>
                            </div> <!-- Closing the div for Enrolled courses list -->
                        </div>  
                    </div>
                </div> <!-- Closing the div for Enrolled courses list Pannel -->

                <!-- Registrations Panel -->
                <div v-show="selectedTab === 'registrations'">
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Registration Details
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
                            <div class="grid grid-cols-2 gap-4">

                                <div v-if="student.registrations" class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >Registrations</span
                                    >
                                    <ul class="list-disc pl-5">
                                        <li
                                            v-for="registration in student.registrations"
                                            :key="registration.id"
                                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {{ registration.name }}
                                        </li>
                                    </ul>
                                </div>
                                <div v-else class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >No Registrations</span
                                    >
                                    <span
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ student.name }}
                                        has not registered for any events yet.
                                    </span>
                                </div>  <!-- Closing the div for No Registrations --> 
                            </div> <!-- Closing the div for Registrations list -->
                        </div>
                    </div>
                </div> <!-- Closing the div for Registrations Panel -->

                <!-- Church Details Panel -->
                <div v-show="selectedTab === 'church'">
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Church Information
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div v-if="student.church_name" class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >Church</span
                                    >
                                    <span
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ student.church_name }}
                                    </span>
                                </div>
                                <div v-if="student.church_address" class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >Address</span
                                    >
                                    <span
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ student.church_address }}
                                    </span>
                                </div> <!-- Closing the div for Church Address -->

                                <div v-if="student.pastor_name" class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >Pastor's Name</span
                                    >
                                    <span
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ student.pastor_name }}
                                    </span>
                                </div> <!-- Closing the div for Pastor's Name -->
                                <div v-if="student.pastor_phone" class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >Pastor's Phone</span
                                    >
                                    <span
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ student.pastor_phone }}
                                    </span>
                                </div> <!-- Closing the div for Pastor's Phone -->
                            </div>
                        </div>
                    </div>
                </div> <!-- Closing the div for Church Details Panel -->

            </div>
        </div>
    </AppLayout>
</template>
