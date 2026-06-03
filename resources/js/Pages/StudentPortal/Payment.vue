<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    CurrencyDollarIcon,
    CheckCircleIcon,
    ClockIcon,
    ArrowDownTrayIcon,
    ExclamationTriangleIcon,
    CalendarIcon,
} from "@heroicons/vue/24/outline";
import { CheckCircleIcon as CheckSolid } from "@heroicons/vue/24/solid";

const props = defineProps({
    student: { type: Object, required: false },
    payments: { type: Array, default: () => [] },
});

// Compute totals from real data if available
const paidPayments = props.payments.filter(p => p.status === 'completed' || p.status === 'paid');
const pendingPayments = props.payments.filter(p => p.status === 'pending');

const totalPaid = paidPayments.reduce((acc, p) => acc + parseFloat(p.amount ?? 0), 0);
const totalPending = pendingPayments.reduce((acc, p) => acc + parseFloat(p.amount ?? 0), 0);
const lastPayment = paidPayments.at(-1);

const statusStyle = (status) => {
    if (status === 'completed' || status === 'paid')
        return "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400";
    if (status === 'pending')
        return "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400";
    return "bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400";
};

const statusIcon = (status) => {
    if (status === 'completed' || status === 'paid') return CheckSolid;
    return ClockIcon;
};

const fmt = (val) => {
    const n = parseFloat(val);
    if (isNaN(n)) return '—';
    return n.toLocaleString("en-US", { minimumFractionDigits: 2 });
};
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-3xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Header -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                    <CurrencyDollarIcon class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Payments</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Your payment history &amp; status</p>
                </div>
            </div>

            <!-- Summary banner -->
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-36 h-36 rounded-full bg-white/10 pointer-events-none"></div>

                <p class="text-emerald-100 text-xs font-medium uppercase tracking-wider mb-1">Total Paid</p>
                <p class="text-3xl font-bold mb-4">
                    {{ fmt(totalPaid) }} <span class="text-lg font-normal text-emerald-200">ETB</span>
                </p>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white/15 rounded-xl p-3 backdrop-blur-sm">
                        <p class="text-xs text-emerald-100 mb-0.5">Outstanding</p>
                        <p class="font-bold text-lg">{{ fmt(totalPending) }}</p>
                    </div>
                    <div class="bg-white/15 rounded-xl p-3 backdrop-blur-sm">
                        <p class="text-xs text-emerald-100 mb-0.5">Last Payment</p>
                        <p class="font-bold text-sm">{{ lastPayment?.created_at?.split('T')[0] ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending alert -->
            <div
                v-if="pendingPayments.length"
                class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-4"
            >
                <ExclamationTriangleIcon class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                        {{ pendingPayments.length }} pending payment{{ pendingPayments.length !== 1 ? 's' : '' }}
                    </p>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                        Total due: <strong>{{ fmt(totalPending) }} ETB</strong>. Please complete your payment to avoid enrollment suspension.
                    </p>
                </div>
            </div>

            <!-- Payment list -->
            <section>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Payment History</h2>

                <div v-if="payments.length" class="space-y-3">
                    <div
                        v-for="payment in [...payments].reverse()"
                        :key="payment.id"
                        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden"
                    >
                        <div class="flex items-center gap-4 p-4">
                            <!-- Status icon -->
                            <div
                                :class="[
                                    'w-11 h-11 rounded-xl flex items-center justify-center shrink-0',
                                    payment.status === 'completed' || payment.status === 'paid'
                                        ? 'bg-emerald-100 dark:bg-emerald-900/30'
                                        : 'bg-amber-100 dark:bg-amber-900/30',
                                ]"
                            >
                                <component
                                    :is="statusIcon(payment.status)"
                                    :class="[
                                        'w-5 h-5',
                                        payment.status === 'completed' || payment.status === 'paid'
                                            ? 'text-emerald-600 dark:text-emerald-400'
                                            : 'text-amber-600 dark:text-amber-400',
                                    ]"
                                />
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ payment.paymentType?.name ?? payment.type ?? 'Payment' }}
                                </p>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                        <CalendarIcon class="w-3.5 h-3.5" />
                                        {{ payment.created_at?.split('T')[0] ?? payment.date ?? '—' }}
                                    </span>
                                    <span v-if="payment.paymentMethod?.name" class="text-xs text-gray-400">
                                        · {{ payment.paymentMethod.name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Amount + badge -->
                            <div class="text-right shrink-0">
                                <p
                                    :class="[
                                        'text-base font-bold',
                                        payment.status === 'completed' || payment.status === 'paid'
                                            ? 'text-emerald-600 dark:text-emerald-400'
                                            : 'text-amber-600 dark:text-amber-400',
                                    ]"
                                >
                                    {{ fmt(payment.amount) }}
                                </p>
                                <span :class="['text-xs font-medium rounded-full px-2 py-0.5 mt-1 inline-block', statusStyle(payment.status)]">
                                    {{ payment.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-else class="text-center py-12">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                        <CurrencyDollarIcon class="w-7 h-7 text-gray-400" />
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">No payment records found</p>
                </div>
            </section>

        </div>
    </AppLayout>
</template>
