<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref } from "vue";
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Form from './Form.vue';

const props = defineProps({ student: Object, programs: Array, departments: Array, years: Array, semesters: Array });

const form = ref({ 
    ...props.student,
    student_id: props.student.student_id || '', 
    student_name: props.student.student_name || '', 
    father_name: props.student.father_name || '', 
    grand_father_name: props.student.grand_father_name || '', 
    mobile_phone: props.student.mobile_phone || '', 
    office_phone: props.student.office_phone || '', 
    date_of_birth: props.student.date_of_birth || '', 
    email: props.student.email || '', 
    marital_status: props.student.marital_status || '', 
    sex: props.student.sex || '', 
    address: props.student.address || '', 
    year_id: props.student.year_id || '', 
    semester_id: props.student.semester_id || '', 
    program_id: props.student.program_id || '', 
    total_credit_hours: props.student.total_credit_hours || '', 
    total_amount_paid: props.student.total_amount_paid || '', 
    pastor_name: props.student.pastor_name || '', 
    pastor_phone: props.student.pastor_phone || '', 
    position_denomination: props.student.position_denomination || '', 
    church_name: props.student.church_name || '', 
    church_address: props.student.church_address || '', 
    student_signature: props.student.student_signature || '', 
    office_use_notes: props.student.office_use_notes || '' 
});

const submit = () => {
    form.put(route('students.update', props.student.id), form.value);
};
</script>

<template>
    <Head title="Edit Student" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Edit Student</h2>
        </template>
        <Form :form="form" @submit="submit" />
    </AppLayout>
</template>
