<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ student: Object });
</script>

<template>
    <Head title="Student Details" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Student Details</h2>
        </template>
        <div class="p-4 bg-white shadow rounded">
            <p><strong>ID:</strong> {{ student.id }}</p>
            <p><strong>Student ID:</strong> {{ student.student_id }}</p>
            <p><strong>Name:</strong> {{ student.student_name }}</p>
            <p><strong>Father's Name:</strong> {{ student.father_name }}</p> 
            <p><strong>Grandfather's Name:</strong> {{ student.grand_father_name }}</p>
            <p><strong>Program:</strong> {{ student.program }}</p>
            <p><strong>Year of Study:</strong> {{ student.year_of_study }}</p>
            <p><strong>Phone:</strong> {{ student.mobile_phone }}</p>
            <p><strong>Email:</strong> {{ student.email }}</p>
            <p><strong>Total Credits:</strong> {{ student.total_credit_hours }}</p>
            <p><strong>Total Amount Paid:</strong> {{ student.total_amount_paid }}</p>
            <Link :href="route('students.index')" class="text-blue-500">Back to List</Link>
        </div>
    </AppLayout>
</template>
