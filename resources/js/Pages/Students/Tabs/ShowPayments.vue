<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, MultiSelect } from "primevue";
import { defineProps, ref } from "vue";
import {  useForm } from "@inertiajs/vue3";
import {
    CogIcon,
    PencilSquareIcon, XMarkIcon, PlusCircleIcon} from "@heroicons/vue/24/solid";
const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    payments: {
        type: Array,
        required: true,
    },
});

const assignPayments = ref(false);
const paymentAssignmentForm = useForm({
    payments: props.student.payments.map(payment => payment.id),
});

const closePaymentAssignment = () => {
    assignPayments.value = false;
    paymentAssignmentForm.reset();
    paymentAssignmentForm.clearErrors();
};

const submitPaymentAssignment = () => {
    paymentAssignmentForm.post(
        route('payments-student.assign', { student: props.student.id }),
        {
            onSuccess: () => {
                Swal.fire(
                    'Successful!',
                    'Payments assigned successfully.',
                    'success'
                );
                assignPayments.value = false;
                paymentAssignmentForm.reset();
                paymentAssignmentForm.payments = props.section.payments.map(payment => payment.id);
            },
        }
    );
};

</script>


<template>
    <div class="">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Payments
            </h2>
            <button
                @click="assignPayments = !assignPayments"
                class="flex text-indigo-600 hover:text-indigo-800"
            >
                <component
                    :is="assignPayments ? XMarkIcon : PlusCircleIcon"
                    class="w-8 h-8"
                />
                Assign Section
            </button>
        </div>

        <div class="overflow-x-auto">
            <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
                
                <div class="flex items-center justify-between mb-4">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Payments
                    </h2>
                </div>

                <!-- Make sure Payments is not null -->
                <div v-if="!student.payments" class="text-center">
                    <p class="text-gray-500 dark:text-gray-400">
                        No payment information available.
                    </p>
                </div>
                <!-- Student payments list -->
                <div v-else-if="student.payments" class="flex flex-col">
                    <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Enrolled Payments</span
                        >
                        
                            <table
                                class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                            >
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th
                                            class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            No.
                                        </th>
                                        <th
                                            class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            Name
                                        </th>
                                        <th
                                            class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            Payment Code
                                        </th>
                                        <th
                                            class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            Credit Hours
                                        </th>
                                        <th
                                            class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            Status
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(payment, index) in student.payments"
                                        :key="payment.id"
                                        :class="
                                            index % 2 === 0
                                                ? 'bg-white dark:bg-gray-800'
                                                : 'bg-gray-50 dark:bg-gray-700'
                                        "
                                        class="border-b border-gray-300 dark:border-gray-600">
                                        <td
                                            class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            {{
                                                index + 1
                                            }}
                                        </td>

                                        <td
                                            class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            <Link
                                                :href="
                                                    route('payments.show', {
                                                        payment: payment.id,
                                                    })
                                                "
                                            >
                                                {{ payment.name }}
                                            </Link>
                                        </td>
                                        <td
                                            class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            {{ payment.code }}
                                        </td>
                                        <td
                                            class="w-20 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                        >
                                            {{ payment.credit_hours }}
                                        </td>

                                        
                                        <!-- Payment Assessments -->
                                        <td
                                            class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                        >
                                        <!-- If Status is 1 Active in Green and if Status is 0 Inactive in red -->
                                            <span
                                                :class="
                                                    payment.status === 1
                                                        ? 'text-green-500'
                                                        : 'text-red-500'
                                                "
                                                >{{ payment.status === 1
                                                    ? 'Active'
                                                    : 'Inactive'
                                                }}</span
                                            >
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-else class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >No Payments Enrolled</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.name }}
                            has not enrolled in any payments yet.
                        </span>
                    </div>
                </div>
            </div>
    </div>

    <Modal
        :show="assignPayments"
        @close="assignPayments = !assignPayments"
        :maxWidth="'6xl'"
        class="p-24 h-full"
    >
        <div class="w-full px-16 py-8">
            <h1 class="text-lg mb-5">
                Pick Payments You Would like To Assign To This Section
            </h1>

            <Listbox
                id="cousesList"
                v-model="paymentAssignmentForm.payments"
                :options="payments"
                optionLabel="name"
                option-value="id"
                appendTo="self"
                filter
                checkmark
                multiple
                list-style="max-height: 500px"
                placeholder="Select Payments"
                :maxSelectedLabels="3"
                class="w-full"
            />

            <InputError
                :message="paymentAssignmentForm.errors.programs"
                class="mt-2 text-sm text-red-500"
            />
            <div class="flex justify-end mt-4">
                <button
                    @click="submitPaymentAssignment"
                    :disabled="paymentAssignmentForm.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                >
                    {{ paymentAssignmentForm.processing ? "Assigning..." : "Assign" }}
                </button>

                <button
                    @click="closePaymentAssignment"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                >
                    Close
                </button>
            </div>
        </div>
    </Modal>
</template>
