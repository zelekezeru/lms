<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import BasicInfoForm from "./BasicInfoForm.vue";
import AcademicInfoForm from "./AcademicInfoForm.vue";
import ChurchInfoForm from "./ChurchInfoForm.vue";

const props = defineProps({
    student: Object,
    programs: Array,
    tracks: Array,
    years: Array,
    semesters: Array,
});

const form = useForm({
    student_id: props.student.student_id || "",
    first_name: props.student.first_name || "",
    middle_name: props.student.middle_name || "",
    last_name: props.student.last_name || "",
    mobile_phone: props.student.mobile_phone || "",
    office_phone: props.student.office_phone || "",
    date_of_birth: props.student.date_of_birth || "",
    marital_status: props.student.marital_status || "",
    sex: props.student.sex || "",
    address: props.student.address || "",
    email: props.student.email || "",
    year_id: props.student.year_id || "",
    semester_id: props.student.semester_id || "",
    program_id: props.student.program_id || "",
    track_id: props.student.track_id || "",
    study_mode_id: props.student.studyMode_id || "",
    section_id: props.student.section_id || "",
    total_credit_hours: props.student.total_credit_hours || "",
    total_amount_paid: props.student.total_amount_paid || "",
    pastor_name: props.student.pastor_name || "",
    pastor_phone: props.student.pastor_phone || "",
    position_denomination: props.student.position_denomination || "",
    church_name: props.student.church_name || "",
    church_address: props.student.church_address || "",
    student_signature: props.student.student_signature || "",
    office_use_notes: props.student.office_use_notes || "",
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
    if (form.value.date_of_birth instanceof Date) {
        const year = form.value.date_of_birth.getFullYear();
        const month = String(form.value.date_of_birth.getMonth() + 1).padStart(2, '0');
        const day = String(form.value.date_of_birth.getDate()).padStart(2, '0');

        // Format to 'YYYY-MM-DD'
        form.value.date_of_birth = `${year}-${month}-${day}`;
    }

    form.post(route("students.update", props.student.id));
};
</script>

<template>
    <Head title="Edit Student" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Edit Student</h2>
        </template>
        <div
            class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
        >
            <div v-if="currentStep === 1">
                <BasicInfoForm :form="form" @next="nextStep" />
            </div>
            <div v-else-if="currentStep === 2">
                <AcademicInfoForm
                    :form="form"
                    :programs="programs"
                    :tracks="tracks"
                    :years="years"
                    :semesters="semesters"
                    @next="nextStep"
                    @previous="previousStep"
                />
            </div>
            <div v-else-if="currentStep === 3">
                <ChurchInfoForm
                    :form="form"
                    @submit="submit"
                    @previous="previousStep"
                />
            </div>
        </div>
    </AppLayout>
</template>
