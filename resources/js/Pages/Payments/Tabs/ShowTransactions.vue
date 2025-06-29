<script setup>
import { ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

import {
    PlusCircleIcon,
    PencilSquareIcon,
    TrashIcon,
    XMarkIcon,
} from "@heroicons/vue/24/solid";

import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import FormFields from "@/Pages/Finance/Partials/FormFields.vue"; // Adjusted path for FormFields

const props = defineProps({
    paymentMethods: {
        type: Object,
        required: true,
    },
    payments: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Transactions
        </h1>
        <Table>
            <Thead>
            <tr>
                <TableHeader>Payment Method</TableHeader>
                <TableHeader>Amount</TableHeader>
                <TableHeader>Date</TableHeader>
                <TableHeader>Status</TableHeader>
            </tr>
            </Thead>
            <tbody is="TableZebraRows">
            <tr v-for="payment in payments.data" :key="payment.id">
                <td>
                {{
                    paymentMethods[payment.payment_method_id]
                        ? paymentMethods[payment.payment_method_id].name
                        : 'Unknown'
                }}
                </td>
                <td>{{ payment.total_amount }}</td>
                <td>{{ payment.payment_date }}</td>
                <td>
                    <span :class="{
                        'text-yellow-500': payment.status === 'pending',
                        'text-green-500': payment.status === 'completed',
                        'text-red-500': payment.status === 'failed',
                        'text-blue-500': payment.status === 'paid_by_college',
                    }">
                        <span v-if="payment.status === 'paid_by_college'"><b>Scholarship</b></span>
                        <span v-else-if="payment.status === 'pending'"><b>Pending</b></span>
                        <span v-else-if="payment.status === 'completed'"><b>Completed</b></span>
                        <span v-else-if="payment.status === 'failed'"><b>Failed</b></span>
                        <span v-else><b>{{ payment.status }}</b></span>
                    </span>
                </td>
            </tr>
            </tbody>
        </Table>
        <template v-if="!payments.data.length">
            <p class="text-gray-500 dark:text-gray-400">
            No transactions available at the moment.
            </p>
        </template>
    </div>
</template>
