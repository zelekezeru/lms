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
        <div class="pb-24 md:pb-8 max-w-3xl mx-auto px-4 md:px-6 pt-6 space-y-6">

            <!-- Header -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/50 flex items-center justify-center">
                    <CurrencyDollarIcon class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Payments &amp; Billings</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Manage invoices, payment history, and account balances</p>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="bg-gradient-to-br from-emerald-550 via-emerald-600 to-teal-700 dark:from-emerald-650 dark:to-teal-850 rounded-2xl p-6 text-white shadow-md relative overflow-hidden border border-emerald-500/20">
                <div class="absolute -right-8 -top-8 w-36 h-36 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
                <div class="absolute -left-12 -bottom-12 w-48 h-48 rounded-full bg-teal-500/10 blur-2xl pointer-events-none"></div>

                <div class="relative z-10">
                    <p class="text-emerald-100/80 text-[10px] font-bold uppercase tracking-wider mb-1">Total Contributions Paid</p>
                    <p class="text-3xl font-extrabold mb-5 tracking-tight">
                        {{ fmt(totalPaid) }} <span class="text-base font-normal text-emerald-200">ETB</span>
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/10 hover:bg-white/15 rounded-xl p-3.5 backdrop-blur-md transition duration-200 border border-white/5 shadow-inner">
                            <p class="text-[10px] text-emerald-100/85 font-semibold uppercase tracking-wider mb-1">Total Outstanding</p>
                            <p class="font-bold text-xl">{{ fmt(totalPending) }} <span class="text-xs font-normal opacity-80">ETB</span></p>
                        </div>
                        <div class="bg-white/10 hover:bg-white/15 rounded-xl p-3.5 backdrop-blur-md transition duration-200 border border-white/5 shadow-inner">
                            <p class="text-[10px] text-emerald-100/85 font-semibold uppercase tracking-wider mb-1">Last Payment Date</p>
                            <p class="font-bold text-sm mt-1.5">{{ lastPayment?.created_at?.split('T')[0] ?? lastPayment?.date ?? 'No payments' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Alert Banner -->
            <div
                v-if="pendingPayments.length"
                class="flex items-start gap-3 bg-amber-50/40 dark:bg-amber-950/15 border border-amber-200/60 dark:border-amber-900/40 rounded-2xl p-4 shadow-sm"
            >
                <div class="p-1 rounded-lg bg-amber-100 dark:bg-amber-900/35 text-amber-600 dark:text-amber-400 shrink-0">
                    <ExclamationTriangleIcon class="w-4.5 h-4.5 shrink-0" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-900 dark:text-amber-300">
                        {{ pendingPayments.length }} Pending Payment{{ pendingPayments.length !== 1 ? 's' : '' }} Due
                    </p>
                    <p class="text-xs text-amber-700/90 dark:text-amber-400 mt-1 leading-relaxed">
                        You have an outstanding balance of <span class="font-bold text-amber-900 dark:text-amber-250">{{ fmt(totalPending) }} ETB</span>. Please complete your pending transactions to ensure uninterrupted course access.
                    </p>
                </div>
            </div>

            <!-- Payment List Section -->
            <section class="space-y-3">
                <div class="flex items-center justify-between pb-1 border-b border-gray-50 dark:border-gray-800">
                    <h2 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Payment Activity</h2>
                    <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">{{ payments.length }} records</span>
                </div>

                <div v-if="payments.length" class="space-y-3">
                    <div
                        v-for="payment in [...payments].reverse()"
                        :key="payment.id"
                        class="group bg-white dark:bg-gray-800/80 rounded-2xl border border-gray-150/80 dark:border-gray-700/80 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 overflow-hidden"
                    >
                        <div class="flex items-center gap-4 p-4">
                            <!-- Status icon container -->
                            <div
                                :class="[
                                    'w-11 h-11 rounded-xl flex items-center justify-center shrink-0 border transition-colors',
                                    payment.status === 'completed' || payment.status === 'paid'
                                        ? 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/30'
                                        : 'bg-amber-50 dark:bg-amber-950/20 border-amber-100 dark:border-amber-900/30',
                                ]"
                            >
                                <component
                                    :is="statusIcon(payment.status)"
                                    :class="[
                                        'w-5 h-5 transition-transform group-hover:scale-110',
                                        payment.status === 'completed' || payment.status === 'paid'
                                            ? 'text-emerald-600 dark:text-emerald-400'
                                            : 'text-amber-600 dark:text-amber-400',
                                    ]"
                                />
                            </div>

                            <!-- Payment details info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                    {{ payment.paymentType?.name ?? payment.type ?? 'Payment Invoice' }}
                                </p>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-1.5">
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500 font-medium">
                                        <CalendarIcon class="w-3.5 h-3.5" />
                                        {{ payment.created_at?.split('T')[0] ?? payment.payment_date ?? '—' }}
                                    </span>
                                    <span v-if="payment.paymentMethod?.bank_name || payment.paymentMethod?.name" class="text-xs text-gray-400 dark:text-gray-500">
                                        · {{ payment.paymentMethod?.bank_name ?? payment.paymentMethod?.name }}
                                    </span>
                                    <span v-if="payment.payment_reference" class="text-xs text-gray-300 dark:text-gray-600">
                                        · #{{ payment.payment_reference }}
                                    </span>
                                </div>
                            </div>

                            <!-- Amount & status badge -->
                            <div class="text-right shrink-0">
                                <p
                                    :class="[
                                        'text-base font-extrabold tracking-tight',
                                        payment.status === 'completed' || payment.status === 'paid'
                                            ? 'text-emerald-600 dark:text-emerald-450'
                                            : 'text-amber-650 dark:text-amber-400',
                                    ]"
                                >
                                    {{ fmt(payment.amount ?? payment.paid_amount) }} <span class="text-xs font-normal">ETB</span>
                                </p>
                                <span :class="['text-[10px] font-bold uppercase tracking-wider rounded-lg px-2 py-0.5 mt-1.5 inline-block border', statusStyle(payment.status)]">
                                    {{ payment.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty state fallback -->
                <div v-else class="text-center py-16 bg-white dark:bg-gray-800/40 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700/60">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-850 flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-800">
                        <CurrencyDollarIcon class="w-6 h-6 text-gray-400 dark:text-gray-500" />
                    </div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">No Payment Records</p>
                    <p class="text-xs text-gray-450 mt-1">There are no billing or transaction logs linked to your account.</p>
                </div>
            </section>

        </div>
    </AppLayout>
</template>
