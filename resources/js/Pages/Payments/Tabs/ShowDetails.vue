<script setup>
import { ref, onMounted, computed } from "vue";
import { ArrowPathIcon, 
        CreditCardIcon,
        BanknotesIcon, ClockIcon, ExclamationCircleIcon } from "@heroicons/vue/24/solid";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from "chart.js";

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

// Props
const props = defineProps({
    payments: { type: Object, required: true },
    paymentCategories: { type: Array, required: true },
    paymentMethods: { type: Array, required: true },
});

// State
const refreshing = ref(false);
const chartData = ref({
    labels: [],
    datasets: [{
        label: "Payments",
        backgroundColor: "#4CAF50",
        data: [],
    }],
});

const formatNumber = (number) => {
    return new Intl.NumberFormat().format(number);
};

const selectedStatus = ref("");

// Filtered payments based on status
const filteredPayments = computed(() => {
    if (!selectedStatus.value) {
        return props.payments.data || [];
    }
    return (props.payments.data || []).filter(
        payment => payment.status && payment.status.toLowerCase() === selectedStatus.value
    );
});
</script>

<template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Payment Dashboard</h2>
            <button
                @click="refreshData"
                class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700"
            >
                <ArrowPathIcon class="w-5 h-5 mr-2" />
                {{ refreshing ? "Refreshing..." : "Refresh" }}
            </button>
        </div>
        
        <!-- Payment Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">

            <!-- Total Payments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center space-x-4">
                <CreditCardIcon class="w-8 h-8 text-green-600" />
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Payments</p>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ payments.length || 0 }}</h2>
                </div>
                </div>
            </div>

            <!-- Total Paid -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center space-x-4">
                <BanknotesIcon class="w-8 h-8 text-blue-600" />
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Paid</p>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ formatNumber((payments.data || []).reduce((sum, payment) => sum + Number(payment.total_amount), 0)) }} Birr
                    </h2>
                </div>
                </div>
            </div>

            <!-- Total Remaining -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center space-x-4">
                <ClockIcon class="w-8 h-8 text-yellow-500" />
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Remaining</p>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ formatNumber((payments.data || [])
                            .filter(payment => payment.status.trim().toLowerCase() === 'pending')
                            .reduce((sum, payment) => sum + Number(payment.total_amount), 0)) }} Birr
                    </h2>
                </div>
                </div>
            </div>

            <!-- Payment Progress -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center space-x-4">
                <ChartBarIcon class="w-8 h-8 text-green-600" />
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Completed Payments </p>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ formatNumber((payments.data || [])
                            .filter(payment => payment.status.trim().toLowerCase() === 'completed')
                            .reduce((sum, payment) => sum + Number(payment.total_amount), 0)) }} Birr
                    </h2>
                </div>
                </div>
            </div>
        </div>


        <!-- Recent Payments Table -->
        <div class="mb-6">
            <div class="flex items-center mb-4 gap-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Recent Payments</h3>
                <!-- Status Filter Dropdown -->
                
            </div>

            <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4 overflow-x-auto">
                <table class="min-w-full table-auto border border-gray-300 dark:border-gray-600">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">No.</th>
                            <th class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">Date</th>
                            <th class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">Amount (Birr)</th>
                            <th class="w-40 px-4 py-2 text-center text-sm font-medium text-gray-700 dark:text-gray-200 border-r">
                                <div class="flex flex-col items-center">
                                    <span class="flex items-center justify-center">
                                        Status
                                        <svg class="w-4 h-4 text-blue-500 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                    <div class="relative mt-1">
                                        <select
                                            v-model="selectedStatus"
                                            class="appearance-none pl-10 pr-4 py-1.5 rounded-full border border-blue-400 dark:border-blue-600 bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 font-semibold shadow focus:ring-2 focus:ring-blue-400 focus:border-blue-500 transition text-center"
                                        >
                                            <option value="">All</option>
                                            <option value="completed">Completed</option>
                                            <option value="pending">Pending</option>
                                            <option value="paid_by_college">Scholarship</option>
                                        </select>
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-blue-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10l4 4 4-4" />
                                        </svg>
                                    </div>
                                </div>
                            </th>
                            <th class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(payment, index) in filteredPayments"
                            :key="payment.id"
                            :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'"
                            class="border-b border-gray-300 dark:border-gray-600"
                        >
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">{{ index + 1 }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">{{ payment.payment_date }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">{{ formatNumber(payment.total_amount) }}.00</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">
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
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">{{ payment.payment_reference }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Links -->
        <div v-if="payments.meta && payments.meta.links && payments.meta.links.length > 1" class="mt-3 flex justify-center space-x-2">
            <button
            v-for="link in payments.meta.links"
            :key="link.label"
            :disabled="!link.url || link.active"
            @click="goToPage(link.url)"
            class="p-2 px-4 text-sm font-medium rounded-lg transition-colors"
            :class="{
                'text-gray-700 dark:text-gray-400': true,
                'cursor-not-allowed opacity-50': !link.url || link.active,
                '!bg-blue-100 !dark:bg-blue-800 text-blue-700 dark:text-blue-200': link.active,
                'hover:bg-gray-200 dark:hover:bg-gray-700': link.url && !link.active
            }"
            v-html="link.label"
            />
        </div>

    </div>
</template>
