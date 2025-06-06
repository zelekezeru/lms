<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ArrowPathIcon } from "@heroicons/vue/24/solid";
import { ref } from "vue";
import StudentList from "./List.vue";

defineProps({
    students: {
        type: Object,
        required: true,
    },
    sortInfo: {
        type: Object,
        required: false,
    },
});

const refreshing = ref(false);

// Refresh function
const refreshData = () => {
    refreshing.value = true;
    router.visit(route("students.index"), {
        only: ["students"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

const search = ref(usePage().props.search || "");
// Search function
const searchstudents = () => {
    router.get(
        route("students.index"),
        { ...route().params, search: search.value },
        { preserveState: true }
    );
};

// Delete function with SweetAlert confirmation
const deletestudent = (id) => {
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
    <AppLayout>
        <h1
            class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
        >
            Student Details
        </h1>

        <!-- Search Bar and Button Section -->
        <div class="flex justify-between items-center mb-3">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg
                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-4.35-4.35M9 17A8 8 0 109 1a8 8 0 000 16z"
                        />
                    </svg>
                </span>
                <input
                    type="text"
                    v-model="search"
                    placeholder="Search students..."
                    class="pl-10 p-2 border rounded-lg text-gray-900 dark:text-white dark:bg-gray-700"
                    @input="searchstudents"
                />
            </div>

            <div class="flex space-x-6">
                <Link
                    v-if="userCan('create-students')"
                    :href="route('students.create')"
                    class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    + Add Student
                </Link>

                <button
                    @click="refreshData"
                    class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    title="Refresh Data"
                >
                    <ArrowPathIcon
                        class="w-5 h-5 mr-2"
                        :class="{ 'animate-spin': refreshing }"
                    />
                    Refresh Data
                </button>
            </div>
        </div>

        <!-- Student List Component -->
        <StudentList
            :students="students"
            :sort-info="sortInfo"
            :deletestudent="deleteStudent"
            :search="search"
        />
    </AppLayout>
</template>
