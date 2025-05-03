<script setup type="module">
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import PaymentsForm from "./Form.vue";

const props = defineProps({
    form: Object,
    users: {
        type: Object,
        default: () => [],
    },
    paymentCategories: {
        type: Object,
        default: () => [],
    },
    paymentSchedules: {
        type: Object,
        default: () => [],
    },
    paymentTypes: {
        type: Object,
        default: () => [],
    },
});

const isEditing = ref(false);

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