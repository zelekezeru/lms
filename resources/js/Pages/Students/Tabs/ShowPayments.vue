<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, MultiSelect, Select } from "primevue";
import { computed, defineProps, ref, watch } from "vue";
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
    activeSemester: {
        type: Object,
        required: true,
    },
    paymentMethods: Array, // Add prop for payment types
    paymentTypes: Array, // Add prop for payment categories
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
    payment_type_id: null,
    payment_date: new Date().toISOString().slice(0, 10),
    paid_amount: null,
    total_amount: null,
    description: null,
    status: null,
    reference_number: null,
});

const unpaidEnrollments = props.student.enrollments.filter(
    (enrollment) => enrollment.status == "pending"
);

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
const perCoursePaymentType = ref(false);
const closeCreatePaymentModal = () => {
    createPaymentModal.value = false;
    paymentCreationForm.reset();
};
const selectedPaymentType = computed(() =>
    props.paymentTypes.find(
        (paymentType) =>
            paymentType.id == paymentCreationForm.payment_type_id &&
            paymentType.study_mode_id == props.student.studyMode.id
    )
);

watch(
    () => paymentCreationForm.paid_amount,
    (newVal) => {
        if (newVal == paymentCreationForm.total_amount) {
            paymentCreationForm.status = "completed";
        } else {
            paymentCreationForm.status = "pending";
        }
    }
);

watch(
    () => paymentCreationForm.status,
    (newVal) => {
        if (newVal == "completed") {
            paymentCreationForm.paid_amount = paymentCreationForm.total_amount;
        } else {
            paymentCreationForm.paid_amount = 0;
        }
    }
);

watch(
    () => paymentCreationForm.payment_type_id,
    () => {
        console.log(selectedPaymentType.value);

        if (
            selectedPaymentType.value &&
            selectedPaymentType.value.duration == "per-course"
        ) {
            perCoursePaymentType.value = true;

            paymentCreationForm.total_amount =
                selectedPaymentType.value.amount * unpaidEnrollments.length;
        } else {
            perCoursePaymentType.value = false;

            if (selectedPaymentType.value) {
                paymentCreationForm.total_amount =
                    selectedPaymentType.value.amount;
            }
        }
    }
);

