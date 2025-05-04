<script setup>
import { ref } from "vue";
import { router, Head, useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import BasicInfoForm from "./BasicInfoForm.vue";
import AcademicInfoForm from "./AcademicInfoForm.vue";
import ChurchInfoForm from "./ChurchInfoForm.vue";

// Props for form data
const props = defineProps({
    programs: { type: Array, required: true },
    years: { type: Array, required: true },
});

// Step-based form data
const form = useForm({
    first_name: "",
    middle_name: "",
    last_name: "",
    mobile_phone: "",
    office_phone: "",
    date_of_birth: "",
    email: "",
    marital_status: "",
    sex: "",
    address: "",
    year_id: "",
    semester_id: "",
    program_id: "",
    track_id: "",
    section_id: "",
    pastor_name: "",
    pastor_phone: "",
    position_denomination: "",
    church_name: "",
    church_address: "",
    student_signature: "",
    office_use_notes: "",
});

// Progress steps array
const steps = [
    { id: 1, label: "Basic Info" },
    { id: 2, label: "Academic Info" },
    { id: 3, label: "Church Info" },
];

// Current step tracking
const currentStep = ref(1);

const nextStep = () => {
    if (currentStep.value < steps.length) currentStep.value++;
};

const previousStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const submit = () => {
    form.post(route('students.store'), {
        onSuccess: () => {
            form.reset();
            currentStep.value = 1;
        },
    });
};
</script>

<template>
    <Head title="Register Student" />
    <AppLayout>
        <!-- Progress Indicator -->
        <div class="flex items-center justify-between mb-6">
            <div
                v-for="step in steps"
                :key="step.id"
                class="flex items-center"
            >
                <div
                    :class="{
                        'bg-blue-500 text-white': currentStep === step.id,
                        'bg-gray-300 text-gray-700': currentStep !== step.id
                    }"
                    class="rounded-full h-10 w-10 flex items-center justify-center font-bold"
                >
                    {{ step.id }}
                </div>
                <p
                    class="ml-2 text-sm"
                    :class="{
                        'text-blue-500': currentStep === step.id,
                        'text-gray-500': currentStep !== step.id
                    }"
                >
                    {{ step.label }}
                </p>
                <div
                    v-if="step.id < steps.length"
                    class="flex-grow h-1 mx-2"
                    :class="{
                        'bg-blue-500': currentStep > step.id,
                        'bg-gray-300': currentStep <= step.id
                    }"
                ></div>
            </div>
        </div>

        <!-- Main Form Content -->
        <transition
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-75"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-75"
        >
            <div
                :key="currentStep"
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
        </transition>
    </AppLayout>
</template>

