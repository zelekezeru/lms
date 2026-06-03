<script setup>
import { computed } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    AcademicCapIcon,
    CurrencyDollarIcon,
    ClipboardDocumentListIcon,
    BookOpenIcon,
    CalendarIcon,
    CreditCardIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});

// Calculate payment statistics
const totalPaid = computed(() => {
    if (!props.student.payments) return 0;
    return props.student.payments
        .filter((p) => p.status === "completed")
        .reduce((sum, p) => sum + parseFloat(p.total_amount || 0), 0);
});

const balanceDue = computed(() => {
    if (!props.student.payments) return 0;
    return props.student.payments
        .filter((p) => p.status === "pending")
        .reduce((sum, p) => sum + parseFloat(p.total_amount || 0), 0);
});

const lastPaymentDate = computed(() => {
    if (!props.student.payments) return "N/A";
    const completedPayments = props.student.payments.filter(
        (p) => p.status === "completed"
    );
    if (completedPayments.length === 0) return "N/A";
    const sorted = [...completedPayments].sort(
        (a, b) => new Date(b.payment_date) - new Date(a.payment_date)
    );
    const d = new Date(sorted[0].payment_date);
    return d.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});

// Identify next pending payment
const nextPendingPayment = computed(() => {
    if (!props.student.payments) return null;
    const pendingList = props.student.payments.filter(
        (p) => p.status === "pending"
    );
    if (pendingList.length === 0) return null;
    return [...pendingList].sort(
        (a, b) => new Date(a.payment_date) - new Date(b.payment_date)
    )[0];
});
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Paid Card -->
                <div
                    class="relative overflow-hidden bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm flex items-center space-x-4 hover:shadow-md transition duration-350"
                >
                    <!-- Background Soft Glow -->
                    <div class="absolute top-[-40px] right-[-40px] w-24 h-24 bg-green-500/10 rounded-full blur-2xl"></div>

                    <div class="p-3.5 bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400 rounded-2xl">
                        <CurrencyDollarIcon class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Total Paid
                        </p>
                        <p class="text-2xl font-black text-green-600 dark:text-green-400 mt-0.5">
                            ${{ totalPaid.toFixed(2) }}
                        </p>
                    </div>
                </div>

                <!-- Balance Due Card -->
                <div
                    class="relative overflow-hidden bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm flex items-center space-x-4 hover:shadow-md transition duration-350"
                >
                    <!-- Background Soft Glow -->
                    <div class="absolute top-[-40px] right-[-40px] w-24 h-24 bg-red-500/10 rounded-full blur-2xl"></div>

                    <div class="p-3.5 bg-red-50 dark:bg-red-950/40 text-red-650 dark:text-red-400 rounded-2xl">
                        <CreditCardIcon class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Balance Due
                        </p>
                        <p class="text-2xl font-black text-red-650 dark:text-red-400 mt-0.5">
                            ${{ balanceDue.toFixed(2) }}
                        </p>
                    </div>
                </div>

                <!-- Last Payment Card -->
                <div
                    class="relative overflow-hidden bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm flex items-center space-x-4 hover:shadow-md transition duration-350"
                >
                    <!-- Background Soft Glow -->
                    <div class="absolute top-[-40px] right-[-40px] w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl"></div>

                    <div class="p-3.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 rounded-2xl">
                        <CalendarIcon class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Last Payment
                        </p>
                        <p class="text-lg font-bold text-gray-850 dark:text-gray-100 mt-1">
                            {{ lastPaymentDate }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Payments -->
            <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm space-y-6">
                <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700/60 pb-4">
                    <div class="flex items-center space-x-3">
                        <BookOpenIcon class="h-6 w-6 text-indigo-500" />
                        <h2
                            class="text-lg font-black text-gray-900 dark:text-white"
                        >
                            Upcoming Invoices
                        </h2>
                    </div>
                    <button
                        v-if="nextPendingPayment"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 text-sm font-semibold rounded-2xl hover:shadow-lg transition duration-250"
                    >
                        Make Payment
                    </button>
                </div>
                
                <div v-if="nextPendingPayment" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 pt-2">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Due Date</span>
                        <p class="text-sm font-bold text-yellow-600 dark:text-yellow-400 mt-1 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-yellow-500 block animate-pulse"></span>
                            {{ new Date(nextPendingPayment.payment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Amount Due</span>
                        <p
                            class="text-base font-black text-gray-850 dark:text-gray-100 mt-1"
                        >
                            ${{ parseFloat(nextPendingPayment.total_amount).toFixed(2) }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Invoice Classification</span>
                        <p
                            class="text-base font-bold text-gray-850 dark:text-gray-100 mt-1"
                        >
                            {{ nextPendingPayment.paymentType?.name || 'Tuition Fee' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Approval Status</span>
                        <p class="text-sm font-bold text-orange-500 mt-1.5">
                            Pending Clearance
                        </p>
                    </div>
                </div>
                <div v-else class="text-center py-6 text-gray-500 dark:text-gray-400">
                    🎉 No upcoming invoices found. Your billing account is fully cleared!
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm space-y-6">
                <div class="flex items-center space-x-3 mb-2">
                    <ClipboardDocumentListIcon class="h-6 w-6 text-indigo-500" />
                    <h2
                        class="text-lg font-black text-gray-900 dark:text-white"
                    >
                        Transaction Ledger
                    </h2>
                </div>
                <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-750">
                    <table
                        class="w-full text-left"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-700/30">
                            <tr>
                                <th
                                    class="py-3.5 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                >
                                    Billing Date
                                </th>
                                <th
                                    class="py-3.5 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                >
                                    Total Amount
                                </th>
                                <th
                                    class="py-3.5 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider"
                                >
                                    Payment Method
                                </th>
                                <th
                                    class="py-3.5 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right"
                                >
                                    Clearance Status
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            v-if="student.payments && student.payments.length > 0"
                            class="divide-y divide-gray-100 dark:divide-gray-700/50"
                        >
                            <tr
                                v-for="payment in student.payments"
                                :key="payment.id"
                                class="hover:bg-gray-55/30 dark:hover:bg-gray-700/10 transition duration-150 animate-fade-in"
                            >
                                <td
                                    class="py-4 px-6 text-sm text-gray-800 dark:text-gray-200"
                                >
                                    {{ new Date(payment.payment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) }}
                                </td>
                                <td class="py-4 px-6 text-sm text-green-650 dark:text-green-400 font-black">
                                    ${{ parseFloat(payment.total_amount).toFixed(2) }}
                                </td>
                                <td
                                    class="py-4 px-6 text-sm text-gray-750 dark:text-gray-300"
                                >
                                    {{ payment.paymentMethod?.name || 'Credit Card' }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <span
                                        class="px-3 py-1 text-xs font-bold rounded-full"
                                        :class="
                                            payment.status === 'completed'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-950/50 dark:text-green-300'
                                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950/50 dark:text-yellow-300'
                                        "
                                    >
                                        {{ payment.status === 'completed' ? 'Cleared' : 'Pending Verification' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="4" class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    No transaction ledger records found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
