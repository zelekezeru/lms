
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
            <div class="flex justify-center w-full">
                <h1
                    class="text-4xl font-extrabold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 drop-shadow-lg tracking-tight text-center w-full"
                    role="heading"
                    aria-level="1"
                >
                    Distance Learning Programs
                </h1>
            </div>

            <div class="flex justify-center w-full">
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 justify-items-center w-full">
                    <Link
                        :href="route('centers.index')"
                        class="bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-500 rounded-xl shadow-lg p-6 flex flex-col items-center justify-between transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400"
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
                    
                    <Link
                        :href="route('students.index', { study_mode: DISTANCE })"
                        class="bg-gradient-to-br from-green-400 via-teal-400 to-blue-400 rounded-xl shadow-lg p-6 flex flex-col items-center justify-between transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400"
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
            </div>

        </div>
    </AppLayout>
</template>

