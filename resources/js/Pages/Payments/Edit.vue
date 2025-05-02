<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import PaymentsForm from "./Form.vue";

defineProps({
    payment: Object,
    paymentTypes: Array,
    paymentCategories: Array,
    paymentSchedules: Array,
});

const form = useForm({
    student_id: payment.student_id,
    payment_type_id: payment.payment_type_id,
    payment_category_id: payment.payment_category_id,
    payment_schedule_item_id: payment.payment_schedule_item_id,
    payment_date: payment.payment_date,
    total_amount: payment.total_amount,
    payment_method: payment.payment_method,
    status: payment.status,
    payment_reference: payment.payment_reference,
    items: payment.paymentItems || [], // Pre-fill payment items
});

const submit = () => {
    form.put(route("payments.update", payment.id));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit Payment
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Update the details of the payment below.
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