const statusOptions = [
    { label: "pending", value: "pending" },
    { label: "completed", value: "completed" },
    { label: "canceled", value: "failed" },
];

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
                                        Description
                                    </th>
                                    <th
                                        class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Type
                                    </th>
                                    <th
                                        class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Method
                                    </th>
                                    <th
                                        class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Total Amount
                                    </th>
                                    <th
                                        class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        Paid Amount
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
                                            {{ payment.description }}
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
                                        {{ payment.paid_amount }}
                                    </td>
                                    <td
                                        class="w-20 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        {{ payment.payment_date }}
                                    </td>
                                    <td
                                        class="w-20 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                    >
                                        {{ payment.reference_number }}
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
        :maxWidth="'6xl'"
        class="p-6"
    >
        <div class="w-full px-8 py-6">
            <h1
                class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100"
            >
                Create Payment for –
                <span class="text-yellow-700 underline">
                    {{ student.first_name }} {{ student.middle_name }}
                </span>
            </h1>

            <form @submit.prevent="submitNewPayment">
                <!-- Description -->
                <div class="mb-4">
                    <label
                        for="narration"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                    >
                        Payment Description
                    </label>
                    <input
                        type="text"
                        v-model="paymentCreationForm.description"
                        placeholder="Payment Description (eg Registration Fee, Tuition Fee...)"
                        id="narration"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none focus:shadow-outline"
                    />
                    <InputError
                        :message="paymentCreationForm.errors.narration"
                        class="mt-2 text-sm text-red-500"
                    />
                </div>
                <div class="flex flex-wrap -mx-2">
                    <!-- Payment Method -->
                    <div class="w-full md:w-1/2 px-2 mb-4">
                        <label
                            for="payment_method_id"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >
                            Payment Method
                        </label>
                        <Select
                            v-model="paymentCreationForm.payment_method_id"
                            :options="paymentMethods"
                            optionLabel="bank_name"
                            optionValue="id"
                            appendTo="self"
                            placeholder="Select Payment Method"
                            class="w-full"
                        />
                        <InputError
                            :message="
                                paymentCreationForm.errors.payment_method_id
                            "
                            class="mt-2 text-sm text-red-500"
                        />
                    </div>

                    <!-- Payment Type -->
                    <div class="w-full md:w-1/2 px-2 mb-4">
                        <label
                            for="payment_type_id"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >
                            Payment Type
                        </label>
                        <Select
                            v-model="paymentCreationForm.payment_type_id"
                            :options="paymentTypes"
                            optionLabel="type"
                            optionValue="id"
                            appendTo="self"
                            placeholder="Select Type"
                            class="w-full"
                        />
                        <InputError
                            :message="
                                paymentCreationForm.errors.payment_type_id
                            "
                            class="mt-2 text-sm text-red-500"
                        />
                    </div>
                </div>

                <Transition
                    enter-active-class="transition ease-out duration-500"
                    enter-from-class="opacity-0 -translate-y-6"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-100"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-6"
                >
                    <div v-if="perCoursePaymentType" class="mb-6">
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3"
                        >
                            Courses with Unpaid Fees –
                            {{ activeSemester.year.name }} Semester
                            {{ activeSemester.level }}
                        </h2>

                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-1 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            #
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-1 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Course Name
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-1 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Fee Amount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700"
                                >
                                    <tr
                                        v-for="(
                                            enrollment, index
                                        ) in unpaidEnrollments"
                                        :key="enrollment.course.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                    >
                                        <td
                                            class="px-6 py-2 text-sm text-gray-800 dark:text-gray-200"
                                        >
                                            {{ index + 1 }}
                                        </td>
                                        <td
                                            class="px-6 py-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {{ enrollment.course.name }}
                                        </td>
                                        <td
                                            class="px-6 py-2 text-sm text-yellow-700 dark:text-yellow-400"
                                        >
                                            {{ selectedPaymentType.amount }}
                                        </td>
                                    </tr>

                                    <!-- Total Row -->
                                    <tr
                                        class="bg-yellow-50 dark:bg-yellow-900 font-bold"
                                    >
                                        <td
                                            colspan="2"
                                            class="px-6 py-2 text-gray-900 dark:text-gray-100"
                                        >
                                            Total
                                        </td>
                                        <td
                                            class="px-6 py-2 text-yellow-800 dark:text-yellow-200 text-lg"
                                        >
                                            {{
                                                selectedPaymentType.amount *
                                                unpaidEnrollments.length
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div
                        v-else-if="
                            selectedPaymentType &&
                            selectedPaymentType.duration != 'per-course'
                        "
                        class="mb-6"
                    >
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3"
                        >
                            Courses with Unpaid Fees –
                            {{ activeSemester.year.name }} Semester
                            {{ activeSemester.level }}
                        </h2>

                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-1 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            #
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-1 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Fee Description
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-1 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                        >
                                            Fee Amount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700"
                                >
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                    >
                                        <td
                                            class="px-6 py-2 text-sm text-gray-800 dark:text-gray-200"
                                        >
                                            1
                                        </td>
                                        <td
                                            class="px-6 py-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {{ selectedPaymentType.type }}
                                        </td>
                                        <td
                                            class="px-6 py-2 text-sm text-yellow-700 dark:text-yellow-400"
                                        >
                                            {{ selectedPaymentType.amount }}
                                        </td>
                                    </tr>

                                    <!-- Total Row -->
                                    <tr
                                        class="bg-yellow-50 dark:bg-yellow-900 font-bold"
                                    >
                                        <td
                                            colspan="2"
                                            class="px-6 py-2 text-gray-900 dark:text-gray-100"
                                        >
                                            Total
                                        </td>
                                        <td
                                            class="px-6 py-2 text-yellow-800 dark:text-yellow-200 text-lg"
                                        >
                                            {{ selectedPaymentType.amount }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </Transition>

                <!-- Last Four Fields in a Responsive Flex Row -->
                <div class="flex flex-wrap -mx-2">
                    <!-- Status -->
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4">
                        <label
                            for="status"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >
                            Status
                        </label>
                        <Select
                            v-model="paymentCreationForm.status"
                            :options="statusOptions"
                            optionLabel="label"
                            optionValue="value"
                            appendTo="self"
                            placeholder="Select Status"
                            class="w-full"
                        />
                        <InputError
                            :message="paymentCreationForm.errors.status"
                            class="mt-2 text-sm text-red-500"
                        />
                    </div>

                    <!-- Paid Amount -->
                    <Transition
                        enter-active-class="transition ease-out duration-500"
                        enter-from-class="opacity-0 -translate-x-6"
                        enter-to-class="opacity-100 translate-x-0"
                        leave-active-class="transition ease-in duration-300"
                        leave-from-class="opacity-100 translate-x-0"
                        leave-to-class="opacity-0 -translate-x-6"
                    >
                        <div
                            class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4"
                            v-if="paymentCreationForm.status !== 'completed'"
                        >
                            <label
                                for="paid_amount"
                                class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                            >
                                Paid Amount
                            </label>
                            <input
                                type="number"
                                v-model="paymentCreationForm.paid_amount"
                                id="paid_amount"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none focus:shadow-outline"
                            />
                            <InputError
                                :message="
                                    paymentCreationForm.errors.paid_amount
                                "
                                class="mt-2 text-sm text-red-500"
                            />
                        </div>
                    </Transition>
                    <!-- Payment Date -->
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4">
                        <label
                            for="payment_date"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >
                            Payment Date
                        </label>
                        <input
                            type="date"
                            v-model="paymentCreationForm.payment_date"
                            id="payment_date"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none focus:shadow-outline"
                        />
                        <InputError
                            :message="paymentCreationForm.errors.payment_date"
                            class="mt-2 text-sm text-red-500"
                        />
                    </div>

                    <!-- Reference Number -->
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4">
                        <label
                            for="reference_number"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >
                            Reference Number (Optional)
                        </label>
                        <input
                            type="text"
                            v-model="paymentCreationForm.reference_number"
                            id="reference_number"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none focus:shadow-outline"
                        />
                        <InputError
                            :message="
                                paymentCreationForm.errors.reference_number
                            "
                            class="mt-2 text-sm text-red-500"
                        />
                    </div>
                </div>

                <!-- Buttons -->
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
