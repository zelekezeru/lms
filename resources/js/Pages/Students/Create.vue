<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Form from './Form.vue';

const form = ref({ 
    student_id: '', 
    student_name: '', 
    father_name: '', 
    grand_father_name: '', 
    mobile_phone: '', 
    office_phone: '', 
    date_of_birth: '', 
    email: '', 
    marital_status: '', 
    sex: '', 
    address_1: '', 
    academic_year: '', 
    semester: '', 
    program_id: '', 
    total_credit_hours: '', 
    total_amount_paid: '', 
    pastor_name: '', 
    pastor_phone: '', 
    position_denomination: '', 
    church_name: '', 
    church_address: '', 
    student_signature: '', 
    office_use_notes: '' 
});

const submit = () => {
    router.post(route('students.store'), form.value);
};
</script>

<template>
    <Head title="Register Student" />
    <AppLayout>
        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition">
            <Form :form="form" @submit="submit" />
        </div>
    </AppLayout>
</template>
