<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";

// Define the props for this component
defineProps({
    users: Object,
    paymentCategories: Object,
    paymentSchedules: Object,
});

// Initialize the form with the payment fields
const form = useForm({
    name: "",
    description: "",
    amount: null,
    type: 1,
    payment_method: 1,
    payment_status: 1,
    installment_count: null,
    installment_interval: null,
    grace_period: null,
    late_fee: null,
    late_fee_amount: null,
    discount: null,
    discount_amount: null,
    penalty: null,
    penalty_amount: null,
    status: 1,
    payment_schedule_id: null,
    payment_category_id: null,
    user_id: "",
    total_amount: null,
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
            Payment Creation Form
         </h2>
         <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
            Please fill out the form below to create a new payment.
         </p>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition">
         <Form
            :users="users"
            :paymentCategories="paymentCategories"
            :paymentSchedules="paymentSchedules"
            @submit="submit"
         />
        </div>
     </div>
    </AppLayout>
</template>
