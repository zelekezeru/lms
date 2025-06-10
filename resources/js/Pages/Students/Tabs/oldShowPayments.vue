<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, MultiSelect } from "primevue";
import { defineProps, ref } from "vue";
import InputError from "@/Components/InputError.vue";
import {
    CogIcon,
    PencilSquareIcon,
    XMarkIcon,
    PlusCircleIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    payments: {
        type: Array,
        required: true,
    },
    paymentMethods: Array, // Add prop for payment types
    paymentCategories: Array, // Add prop for payment categories
});

// Refs for controlling modal visibility
const assignPayments = ref(false);
const createPaymentModal = ref(false);

// Form for assigning payments
const paymentForm = useForm({
    payments: [],
    student_id: props.student.id, // Set student ID here
});

// Form for creating new payment
const paymentCreationForm = useForm({
    student_id: props.student.id,
    payment_method_id: null,
    payment_category_id: null,
    payment_date: new Date().toISOString().slice(0, 10),
    total_amount: null,
    narration: null,
    status: null,
    payment_reference: null,
});

const closePayment = () => {
    assignPayments.value = false;
    paymentForm.reset();
};

const submitPayment = () => {
    paymentForm.post(route("payments", props.student.id), {
        preserveScroll: true,
        onSuccess: () => {
            assignPayments.value = false;
            paymentForm.reset();
            // Optionally, show a success message or refresh data
        },
    });
};

const closeCreatePaymentModal = () => {
    createPaymentModal.value = false;
    paymentCreationForm.reset();
};

