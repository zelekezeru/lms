<script setup>
import { Link, useForm, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, Select } from "primevue";
import { computed, defineProps, ref, watch } from "vue";
import InputError from "@/Components/InputError.vue";
import { CogIcon, PlusCircleIcon } from "@heroicons/vue/24/solid";

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
    payment_reference: null,
});

const paymentUpdateForm = useForm({
    payment_method_id: null,
    payment_type_id: props.payments[0]?.payment_type_id || null,
    description: props.payments[0]?.description || "",
    status: props.payments[0]?.status || "pending",
    paid_amount: props.payments[0]?.paid_amount || 0,
    payment_date: new Date().toISOString().slice(0, 10),
    payment_reference: props.payments[0]?.payment_reference || "",
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
    paymentUpdateForm.payment_date = new Date().toISOString().slice(0, 10);
    paymentUpdateForm.paid_amount = payment.paid_amount;
    paymentUpdateForm.status = payment.status;
    paymentUpdateForm.payment_reference = payment.payment_reference;

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
        if (selectedPayment.value != null) {
            if (newVal == selectedPayment.value.total_amount) {
                paymentUpdateForm.status = "completed";
            } else {
                paymentUpdateForm.status = "pending";
            }
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
        if (selectedPaymentType.value && newVal == "completed") {
            paymentUpdateForm.paid_amount = selectedPayment.value.total_amount;
        } else {
            paymentUpdateForm.paid_amount =
                selectedPayment.value?.paid_amount || 0;
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
                paymentCreationForm.payment_reference = null;
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
        class="max-w-5xl mx-auto space-y-6"
        v-if="userCanAny(['create-payments', 'update-payments'])"
    >
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-700 pb-5">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    Payments &amp; Transactions
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Showing {{ selectedStatus ? selectedStatus : 'all' }} bills for 
                    <span class="font-bold text-gray-700 dark:text-gray-300">
                        {{ semesters.find(s => s.id == selectedSemester)?.name }}
                    </span>
                    {{ semesters.find(s => s.id == selectedSemester)?.id == activeSemester.id ? "(Current Semester)" : "" }}
                </p>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-3">
                <template v-if="student.paymentCode == null">
                    <button
                        @click="addCode = true"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-150 dark:border-indigo-800 rounded-xl text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100/70 transition shadow-sm"
                        aria-label="Add Payment Code"
                    >
                        <CogIcon class="w-4 h-4" />
                        Add Payment Code
                    </button>
                </template>
                <template v-else>
                    <button
                        @click="createPaymentModal = true"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-sm transition active:scale-95"
                    >
                        <PlusCircleIcon class="w-4.5 h-4.5" />
                        Create Payment
                    </button>
                </template>
            </div>
        </div>

        <!-- Payment Code Indicator -->
        <div v-if="student.paymentCode" class="flex">
            <span
                class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 text-xs font-bold border border-emerald-100 dark:border-emerald-900/40 shadow-sm"
            >
                <svg
                    class="w-4 h-4 mr-2 text-emerald-550 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
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
        </div>

        <!-- Filters Grid -->
        <div class="bg-white dark:bg-gray-800/60 backdrop-blur-md rounded-2xl border border-gray-150 dark:border-gray-700/80 p-5 shadow-sm space-y-4">
            <div>
                <h3 class="text-xs font-bold text-gray-400 dark:text-gray-555 uppercase tracking-wider">
                    Filters &amp; Scopes
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Select semester or payment status to narrow down records.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-bold text-gray-550 dark:text-gray-450 mb-2 uppercase tracking-wide">
                        Payment Status
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
                <div>
                    <label class="block text-xs font-bold text-gray-550 dark:text-gray-450 mb-2 uppercase tracking-wide">
                        Semester Scope
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
                            <span class="text-xs text-indigo-500 dark:text-indigo-400 ml-1.5" v-if="slotProps.option.id == activeSemester.id">
                                (Current)
                            </span>
                        </template>
                    </Select>
                </div>
            </div>
        </div>

        <!-- Payments List -->
        <transition
            mode="out-in"
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="scale-95 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
        >
            <div
                class="space-y-4"
                :key="`${selectedStatus}-${selectedSemester}`"
            >
                <div v-if="semesterPayments.length" class="space-y-3">
                    <div
                        v-for="payment in semesterPayments"
                        :key="payment.id"
                        class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-150 dark:border-gray-700/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden"
                    >
                        <!-- Main Card Info -->
                        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1.5">
                                <span :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider border',
                                    payment.status === 'completed' || payment.status === 'paid_by_college'
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-950/20 dark:text-emerald-450 dark:border-emerald-900/40'
                                        : payment.status === 'pending'
                                        ? 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-950/20 dark:text-amber-450 dark:border-amber-900/40'
                                        : 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-950/20 dark:text-rose-450 dark:border-rose-900/40'
                                ]">
                                    {{ paymentStatusLabels[payment.status] || payment.status }}
                                </span>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ payment.payment_type?.type || "General Bill" }}
                                </h3>
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ payment.semester?.name || "No Semester Scope" }} &middot; 
                                    {{ new Date(payment.payment_date).toLocaleDateString() }}
                                </p>
                            </div>

                            <div class="text-left md:text-right shrink-0">
                                <p class="text-xs text-gray-450">
                                    Invoice Total: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ payment.total_amount }} ETB</span>
                                </p>
                                <p class="text-lg font-extrabold text-gray-900 dark:text-white mt-1">
                                    Paid Amount: <span class="text-emerald-600 dark:text-emerald-400">{{ payment.paid_amount }} ETB</span>
                                </p>
                            </div>
                        </div>

                        <!-- Custom Progress Bar -->
                        <div class="px-5">
                            <div class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-emerald-500 rounded-full"
                                    :style="{
                                        width: payment.status == 'paid_by_college'
                                                ? '100%'
                                                : (payment.paid_amount / payment.total_amount) * 100 + '%'
                                    }"
                                ></div>
                            </div>
                        </div>

                        <!-- Card Action Bar -->
                        <div class="px-5 py-3.5 bg-gray-50/50 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-3">
                            <button
                                @click="payment.showDetails = !payment.showDetails"
                                class="inline-flex items-center text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition"
                            >
                                {{ payment.showDetails ? "Hide Details" : "Show Breakdown" }}
                            </button>
                            <div class="flex items-center gap-2">
                                <Link
                                    v-if="payment.status === 'completed' || payment.status === 'paid_by_college'"
                                    :href="route('payments.show', { payment: payment.id })"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100/70 border border-indigo-100 dark:border-indigo-800 rounded-xl transition"
                                >
                                    Receipt
                                </Link>
                                <button
                                    v-if="payment.status === 'pending'"
                                    @click="showUpdatePaymentModal(payment)"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900 rounded-xl transition"
                                >
                                    Update Payment
                                </button>
                            </div>
                        </div>

                        <!-- Nested Details Section -->
                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-2"
                        >
                            <div
                                v-if="payment.showDetails"
                                class="px-5 pb-5 border-t border-gray-100 dark:border-gray-700/60 pt-4 space-y-3"
                            >
                                <div class="grid grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <p class="text-gray-400 dark:text-gray-500 font-medium">Payment Method</p>
                                        <p class="font-bold text-gray-900 dark:text-white mt-0.5">{{ payment.payment_method?.bank_name || "Bank Transfer" }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-400 dark:text-gray-500 font-medium">Reference Number</p>
                                        <p class="font-mono font-bold text-gray-900 dark:text-white mt-0.5">{{ payment.payment_reference || "N/A" }}</p>
                                    </div>
                                </div>

                                <!-- Course Fees Table -->
                                <div v-if="payment.payment_type.type == 'Course Fee'" class="space-y-2 pt-2">
                                    <h4 class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Course Fees Breakdown</h4>
                                    <div class="overflow-x-auto rounded-xl border border-gray-150 dark:border-gray-700">
                                        <table class="w-full text-left text-xs">
                                            <thead class="bg-gray-55/60 dark:bg-gray-800/80">
                                                <tr class="border-b border-gray-150 dark:border-gray-750">
                                                    <th class="px-4 py-2.5 font-bold text-gray-400 dark:text-gray-500">Course</th>
                                                    <th class="px-4 py-2.5 font-bold text-gray-400 dark:text-gray-500">Fee Amount</th>
                                                    <th class="px-4 py-2.5 font-bold text-gray-400 dark:text-gray-500">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800/40">
                                                <tr
                                                    v-for="enrollment in unpaidEnrollments"
                                                    :key="enrollment.course.id"
                                                    class="hover:bg-gray-50/50 dark:hover:bg-gray-750/10"
                                                >
                                                    <td class="px-4 py-2 font-semibold text-gray-900 dark:text-white">
                                                        {{ enrollment.course.name }}
                                                        <span class="text-[10px] text-gray-400 dark:text-gray-550 block font-normal">{{ enrollment.course.code }}</span>
                                                    </td>
                                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300 font-medium">
                                                        {{ payment.payment_type.amount }} ETB
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-100/50 dark:border-amber-900/30 uppercase tracking-wide">
                                                            {{ enrollment.status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Totals Row -->
                                                <tr class="bg-indigo-50/20 dark:bg-indigo-950/10 font-bold border-t-2 border-indigo-100/50 dark:border-indigo-900/40">
                                                    <td class="px-4 py-3 text-gray-900 dark:text-white">Total Breakdown</td>
                                                    <td class="px-4 py-3 text-indigo-650 dark:text-indigo-400 text-sm font-extrabold">
                                                        {{ payment.payment_type.amount * student.enrollments.length }} ETB
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-500 uppercase tracking-wider text-[10px]">
                                                        {{ payment.status }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16 bg-white dark:bg-gray-800/40 rounded-2xl border border-dashed border-gray-250 dark:border-gray-700/80">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-850 flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-800">
                        <CogIcon class="w-6 h-6 text-gray-400 dark:text-gray-550" />
                    </div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">No Payments Found</p>
                    <p class="text-xs text-gray-450 mt-1">There are no payment invoices recorded matching the filters.</p>
                </div>
            </div>
        </transition>
    </div>
    <div v-else class="text-center py-12 text-sm text-gray-450 italic">
        Access Denied. You do not have permission to view payments.
    </div>

    <!-- ── Add Payment Code Modal ───────────────────────────── -->
    <Modal :show="addCode" @close="closeCode" maxWidth="sm">
        <div class="p-6 space-y-6 bg-gradient-to-br from-white via-indigo-50/20 to-indigo-100/10 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <CogIcon class="w-5 h-5 text-indigo-500" />
                Add / Update Payment Code
            </h2>

            <form @submit.prevent="submitPaymentCode" class="space-y-4">
                <div>
                    <label
                        for="paymentCode"
                        class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
                    >
                        Payment Code
                    </label>
                    <input
                        id="paymentCode"
                        type="text"
                        v-model="paymentCodeForm.paymentCode"
                        maxlength="20"
                        placeholder="e.g. ABC12345"
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-950 dark:text-white focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 transition py-2.5 px-3.5 text-sm"
                    />
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2">
                        Must be a unique identifier sequence for tracking bank payments.
                    </p>
                    <InputError
                        :message="paymentCodeForm.errors.paymentCode"
                        class="mt-2"
                    />
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button
                        type="button"
                        @click="closeCode"
                        class="px-4 py-2 text-xs font-bold bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl text-gray-700 dark:text-gray-200 transition"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="paymentCodeForm.processing"
                        class="inline-flex items-center px-4 py-2 text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition"
                    >
                        {{ paymentCodeForm.processing ? "Saving..." : "Save Code" }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>

    <!-- ── Create Payment Modal ──────────────────────────────── -->
    <Modal
        :show="createPaymentModal"
        @close="closeCreatePaymentModal"
        :maxWidth="'5xl'"
    >
        <div class="p-6 space-y-6 bg-gradient-to-br from-white via-indigo-50/20 to-indigo-100/10 dark:from-gray-800 dark:via-gray-900 dark:to-gray-805 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <PlusCircleIcon class="w-5.5 h-5.5 text-emerald-500" />
                Create New Payment &mdash; {{ activeSemester.name }}
            </h2>

            <form @submit.prevent="submitNewPayment" class="space-y-4">
                <!-- Description -->
                <div>
                    <label
                        for="narration"
                        class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
                    >
                        Payment Description
                    </label>
                    <input
                        type="text"
                        v-model="paymentCreationForm.description"
                        placeholder="Payment Description (e.g. Registration Fee, Course Fee)"
                        id="narration"
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-950 dark:text-white focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 transition py-2.5 px-3.5 text-sm"
                    />
                    <InputError
                        :message="paymentCreationForm.errors.narration"
                        class="mt-2 text-xs text-red-500"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Payment Method -->
                    <div>
                        <label
                            for="payment_method_id"
                            class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
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
                            :message="paymentCreationForm.errors.payment_method_id"
                            class="mt-2 text-xs text-red-500"
                        />
                    </div>

                    <!-- Payment Type -->
                    <div>
                        <label
                            for="payment_type_id"
                            class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
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
                            :message="paymentCreationForm.errors.payment_type_id"
                            class="mt-2 text-xs text-red-500"
                        />
                    </div>
                </div>

                <!-- Course Breakdown Nested Table inside creation form -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 -translate-y-3"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-3"
                >
                    <div v-if="perCoursePaymentType && selectedPaymentType" class="bg-gray-50/50 dark:bg-gray-900/30 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-250 uppercase tracking-wider mb-2">Unpaid Courses Breakdown</h4>
                        <div class="overflow-x-auto rounded-xl border border-gray-150 dark:border-gray-800">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-100/60 dark:bg-gray-850/80">
                                    <tr class="border-b border-gray-150 dark:border-gray-700">
                                        <th class="px-4 py-2 font-bold text-gray-400 dark:text-gray-500">Course</th>
                                        <th class="px-4 py-2 font-bold text-gray-400 dark:text-gray-500">Fee Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-750">
                                    <tr v-for="enrollment in unpaidEnrollments" :key="enrollment.course.id">
                                        <td class="px-4 py-2 font-semibold text-gray-900 dark:text-white">{{ enrollment.course.name }}</td>
                                        <td class="px-4 py-2 text-gray-650 dark:text-gray-300">{{ selectedPaymentType.amount }} ETB</td>
                                    </tr>
                                    <tr class="bg-indigo-50/20 dark:bg-indigo-950/15 font-bold border-t border-indigo-100 dark:border-indigo-900/30">
                                        <td class="px-4 py-2">Total Amount</td>
                                        <td class="px-4 py-2 text-indigo-650 dark:text-indigo-400">{{ selectedPaymentType.amount * unpaidEnrollments.length }} ETB</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-else-if="selectedPaymentType" class="bg-gray-50/50 dark:bg-gray-900/30 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                        <h4 class="text-xs font-bold text-gray-800 dark:text-gray-250 uppercase tracking-wider mb-2">Fee Configuration</h4>
                        <div class="overflow-x-auto rounded-xl border border-gray-150 dark:border-gray-800">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-100/60 dark:bg-gray-850/80">
                                    <tr class="border-b border-gray-150 dark:border-gray-700">
                                        <th class="px-4 py-2 font-bold text-gray-400 dark:text-gray-500">Type</th>
                                        <th class="px-4 py-2 font-bold text-gray-400 dark:text-gray-500">Fee Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-750">
                                    <tr>
                                        <td class="px-4 py-2 font-semibold text-gray-900 dark:text-white">{{ selectedPaymentType.type }}</td>
                                        <td class="px-4 py-2 text-gray-650 dark:text-gray-300">{{ selectedPaymentType.amount }} ETB</td>
                                    </tr>
                                    <tr class="bg-indigo-50/20 dark:bg-indigo-950/15 font-bold border-t border-indigo-100 dark:border-indigo-900/30">
                                        <td class="px-4 py-2">Total Amount</td>
                                        <td class="px-4 py-2 text-indigo-650 dark:text-indigo-400">{{ selectedPaymentType.amount }} ETB</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </Transition>

                <!-- Status, Date, Amount, Reference Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
                    <!-- Status -->
                    <div>
                        <label
                            for="status"
                            class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
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
                            class="mt-2 text-xs text-red-500"
                        />
                    </div>

                    <!-- Paid Amount -->
                    <Transition
                        enter-active-class="transition ease-out duration-300"
                        enter-from-class="opacity-0 -translate-x-3"
                        enter-to-class="opacity-100 translate-x-0"
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100 translate-x-0"
                        leave-to-class="opacity-0 -translate-x-3"
                    >
                        <div v-if="paymentCreationForm.status !== 'completed'">
                            <label
                                for="paid_amount"
                                class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
                            >
                                Paid Amount
                            </label>
                            <input
                                type="number"
                                v-model="paymentCreationForm.paid_amount"
                                id="paid_amount"
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-950 dark:text-white focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 transition py-2.5 px-3.5 text-sm"
                            />
                            <InputError
                                :message="paymentCreationForm.errors.paid_amount"
                                class="mt-2 text-xs text-red-500"
                            />
                        </div>
                    </Transition>

                    <!-- Payment Date -->
                    <div>
                        <label
                            for="payment_date"
                            class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
                        >
                            Payment Date
                        </label>
                        <input
                            type="date"
                            v-model="paymentCreationForm.payment_date"
                            id="payment_date"
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-950 dark:text-white focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 transition py-2.5 px-3.5 text-sm"
                        />
                        <InputError
                            :message="paymentCreationForm.errors.payment_date"
                            class="mt-2 text-xs text-red-500"
                        />
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label
                            for="payment_reference"
                            class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
                        >
                            Reference Number
                        </label>
                        <input
                            type="text"
                            v-model="paymentCreationForm.payment_reference"
                            id="payment_reference"
                            placeholder="Optional"
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-950 dark:text-white focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 transition py-2.5 px-3.5 text-sm"
                        />
                        <InputError
                            :message="paymentCreationForm.errors.payment_reference"
                            class="mt-2 text-xs text-red-500"
                        />
                    </div>
                </div>

                <!-- Footer buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button
                        type="button"
                        @click="closeCreatePaymentModal"
                        class="px-4 py-2 text-xs font-bold bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl text-gray-700 dark:text-gray-200 transition"
                    >
                        Close
                    </button>
                    <button
                        type="submit"
                        :disabled="paymentCreationForm.processing"
                        class="inline-flex items-center px-4 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-sm transition disabled:opacity-60"
                    >
                        {{ paymentCreationForm.processing ? "Creating..." : "Create Payment" }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>

    <!-- ── Update Payment Modal ──────────────────────────────── -->
    <Modal
        :show="updatePaymentModal"
        @close="closeUpdatePaymentModal"
        :maxWidth="'5xl'"
    >
        <div class="p-6 space-y-6 bg-gradient-to-br from-white via-indigo-50/20 to-indigo-100/10 dark:from-gray-800 dark:via-gray-900 dark:to-gray-805 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700" v-if="selectedPayment">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <CogIcon class="w-5.5 h-5.5 text-indigo-500" />
                Update {{ selectedPayment.payment_type?.type || 'General' }} Payment &mdash; {{ activeSemester.name }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Total Amount -->
                <div class="bg-indigo-50/45 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 rounded-xl p-4 text-center">
                    <div class="text-[10px] uppercase text-indigo-700 dark:text-indigo-400 font-bold mb-1">Total Invoice</div>
                    <div class="text-2xl font-extrabold text-indigo-850 dark:text-indigo-200">
                        {{ selectedPayment.total_amount }} <span class="text-xs font-normal">ETB</span>
                    </div>
                </div>
                <!-- Paid Amount -->
                <div class="bg-emerald-50/45 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 rounded-xl p-4 text-center">
                    <div class="text-[10px] uppercase text-emerald-700 dark:text-emerald-400 font-bold mb-1">Already Paid</div>
                    <div class="text-2xl font-extrabold text-emerald-850 dark:text-emerald-200">
                        {{ selectedPayment.paid_amount }} <span class="text-xs font-normal">ETB</span>
                    </div>
                </div>
                <!-- Remaining Amount -->
                <div class="bg-amber-50/45 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/40 rounded-xl p-4 text-center">
                    <div class="text-[10px] uppercase text-amber-700 dark:text-amber-400 font-bold mb-1">Outstanding Balance</div>
                    <div class="text-2xl font-extrabold text-amber-850 dark:text-amber-250">
                        {{ selectedPayment.total_amount - selectedPayment.paid_amount }} <span class="text-xs font-normal">ETB</span>
                    </div>
                </div>
            </div>

            <span class="text-red-700 text-sm block">{{ errors.error }}</span>

            <form @submit.prevent="updatePayment" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Payment Method -->
                    <div>
                        <label
                            for="payment_method_id"
                            class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
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
                            :message="paymentUpdateForm.errors.payment_method_id"
                            class="mt-2 text-xs text-red-500"
                        />
                    </div>

                    <!-- Payment Description -->
                    <div>
                        <label
                            for="description"
                            class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
                        >
                            Payment Description
                        </label>
                        <input
                            type="text"
                            v-model="paymentUpdateForm.description"
                            placeholder="Payment Description (e.g. Tuition Fee)"
                            id="description"
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-950 dark:text-white focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 transition py-2.5 px-3.5 text-sm"
                        />
                        <InputError
                            :message="paymentUpdateForm.errors.description"
                            class="mt-2 text-xs text-red-500"
                        />
                    </div>
                </div>

                <!-- Last Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Status -->
                    <div>
                        <label
                            for="status"
                            class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
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
                            class="mt-2 text-xs text-red-500"
                        />
                    </div>

                    <!-- Paid Amount -->
                    <Transition
                        enter-active-class="transition ease-out duration-300"
                        enter-from-class="opacity-0 -translate-x-3"
                        enter-to-class="opacity-100 translate-x-0"
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100 translate-x-0"
                        leave-to-class="opacity-0 -translate-x-3"
                    >
                        <div v-if="paymentUpdateForm.status !== 'completed'">
                            <label
                                for="paid_amount"
                                class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
                            >
                                Paid Amount
                            </label>
                            <input
                                type="number"
                                v-model.number="paymentUpdateForm.paid_amount"
                                :max="selectedPayment.total_amount"
                                min="0"
                                id="paid_amount"
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-955 dark:text-white focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 transition py-2.5 px-3.5 text-sm"
                            />
                            <InputError
                                :message="paymentUpdateForm.errors.paid_amount"
                                class="mt-2 text-xs text-red-500"
                            />
                            <div
                                v-if="paymentUpdateForm.paid_amount > selectedPayment.total_amount"
                                class="text-rose-500 text-[10px] mt-1"
                            >
                                Paid amount cannot exceed {{ selectedPayment.total_amount }} ETB.
                            </div>
                        </div>
                    </Transition>

                    <!-- Payment Date -->
                    <div>
                        <label
                            for="payment_date"
                            class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
                        >
                            Payment Date
                        </label>
                        <input
                            type="date"
                            v-model="paymentUpdateForm.payment_date"
                            id="payment_date"
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-950 dark:text-white focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 transition py-2.5 px-3.5 text-sm"
                        />
                        <InputError
                            :message="paymentUpdateForm.errors.payment_date"
                            class="mt-2 text-xs text-red-500"
                        />
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label
                            for="payment_reference"
                            class="block text-xs font-bold text-gray-450 uppercase tracking-wider mb-2"
                        >
                            Reference Number
                        </label>
                        <input
                            type="text"
                            v-model="paymentUpdateForm.payment_reference"
                            id="payment_reference"
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-950 dark:text-white focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500 transition py-2.5 px-3.5 text-sm"
                        />
                        <InputError
                            :message="paymentUpdateForm.errors.payment_reference"
                            class="mt-2 text-xs text-red-500"
                        />
                    </div>
                </div>

                <!-- Footer buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button
                        type="button"
                        @click="closeUpdatePaymentModal"
                        class="px-4 py-2 text-xs font-bold bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl text-gray-700 dark:text-gray-200 transition"
                    >
                        Close
                    </button>
                    <button
                        type="submit"
                        :disabled="paymentUpdateForm.processing"
                        class="inline-flex items-center px-4 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-sm transition disabled:opacity-60"
                    >
                        {{ paymentUpdateForm.processing ? "Saving..." : "Save Payment" }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
