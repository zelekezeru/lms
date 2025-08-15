<script setup>
import App from "@/App.vue";
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
  centers: Array,
  rows: Array,
});

const downloadPDF = () => {
  window.location.href = "/download-distance-report-pdf";
};
</script>

<style scoped>
table {
  border-collapse: collapse;
}
th, td {
  border: 1px solid #ccc;
}
.dark th, .dark td {
  border-color: #444;
}
</style>

<template>
    <AppLayout>
    <div class="p-6 dark:bg-gray-900 dark:text-gray-100 min-h-screen">
        <h1 class="text-2xl font-bold mb-4 dark:text-white">Distance Report</h1>

        <button
            @click="downloadPDF"
            class="mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
        >
        Download PDF
        </button>

        <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
                <th class="border px-2 py-1 dark:bg-gray-800 dark:text-gray-100">No</th>
                <th class="border px-2 py-1 dark:bg-gray-800 dark:text-gray-100">Course Name</th>
                <th class="border px-2 py-1 dark:bg-gray-800 dark:text-gray-100">Total Students</th>
                <th
                    v-for="center in centers"
                    :key="center.id"
                    class="border px-2 py-1 dark:bg-gray-800 dark:text-gray-100"
                >
                Center - {{ center.name }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(row, idx) in rows" :key="row.no" class="text-center">
                <td class="border px-2 py-1 dark:text-gray-100">{{ idx + 1 }}</td>
                <td class="border px-2 py-1 dark:text-gray-100">{{ row.name }}</td>
                <td class="border px-2 py-1 dark:text-gray-100">{{ row.total }}</td>
                <td
                    v-for="center in centers"
                    :key="center.id"
                    class="border px-2 py-1 dark:text-gray-100"
                >
                {{ row['center_' + center.id] ?? 0 }}
                </td>
            </tr>
            </tbody>
        </table>
        </div>
    </div>
    </AppLayout>
</template>

