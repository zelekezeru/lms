<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import PaymentsForm from "./Form.vue";

defineProps({
    student: Object,
    paymentTypes: Array,
    paymentCategories: Array,
    paymentSchedules: Array,
});

const form = useForm({
    student_id: props.student.id,
    payment_type_id: "",
    payment_category_id: null,
    payment_schedule_item_id: null,
    payment_date: new Date().toISOString().slice(0, 10),
    total_amount: "",
    payment_method: "",
    status: "pending", // Default status
    payment_reference: "",
    items: [], // For Payment Items
});

const submit = () => {
    form.post(route("payments.store"));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Record New Payment for {{ student.name }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Please fill out the form below to record a new payment.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition">
                <PaymentsForm
                    :form="form"
                    @submit="submit"
                    :paymentTypes="paymentTypes"
                    :paymentCategories="paymentCategories"
                    :paymentSchedules="paymentSchedules"
                />
            </div>
        </div>
    </AppLayout>
</template>