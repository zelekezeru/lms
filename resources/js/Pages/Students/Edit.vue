<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref } from "vue";
import { Head, router } from '@inertiajs/vue3';
import BasicInfoForm from './BasicInfoForm.vue';
import AcademicInfoForm from './AcademicInfoForm.vue';
import ChurchInfoForm from './ChurchInfoForm.vue';

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
    department_id: props.student.department_id || '',
    section_id: props.student.section_id || '',
    total_credit_hours: props.student.total_credit_hours || '', 
    total_amount_paid: props.student.total_amount_paid || '', 
    pastor_name: props.student.pastor_name || '', 
    pastor_phone: props.student.pastor_phone || '', 
    position_denomination: props.student.position_denomination || '', 
    church_name: props.student.church_name || '', 
    church_address: props.student.church_address || '', 
    student_signature: props.student.student_signature || '', 
    office_use_notes: props.student.office_use_notes || '' ,
    _method: "PATCH", 
});


const currentStep = ref(1);

const nextStep = () => {
    if (currentStep.value < 3) currentStep.value++;
};

const previousStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const submit = () => {
    form.value.processing = true; // Optional: Add a processing state if needed
    router.post(route('students.update', props.student.id), form.value, {
        onSuccess: () => {
            alert('Student registered successfully!');
        },
        onError: (errors) => {
            form.value.errors = errors; // Capture validation errors
        },
        onFinish: () => {
            form.value.processing = false; // Reset processing state
        },
    });
};
</script>

<template>
    <Head title="Edit Student" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Edit Student</h2>
        </template>
        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition">
            <div v-if="currentStep === 1">
                <BasicInfoForm :form="form" @next="nextStep" />
            </div>
            <div v-else-if="currentStep === 2">
                <AcademicInfoForm :form="form" 
                    :programs="programs"
                    :departments="departments"
                    :years="years"
                    :semesters="semesters"
                @next="nextStep" @previous="previousStep" />
            </div>
            <div v-else-if="currentStep === 3">
                <ChurchInfoForm :form="form" @submit="submit" @previous="previousStep" />
            </div>
        </div>
    </AppLayout>
</template>
