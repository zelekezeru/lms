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
</template>