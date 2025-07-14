<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
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
    semesters: {
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

const paymentStatusLabels = {
    pending: "Pending",
    completed: "Completed",
    paid_by_college: "Paid by College",
    canceled: "Canceled",
};

const errors = usePage().props.errors;
// Refs for controlling modal visibility
const assignPayments = ref(false);
const createPaymentModal = ref(false);

// Form for assigning payments
const paymentForm = useForm({
    payments: [],
    student_id: props.student.id, // Set student ID here
});

const selectedStatus = ref("");
const selectedSemester = ref(props.activeSemester.id);

const semesterPayments = ref(
    props.payments.filter(
        (payment) => payment.semester?.id === selectedSemester.value
    )
);

watch(
    () => [selectedSemester.value, selectedStatus.value, props.payments],
    ([newSemester, newStatus]) => {
        console.log("Semester:", newSemester);
        console.log("Status:", newStatus);

        semesterPayments.value = props.payments.filter((payment) => {
            const matchesSemester =
                !newSemester || payment.semester?.id === newSemester;
            const matchesStatus = !newStatus || payment.status === newStatus;

            return matchesSemester && matchesStatus;
        });
    },
    { immediate: true } // runs the first time on mount
);

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

const paymentUpdateForm = useForm({
    payment_method_id: null,
    payment_type_id: props.payments[0]?.payment_type_id || null,
    description: props.payments[0]?.description || "",
    status: props.payments[0]?.status || "pending",
    paid_amount: props.payments[0]?.paid_amount || 0,
    payment_date: new Date().toISOString().slice(0, 10),
    reference_number: props.payments[0]?.reference_number || "",
    _method: "PATCH",
});

const selectedPayment = ref(null);
const updatePaymentModal = ref(false);
const addCode = ref(false);

const paymentCodeForm = useForm({
    student_id: props.student.id,
    paymentCode: props.student.paymentCode || "",
});

const closeCode = () => {
    addCode.value = false;
};

const submitPaymentCode = () => {
    if (!paymentCodeForm.paymentCode.trim()) {
        Swal.fire({
            icon: "error",
            title: "Missing Code",
            text: "Please enter a valid payment code.",
        });
        return;
    }

    if (paymentCodeForm.paymentCode.length < 5) {
        Swal.fire({
            icon: "error",
            title: "Invalid Code",
            text: "Payment code must be at least 5 characters long.",
        });
        return;
    }
    closeCode();

    Swal.fire({
        title: "Are you sure?",
        text: "This will Add the payment code for the student.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Create it",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            paymentCodeForm.post(
                route("students.payment-code", props.student.id),
                {
                    onSuccess: () => {
                        Swal.fire({
                            icon: "success",
                            title: "Created!",
                            text: "Payment code saved successfully.",
                        });
                        closeCode();
                        // Optionally refresh the student paymentCode
                        props.student.paymentCode = paymentCodeForm.paymentCode;
                    },
                    onError: (errors) => {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text:
                                errors.error ??
                                "Could not save payment code. Check your input if repeated.",
                        });
                    },
                }
            );
        }
    });
};

const showUpdatePaymentModal = (payment) => {
    selectedPayment.value = payment;

    paymentUpdateForm.payment_type_id = payment.payment_type_id;
    paymentUpdateForm.description = payment.description;
    (paymentUpdateForm.payment_date = new Date().toISOString().slice(0, 10)),
        (paymentUpdateForm.paid_amount = payment.paid_amount);
    paymentUpdateForm.status = payment.status;
    paymentUpdateForm.reference_number = payment.reference_number;

    updatePaymentModal.value = true;
};

const closeUpdatePaymentModal = () => {
    paymentUpdateForm.reset();
    selectedPayment.value = null;
    updatePaymentModal.value = false;
};
const unpaidEnrollments = props.student.enrollments.filter(
    (enrollment) => enrollment.status == "pending"
);

const closePayment = () => {
    paymentForm.reset();
    assignPayments.value = false;
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
    () => paymentUpdateForm.paid_amount,
    (newVal) => {
        console.log(selectedPayment);
        if (newVal == selectedPayment.value.total_amount) {
            paymentUpdateForm.status = "completed";
        } else {
            paymentUpdateForm.status = "pending";
        }
    }
);

