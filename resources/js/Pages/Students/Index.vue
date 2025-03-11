// resources/js/Pages/Students/Index.vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from "sweetalert2"; // Ensure this line is uncommented
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";

const props = defineProps({ students: Object });

const deleteStudent = (id) => {
    if (confirm('Are you sure you want to delete this student?')) {
        router.delete(route('students.destroy', id));
    }
};
</script>

<template>
    <Head title="Students" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Students</h2>
        </template>
        
        <div class="p-4">
            <Link
                :href="route('students.create')"
                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 dark:bg-gray-200 dark:text-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 mb-3">
                Add Student
            </Link>
        </div>

        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th class="p-2">ID</th>
                        <th class="p-2">Student ID</th>
                        <th class="p-2">Full Name</th>
                        <th class="p-2">Program</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(student, index) in students.data" :key="student.id">
                        <td class="p-2">{{ index + 1 }}</td>
                        <td class="p-2">{{ student.student_id }}</td>
                        <td class="p-2">{{ student.student_name }} {{ student.father_name }} {{ student.grand_father_name }}</td>
                        <td class="p-2">{{ student.program }}</td>
                        <td class="p-2">
                            <Link :href="route('students.show', student.id)" class="text-green-500">View</Link> |
                            <Link :href="route('students.edit', student.id)" class="text-blue-500">Edit</Link> |
                            <button @click="deleteStudent(student.id)" class="text-red-500">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>