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
    totalCourses: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Distance Learning Report</h1>
            <div class="mb-4">
                <Link :href="route('downloadDistanceReportPDF')" class="btn btn-primary">
                    Download PDF
                </Link>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Center</th>
                            <th class="py-2 px-4 border-b">Total Students</th>
                            <th class="py-2 px-4 border-b">Total Coordinators</th>
                            <th class="py-2 px-4 border-b">Total Courses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="center in centers" :key="center.id">
                            <td class="py-2 px-4 border-b">{{ center.name }}</td>
                            <td class="py-2 px-4 border-b">{{ center.total_students }}</td>
                            <td class="py-2 px-4 border-b">{{ center.total_coordinators }}</td>
                            <td class="py-2 px-4 border-b">{{ center.total_courses }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>