// resources/js/Pages/Students/Index.vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
            <Link :href="route('students.create')" class="btn btn-primary mb-4">Add Student</Link>
            <table class="w-full bg-white shadow-md rounded">
                <thead>
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