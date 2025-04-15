<script setup>
import { ref } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import BasicInfoForm from './BasicInfoForm.vue';
import AcademicInfoForm from './AcademicInfoForm.vue';
import ChurchInfoForm from './ChurchInfoForm.vue';


const props = defineProps({
    programs: { type: Array, required: true },
    departments: { type: Array, required: true },
    years: { type: Array, required: true },
    semesters: { type: Array, required: true },
});

const form = ref({
    student_name: '',
    father_name: '',
    grand_father_name: '',
    mobile_phone: '',
    office_phone: '',
    date_of_birth: '',
    email: '',
    marital_status: '',
    sex: '',
    address: '',
    year_id: '',
    semester_id: '',
    program_id: '',
    department_id: '',
    section_id: '',
    pastor_name: '',
    pastor_phone: '',
    position_denomination: '',
    church_name: '',
    church_address: '',
    student_signature: '',
    office_use_notes: ''
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
    router.post(route('students.store'), form.value, {
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
    <Head title="Register Student" />
    <AppLayout>
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