const submitNewPayment = () => {
    paymentCreationForm.post(
        route("payments.store", { student: props.student.id }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Success!",
                    "Payment has been created successfully.",
                    "success"
                );
                createPaymentModal.value = false;
                paymentCreationForm.reset();
            },
            onError: (errors) => {
                Swal.fire(
                    "Error!",
                    "Failed to create the payment. Please check the form for errors.",
                    "error"
                );
                console.log(errors); // Log errors for debugging
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
                @click="createPaymentModal = !createPaymentModal"
                class="flex text-green-600 hover:text-green-800"
            >
                <PlusCircleIcon class="mx-2 w-8 h-8" />
                Create Payment
            </button>
        </div>

        <div class="overflow-x-auto">
            <div
                class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <div v-if="!payments" class="text-center">
                    <p class="text-gray-500 dark:text-gray-400">
                        No payment information available.
                    </p>
                </div>
                <div v-else-if="payments" class="flex flex-col">
                    <div
                        class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
                    >
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
                                        Naration
                                    </th>
                                    <th
                                        class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Category
                                    </th>
                                    <th
                                        class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Method
                                    </th>
                                    <th
                                        class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Amount
                                    </th>
                                    <th
                                        class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Reference
                                    </th>
                                    <th
                                        class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(payment, index) in payments"
                                    :key="payment.id"
                                    :class="
                                        index % 2 === 0
                                            ? 'bg-white dark:bg-gray-800'
                                            : 'bg-gray-50 dark:bg-gray-700'
                                    "
                                    class="border-b border-gray-300 dark:border-gray-600"
                                >
                                    <td
                                        class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        {{ index + 1 }}
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
                                            {{
                                                payment.enrollment
                                                    ? payment.enrollment
                                                          .course_offering
                                                          .course.name
                                                    : "N/A"
                                            }}
                                        </Link>
                                    </td>
                                    <td
                                        class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        {{
                                            payment.payment_type
                                                ? payment.payment_type.type
                                                : "N/A"
                                        }}
                                    </td>
                                    <td
                                        class="w-20 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        {{
                                            payment.payment_method
                                                ? payment.payment_method
                                                      .bank_name
                                                : "N/A"
                                        }}
                                    </td>
                                    <td
                                        class="w-20 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        {{ payment.total_amount }}
                                    </td>
                                    <td
                                        class="w-20 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        {{ payment.payment_date }}
                                    </td>
                                    <td
                                        class="w-20 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        {{ payment.payment_reference }}
                                    </td>
                                    <td
                                        class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        <span
                                            :class="{
                                                'text-yellow-500':
                                                    payment.status ===
                                                    'pending',
                                                'text-green-500':
                                                    payment.status ===
                                                    'completed',
                                                'text-red-500':
                                                    payment.status === 'failed',
                                                'text-blue-500':
                                                    payment.status ===
                                                    'refunded',
                                            }"
                                        >
                                            {{
                                                payment.status
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                payment.status.slice(1)
                                            }}
                                        </span>
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
        @close="assignPayments = false"
        :maxWidth="'6xl'"
        class="p-24 h-full"
    >
        <div class="w-full px-16 py-8">
            <h1 class="text-lg mb-5">
                Pick Payments You Would like To Assign To This Section
            </h1>

            <Listbox
                id="cousesList"
                v-model="paymentForm.payments"
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
                :message="paymentForm.errors.payments"
                class="mt-2 text-sm text-red-500"
            />
            <div class="flex justify-end mt-4">
                <button
                    @click="submitPayment"
                    :disabled="paymentForm.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                >
                    {{ paymentForm.processing ? "Assigning..." : "Assign" }}
                </button>

                <button
                    @click="closePayment"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                >
                    Close
                </button>
            </div>
        </div>
    </Modal>

    <Modal
        :show="createPaymentModal"
        @close="closeCreatePaymentModal"
        :maxWidth="'md'"
        class="p-6"
    >
        <div class="w-full px-8 py-6">
            <h1
                class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100"
            >
                Create Payment for -
                <span class="text-yellow-700 underline"
                    >{{ student.first_name }} {{ student.middle_name }}</span
                >
            </h1>

            <form @submit.prevent="submitNewPayment">
                <div class="mb-4">
                    <label
                        for="payment_method_id"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >Payment Method</label
                    >
                    <select
                        v-model="paymentCreationForm.payment_method_id"
                        id="payment_method_id"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                    >
                        <option :value="null" disabled>
                            Select Payment Method
                        </option>
                        <option
                            v-for="type in paymentMethods"
                            :key="type.id"
                            :value="type.id"
                        >
                            {{ type.bank_name }}
                        </option>
                    </select>
                    <InputError
                        :message="paymentCreationForm.errors.payment_method_id"
                        class="mt-2 text-sm text-red-500"
                    />
                </div>

                <div class="mb-4">
                    <label
                        for="payment_category_id"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >Payment Category</label
                    >
                    <select
                        v-model="paymentCreationForm.payment_category_id"
                        id="payment_category_id"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                    >
                        <option :value="null" disabled>Select Category</option>
                        <option
                            v-for="category in paymentCategories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                    <InputError
                        :message="
                            paymentCreationForm.errors.payment_category_id
                        "
                        class="mt-2 text-sm text-red-500"
                    />
                </div>

                <div class="mb-4">
                    <label
                        for="total_amount"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >Total Amount</label
                    >
                    <input
                        type="number"
                        v-model="paymentCreationForm.total_amount"
                        id="total_amount"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                    />
                    <InputError
                        :message="paymentCreationForm.errors.total_amount"
                        class="mt-2 text-sm text-red-500"
                    />
                </div>

                <div class="mb-4">
                    <label
                        for="narration"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >Payment Naration</label
                    >
                    <input
                        type="text"
                        v-model="paymentCreationForm.narration"
                        id="narration"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                    />
                    <InputError
                        :message="paymentCreationForm.errors.narration"
                        class="mt-2 text-sm text-red-500"
                    />
                </div>

                <div class="mb-4">
                    <label
                        for="payment_date"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >Payment Date</label
                    >
                    <input
                        type="date"
                        v-model="paymentCreationForm.payment_date"
                        id="payment_date"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                    />
                    <InputError
                        :message="paymentCreationForm.errors.payment_date"
                        class="mt-2 text-sm text-red-500"
                    />
                </div>

                <div class="mb-4">
                    <label
                        for="status"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >Status</label
                    >
                    <select
                        v-model="paymentCreationForm.status"
                        id="status"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                    >
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                    <InputError
                        :message="paymentCreationForm.errors.status"
                        class="mt-2 text-sm text-red-500"
                    />
                </div>

                <div class="mb-4">
                    <label
                        for="payment_reference"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >Payment Reference (Optional)</label
                    >
                    <input
                        type="text"
                        v-model="paymentCreationForm.payment_reference"
                        id="payment_reference"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline"
                    />
                    <InputError
                        :message="paymentCreationForm.errors.payment_reference"
                        class="mt-2 text-sm text-red-500"
                    />
                </div>

                <div class="flex justify-end mt-4">
                    <button
                        type="submit"
                        :disabled="paymentCreationForm.processing"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                    >
                        {{
                            paymentCreationForm.processing
                                ? "Creating..."
                                : "Create Payment"
                        }}
                    </button>

                    <button
                        type="button"
                        @click="closeCreatePaymentModal"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                    >
                        Close
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
