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
        <div class="p-6 bg-white shadow rounded-lg">
            <div class="flex justify-center mb-6">
                <img :src="student.profile_image_url" alt="Profile Image" class="w-32 h-32 rounded-full shadow-lg" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p><strong>ID:</strong> {{ student.id }}</p>
                    <p><strong>Student ID:</strong> {{ student.student_id }}</p>
                    <p><strong>Name:</strong> {{ student.student_name }}</p>
                    <p><strong>Father's Name:</strong> {{ student.father_name }}</p> 
                    <p><strong>Grandfather's Name:</strong> {{ student.grand_father_name }}</p>
                    <p><strong>Mobile Phone:</strong> {{ student.mobile_phone }}</p>
                    <p><strong>Office Phone:</strong> {{ student.office_phone }}</p>
                    <p><strong>Date of Birth:</strong> {{ student.date_of_birth }}</p>
                    <p><strong>Email:</strong> {{ student.email }}</p>
                    <p><strong>Marital Status:</strong> {{ student.marital_status }}</p>
                    <p><strong>Sex:</strong> {{ student.sex }}</p>
                </div>
                <div>
                    <p><strong>Academic Year:</strong> {{ student.academic_year }}</p>
                    <p><strong>Semester:</strong> {{ student.semester }}</p>
                    <p><strong>Program:</strong> {{ student.program }}</p>
                    <p><strong>Pastor's Name:</strong> {{ student.pastor_name }}</p>
                    <p><strong>Pastor's Phone:</strong> {{ student.pastor_phone }}</p>
                    <p><strong>Address:</strong> {{ student.address_1 }}</p>
                    <p><strong>Position/Denomination:</strong> {{ student.position_denomination }}</p>
                    <p><strong>Church Name:</strong> {{ student.church_name }}</p>
                    <p><strong>Church Address:</strong> {{ student.church_address }}</p>
                    <p><strong>Office Use Notes:</strong> {{ student.office_use_notes }}</p>
                </div>
            </div>
            <div class="mt-6 text-right">
                <Link :href="route('students.index')" class="btn btn-secondary">Back to List</Link>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.btn {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-secondary {
    background-color: #6c757d;
    color: #fff;
}

.btn-secondary:hover {
    background-color: #5a6268;
}
</style>