watch(
    () => paymentCreationForm.status,
    (newVal) => {
        if (newVal == "completed") {
            paymentCreationForm.paid_amount = paymentCreationForm.total_amount;
        } else {
            paymentCreationForm.paid_amount =
                props.payments[0]?.paid_amount || 0;
        }
    }
);

watch(
    () => paymentUpdateForm.status,
    (newVal) => {
        if (newVal == "completed") {
            paymentUpdateForm.paid_amount = selectedPayment.value.total_amount;
        } else {
            paymentUpdateForm.paid_amount = props.payments[0]?.paid_amount || 0;
        }
    }
);

watch(
    () => paymentCreationForm.payment_type_id,
    () => {
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
    { label: "Pending", value: "pending" },
    { label: "Completed", value: "completed" },
    { label: "Canceled", value: "failed" },
];
const submitNewPayment = () => {
    paymentCreationForm.post(
        route("payments.store", { student: props.student.id }),
        {
            onSuccess: () => {
                paymentCreationForm.reset();
                // Clear all form fields manually
                paymentCreationForm.payment_method_id = null;
                paymentCreationForm.payment_type_id = null;
                paymentCreationForm.payment_date = new Date()
                    .toISOString()
                    .slice(0, 10);
                paymentCreationForm.paid_amount = null;
                paymentCreationForm.total_amount = null;
                paymentCreationForm.description = null;
                paymentCreationForm.status = null;
                paymentCreationForm.reference_number = null;
                Swal.fire({
                    title: "Success!",
                    text: "Payment has been created successfully.",
                    icon: "success",
                });

                createPaymentModal.value = false;
                paymentCreationForm.reset();
            },
            onError: (errors) => {
                // paymentCreationForm.reset();
                // createPaymentModal.value = false;
                Swal.fire({
                    title: "Error!",
                    text: errors.error ?? "Fill the Payment Form Properly",
                    icon: "error",
                    customClass: {
                        popup: "z-[1100]",
                    },
                });
            },
        }
    );
};

const updatePayment = () => {
    paymentUpdateForm.post(
        route("payments.update", { payment: selectedPayment.value.id }),
        {
            onSuccess: () => {
                console.log(paymentUpdateForm.paid_amount);

                paymentUpdateForm.reset();
                closeUpdatePaymentModal();
                Swal.fire(
                    "Success!",
                    "Payment has been created successfully.",
                    "success"
                );
            },
            onError: (errors) => {
                // paymentUpdateForm.reset();
                // createPaymentModal.value = false;
                Swal.fire({
                    title: "Error!",
                    text: errors.error ?? "Fill the Payment Form Properly",
                    icon: "error",
                    target: document.body,
                    customClass: {
                        popup: "z-[1100]",
                        backdrop: "z-[1099]",
                    },
                });
            },
        }
    );
};
</script>

<template>
    <div
        class="max-w-5xl mx-auto"
        v-if="userCanAny(['create-payments', 'update-payments'])"
    >
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ selectedStatus.toUpperCase() }} Payments Of
                {{
                    semesters.find(
                        (semester) => semester.id == selectedSemester
                    )?.name
                }}
                {{
                    semesters.find(
                        (semester) => semester.id == selectedSemester
                    )?.id == activeSemester.id
                        ? "(Current Semester)"
                        : ""
                }}
            </h2>
            <template v-if="student.paymentCode == null">
                <button
                    @click="addCode = true"
                    class="inline-flex items-center px-4 py-1 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 text-white text-sm font-bold shadow-md border border-blue-600 hover:from-blue-600 hover:to-blue-800 transition"
                >
                    <CogIcon class="w-5 h-5 mr-2" />
                    <span>Add Payment Code</span>
                </button>
            </template>
            <template v-else>
                <button
                    @click="createPaymentModal = !createPaymentModal"
                    class="flex items-center text-green-600 hover:text-green-800"
                >
                    <PlusCircleIcon class="mx-2 w-6 h-6" />
                    <span class="font-medium">Create Payment</span>
                </button>
            </template>
        </div>

        <div class="my-4 ml-4 flex items-center space-x-2">
            <template v-if="student.paymentCode">
                <span
                    class="inline-flex items-center px-4 py-3 py-1 rounded-full bg-gradient-to-r from-green-400 to-green-600 text-white text-sm font-bold shadow-md border border-green-500 animate-pulse"
                >
                    <svg
                        class="w-4 h-4 mr-2 text-white"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12l2 2l4 -4"
                        />
                    </svg>
                    Payment Code: {{ student.paymentCode }}
                </span>
            </template>
        </div>
        <!-- Filters -->
        <div class="mb-6 space-y-4">
            <div>
                <h3
                    class="text-lg font-semibold text-gray-800 dark:text-gray-100"
                >
                    Filters
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Narrow down payments based on status or semester.
                </p>
            </div>

            <div class="flex flex-wrap gap-6">
                <!-- Status Filter -->
                <div class="w-64">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Status
                    </label>
                    <Select
                        v-model="selectedStatus"
                        :options="statusOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select Status"
                        class="w-full"
                    />
                </div>

                <!-- Semester Filter -->
                <div class="w-64">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Semester
                    </label>
                    <Select
                        v-model="selectedSemester"
                        :options="semesters"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Select Semester"
                        class="w-full"
                    >
                        <template #option="slotProps">
                            {{ slotProps.option.name }}
                            <span class="text-sm">
                                {{
                                    slotProps.option.id == activeSemester.id
                                        ? "(Current Semester)"
                                        : ""
                                }}
                            </span>
                        </template>
                    </Select>
                </div>
            </div>
        </div>
        <transition
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="scale-75 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-75 opacity-0"
        >
            <div
                class="grid grid-cols-1 gap-4 mt-6"
                :key="`${selectedStatus}-${selectedSemester}`"
            >
                <div
                    v-for="(payment, index) in semesterPayments"
                    :key="payment.id"
                    class="rounded-2xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-5 shadow-sm transition hover:shadow-md"
                >
                    <!-- Summary -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h3
                                class="text-lg font-semibold text-gray-800 dark:text-gray-100"
                            >
                                {{
                                    payment.payment_type?.type || "Unknown Type"
                                }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ payment.semester?.name || "No Semester" }} |
                                {{
                                    new Date(
                                        payment.payment_date
                                    ).toLocaleDateString()
                                }}
                            </p>
                        </div>

                        <div class="text-right space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Total: {{ payment.total_amount }} ETB
                            </p>
                            <p
                                class="text-xl font-bold text-gray-800 dark:text-gray-100"
                            >
                                Paid: {{ payment.paid_amount }} ETB
                            </p>
                            <p
                                :class="{
                                    'text-yellow-500':
                                        payment.status === 'pending',
                                    'text-green-500':
                                        payment.status === 'completed',
                                    'text-red-500': payment.status === 'failed',
                                    'text-blue-500':
                                        payment.status === 'refunded',
                                }"
                                class="text-sm font-bold"
                            >
                                {{ paymentStatusLabels[payment.status] }}
                            </p>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div
                        class="mt-3 w-full bg-gray-300 dark:bg-gray-700 h-2 rounded-full overflow-hidden"
                    >
                        <div
                            class="h-full bg-green-500"
                            :style="{
                                width:
                                    payment.status == 'paid_by_college'
                                        ? '100%'
                                        : (payment.paid_amount /
                                              payment.total_amount) *
                                              100 +
                                          '%',
                            }"
                        ></div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex justify-between items-center">
                        <button
                            @click="payment.showDetails = !payment.showDetails"
                            class="text-sm text-blue-600 hover:underline dark:text-blue-400"
                        >
                            {{
                                payment.showDetails
                                    ? "Hide Details"
                                    : "Show Details"
                            }}
                        </button>
                        <div class="space-x-2 flex flex-wrap">
                            <Link
                                v-if="
                                    payment.status === 'completed' ||
                                    payment.status === 'paid_by_college'
                                "
                                :href="
                                    route('payments.show', {
                                        payment: payment.id,
                                    })
                                "
                                class="px-3 py-1 text-sm font-medium rounded-md bg-indigo-100 text-indigo-700 hover:bg-indigo-200 dark:bg-indigo-800 dark:text-white dark:hover:bg-indigo-700"
                            >
                                Receipt
                            </Link>
                            <button
                                v-if="payment.status === 'pending'"
                                @click="showUpdatePaymentModal(payment)"
                                class="px-3 py-1 text-sm font-medium rounded-md bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-800 dark:text-white dark:hover:bg-green-700"
                            >
                                Update Payment
                            </button>
                        </div>
                    </div>

                    <!-- Detail Section -->
                    <div
                        v-if="payment.showDetails"
                        class="mt-4 text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-xl p-4"
                    >
                        <p>
                            <strong>Method:</strong>
                            {{ payment.payment_method?.bank_name || "N/A" }}
                        </p>
                        <p>
                            <strong>Reference:</strong>
                            {{ payment.reference_number || "N/A" }}
                        </p>

                        <div
                            v-if="payment.payment_type.type == 'Course Fee'"
                            class="mb-6"
                        >
                            <h2
                                class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3"
                            >
                                Course Fees –
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
                                            <th
                                                scope="col"
                                                class="px-6 py-1 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                                            >
                                                Status
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
                                                {{
                                                    payment.payment_type.amount
                                                }}
                                            </td>
                                            <td
                                                class="px-6 py-2 text-sm text-yellow-700 dark:text-yellow-400"
                                            >
                                                {{ enrollment.status }}
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
                                                    payment.payment_type
                                                        .amount *
                                                    student.enrollments.length
                                                }}
                                            </td>
                                            <td
                                                colspan="2"
                                                class="px-6 py-2 text-gray-900 dark:text-gray-100"
                                            >
                                                {{ payment.status }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>

    <div v-else>Unavalilable!</div>
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
                {{ activeSemester.name }}
            </h1>
            <span class="text-red-700 text-lg">{{ errors.error }}</span>
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

    <Modal
        :show="updatePaymentModal"
        @close="closeUpdatePaymentModal"
        :maxWidth="'6xl'"
        class="p-6"
    >
        <div class="w-full px-8 py-6" v-if="selectedPayment">
            <h1
                class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100"
            >
                Update {{ selectedPayment.payment_type.type }} Payment for –
                {{ activeSemester.name }}
            </h1>
            <div class="flex flex-wrap gap-6 mb-6">
                <!-- Total Amount -->
                <div
                    class="flex-1 min-w-[180px] bg-blue-50 dark:bg-blue-900 rounded-xl p-4 shadow text-center"
                >
                    <div
                        class="text-xs uppercase text-blue-700 dark:text-blue-200 font-semibold mb-1"
                    >
                        Total Payment
                    </div>
                    <div
                        class="text-2xl font-bold text-blue-800 dark:text-blue-100"
                    >
                        {{ selectedPayment.total_amount }}
                        <span class="text-base font-normal">ETB</span>
                    </div>
                </div>
                <!-- Paid Amount -->
                <div
                    class="flex-1 min-w-[180px] bg-green-50 dark:bg-green-900 rounded-xl p-4 shadow text-center"
                >
                    <div
                        class="text-xs uppercase text-green-700 dark:text-green-200 font-semibold mb-1"
                    >
                        Already Paid
                    </div>
                    <div
                        class="text-2xl font-bold text-green-800 dark:text-green-100"
                    >
                        {{ selectedPayment.paid_amount }}
                        <span class="text-base font-normal">ETB</span>
                    </div>
                </div>
                <!-- Remaining Amount -->
                <div
                    class="flex-1 min-w-[180px] bg-yellow-50 dark:bg-yellow-900 rounded-xl p-4 shadow text-center"
                >
                    <div
                        class="text-xs uppercase text-yellow-700 dark:text-yellow-200 font-semibold mb-1"
                    >
                        Remaining Amount
                    </div>
                    <div
                        class="text-2xl font-bold text-yellow-800 dark:text-yellow-100"
                    >
                        {{
                            selectedPayment.total_amount -
                            selectedPayment.paid_amount
                        }}
                        <span class="text-base font-normal">ETB</span>
                    </div>
                </div>
            </div>

            <span class="text-red-700 text-lg">{{ errors.error }}</span>
            <form @submit.prevent="updatePayment">
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
                            v-model="paymentUpdateForm.payment_method_id"
                            :options="paymentMethods"
                            optionLabel="bank_name"
                            optionValue="id"
                            appendTo="self"
                            placeholder="Select Payment Method"
                            class="w-full"
                        />
                        <InputError
                            :message="
                                paymentUpdateForm.errors.payment_method_id
                            "
                            class="mt-2 text-sm text-red-500"
                        />
                    </div>

                    <!-- Payment Type -->
                    <div class="w-full md:w-1/2 px-2 mb-4">
                        <label
                            for="description"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >
                            Payment Description
                        </label>
                        <input
                            type="text"
                            v-model="paymentUpdateForm.description"
                            placeholder="Payment Description (eg Registration Fee, Tuition Fee...)"
                            id="description"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none focus:shadow-outline"
                        />
                        <InputError
                            :message="paymentUpdateForm.errors.description"
                            class="mt-2 text-sm text-red-500"
                        />
                    </div>
                </div>

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
                            v-model="paymentUpdateForm.status"
                            :options="statusOptions"
                            optionLabel="label"
                            optionValue="value"
                            appendTo="self"
                            placeholder="Select Status"
                            class="w-full"
                        />
                        <InputError
                            :message="paymentUpdateForm.errors.status"
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
                            v-if="paymentUpdateForm.status !== 'completed'"
                        >
                            <label
                                for="paid_amount"
                                class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                            >
                                Paid Amount
                            </label>
                            <input
                                type="number"
                                v-model.number="paymentUpdateForm.paid_amount"
                                :max="selectedPayment.total_amount"
                                min="0"
                                id="paid_amount"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none focus:shadow-outline"
                            />
                            <InputError
                                :message="paymentUpdateForm.errors.paid_amount"
                                class="mt-2 text-sm text-red-500"
                            />
                            <div
                                v-if="
                                    paymentUpdateForm.paid_amount >
                                    selectedPayment.total_amount
                                "
                                class="text-red-500 text-xs mt-1"
                            >
                                Paid amount cannot exceed the remaining amount
                                ({{
                                    selectedPayment.total_amount -
                                    selectedPayment.paid_amount
                                }}
                                ETB).
                            </div>
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
                            v-model="paymentUpdateForm.payment_date"
                            id="payment_date"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none focus:shadow-outline"
                        />
                        <InputError
                            :message="paymentUpdateForm.errors.payment_date"
                            class="mt-2 text-sm text-red-500"
                        />
                    </div>

                    <!-- Reference Number -->
                    <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-4">
                        <label
                            for="reference_number"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2"
                        >
                            Reference Number
                        </label>
                        <input
                            type="text"
                            v-model="paymentUpdateForm.reference_number"
                            id="reference_number"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none focus:shadow-outline"
                        />
                        <InputError
                            :message="paymentUpdateForm.errors.reference_number"
                            class="mt-2 text-sm text-red-500"
                        />
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end mt-4">
                    <button
                        type="submit"
                        :disabled="paymentUpdateForm.processing"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                    >
                        {{
                            paymentCreationForm.processing
                                ? "Saving..."
                                : "Save Payment"
                        }}
                    </button>

                    <button
                        type="button"
                        @click="closeUpdatePaymentModal"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                    >
                        Close
                    </button>
                </div>
            </form>
        </div>
    </Modal>

    <Modal :show="addCode" @close="closeCode" maxWidth="sm">
        <div class="px-6 py-4">
            <h2 class="text-lg font-bold mb-4">Add / Update Payment Code</h2>

            <form @submit.prevent="submitPaymentCode" class="space-y-4">
                <div>
                    <label
                        for="paymentCode"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Payment Code
                    </label>
                    <input
                        id="paymentCode"
                        type="text"
                        v-model="paymentCodeForm.paymentCode"
                        maxlength="20"
                        placeholder="e.g. ABC12345"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 shadow-sm focus:ring focus:ring-blue-300"
                    />
                    <p class="text-xs text-gray-500 mt-1">
                        This code should be unique to the student.
                    </p>
                    <InputError
                        :message="paymentCodeForm.errors.paymentCode"
                        class="mt-2"
                    />
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        @click="closeCode"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="paymentCodeForm.processing"
                        class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white"
                    >
                        {{ paymentCodeForm.processing ? "Saving..." : "Save" }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
