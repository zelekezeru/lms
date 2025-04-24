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

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

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
    
    <!-- student Image -->
    <div class="flex justify-center mb-8">
        <div
            v-if="!imageLoaded"
            class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
        ></div>

        <img
            v-show="imageLoaded"
            class="rounded-full w-44 h-44 object-contain bg-gray-400"
            :src="student.profileImg"
            :alt="`Logo of ` + student.name"
            @load="handleImageLoad"
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
            {{ student.id_no }}
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

</template>
