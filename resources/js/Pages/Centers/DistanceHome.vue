
<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { EyeIcon, PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/solid";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    centers: {
        type: Array,
        required: true,
    },
    totalStudents: {
        type: Number,
        required: true,
    },
    totalCoordinators: {
        type: Number,
        required: true,
    },
});

function confirmDelete(center) {
    Swal.fire({
        title: "Delete Center?",
        text: `Are you sure you want to delete "${center.name}"?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, delete it!",
        background: document.documentElement.classList.contains('dark') ? "#1f2937" : "#fff",
        color: document.documentElement.classList.contains('dark') ? "#f3f4f6" : "#111827",
    }).then((result) => {
        if (result.isConfirmed) {
            // Replace with your delete logic
            Swal.fire("Deleted!", "The center has been deleted.", "success");
        }
    });
}
</script>

<template>
    
    <AppLayout>
        <div class="container mx-auto py-10 px-4 flex flex-col items-center">
            
            <div class="flex items-center justify-center mb-12">
                <h2
                    class="text-3xl md:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 via-blue-500 to-blue-400 drop-shadow-lg text-center px-8 py-4 rounded-xl border-b-4 border-blue-400 shadow-xl"
                >
                    <span class="inline-block align-middle mr-3 animate-bounce">
                        <svg
                            class="w-8 h-8 text-blue-500"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16 17l-4 4m0 0l-4-4m4 4V3"
                            />
                        </svg>
                    </span>
                    Distance Learning Programs
                    <span
                        class="inline-block align-middle ml-3 animate-bounce animation-delay-200"
                    >
                        <svg
                            class="w-8 h-8 text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8 7l4-4m0 0l4 4m-4-4v18"
                            />
                        </svg>
                    </span>
                </h2>
            </div>
        </div>
        <!-- Section Details -->
        <div
            class="border-t border-b border-gray-300 dark:border-gray-600 pt-6 pb-6 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg"
        >
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-6 text-gray-800 dark:text-gray-200"
            >
                    <Link
                        :href="route('centers.index')"
                        class="flex flex-col items-start bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-blue-100 dark:border-gray-700 
                                bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-500 rounded-xl shadow-lg p-6 flex flex-col items-center justify-between transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    >
                        <h2 class="text-lg font-extrabold text-white mb-1 flex items-center gap-2 justify-center">
                            <EyeIcon class="w-5 h-5 text-white opacity-80" />
                            Available Centers
                        </h2>
                        <p class="text-sm text-indigo-100 mb-2 text-center">
                            <span class="font-semibold text-white text-xl">{{ centers.length }} - </span>
                            <span class="ml-1"> Center{{ centers.length === 1 ? '' : 's' }} Available</span>
                        </p>
                    </Link>
                <div
                    class=""
                >
                    <Link
                        :href="route('students.index', { study_mode: DISTANCE })"
                        class="flex flex-col items-start bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-blue-100 dark:border-gray-700 
                                bg-gradient-to-br from-green-400 via-teal-400 to-blue-400 rounded-xl shadow-lg p-6 flex flex-col items-center justify-between transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400"
                    >
                        <h2 class="text-lg font-extrabold text-white mb-1 flex items-center gap-2 justify-center">
                            <EyeIcon class="w-5 h-5 text-white opacity-80" />
                            All Distance Students
                        </h2>
                        <span class="text-sm text-teal-100 mb-2 text-center">
                            <span class="font-semibold text-white text-xl">{{ totalStudents }} - </span>
                            <span class="ml-1"> Student{{ totalStudents === 1 ? '' : 's' }} Total</span>
                        </span>
                    </Link>
                </div>
                <div
                    class=""
                >
                    <Link
                        :href="route('coordinators.index')"
                        class="flex flex-col items-start bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-blue-100 dark:border-gray-700 
                                bg-gradient-to-br from-pink-500 via-red-400 to-yellow-300 rounded-xl shadow-lg p-6 flex flex-col items-center justify-between transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-pink-400"
                    >
                        <h2 class="text-lg font-extrabold text-white mb-1 flex items-center gap-2 justify-center">
                            <EyeIcon class="w-5 h-5 text-white opacity-80" />
                            Distance Coordinators
                        </h2>
                        <span class="text-sm text-teal-100 mb-2 text-center">
                            <span class="font-semibold text-white text-xl">{{ totalCoordinators }} - </span>
                            <span class="ml-1"> Coordinator{{ totalCoordinators === 1 ? '' : 's' }} Total</span>
                        </span>
                    </Link>
                </div>
            </div>
            <div
                class="grid grid-cols-1 md:grid-cols-3 my-4 gap-6 text-gray-800 dark:text-gray-200"
            >
                <div class="flex flex-col items-start bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-blue-100 dark:border-gray-700 
                        bg-gradient-to-br from-blue-400 via-blue-300 to-blue-200 rounded-xl shadow-lg p-6 flex flex-col items-center justify-between transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <h2 class="text-lg font-extrabold text-gray-900 dark:text-gray-100 mb-1 flex items-center gap-2 justify-center">
                        <PencilSquareIcon class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                        Generate Overview Report
                    </h2>
                    <Link :href="route('downloadDistanceReportPDF')" class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-500">
                        Generate Report
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

