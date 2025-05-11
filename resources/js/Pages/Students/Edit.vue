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

console.log(props.student);
const form = useForm({
    first_name: props.student.firstName || "",
    middle_name: props.student.middleName || "",
    last_name: props.student.lastName || "",
    mobile_phone: props.student.mobilePhone || "",
    office_phone: props.student.officePhone || "",
    date_of_birth: props.student.dateOfBirth || "",
    marital_status: props.student.maritalStatus || "",
    sex: props.student.sex || "",
    address: props.student.address || "",
    email: props.student.email || "",
    year_id: props.student.year.id || "",
    semester_id: props.student.semester.id || "",
    program_id: props.student.program.id || "",
    track_id: props.student.track.id || "",
    study_mode_id: props.student.studyMode.id || "",
    total_credit_hours: props.student.totalCreditHours || "",
    total_amount_paid: props.student.totalAmountPaid || "",
    pastor_name: props.student.pastorName || "",
    pastor_phone: props.student.pastorPhone || "",
    position_denomination: props.student.positionDenomination || "",
    church_name: props.student.churchName || "",
    church_address: props.student.churchAddress || "",
    student_signature: props.student.studentSignature || "",
    office_use_notes: props.student.officeUseNotes || "",
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
    //Not sure what this code is for

    if (form.date_of_birth instanceof Date) {
        const year = form.date_of_birth.getFullYear();
        const month = String(form.date_of_birth.getMonth() + 1).padStart(2, '0');
        const day = String(form.date_of_birth.getDate()).padStart(2, '0');

        // Format to 'YYYY-MM-DD'
        form.date_of_birth = `${year}-${month}-${day}`;
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
                    :years="years"
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
