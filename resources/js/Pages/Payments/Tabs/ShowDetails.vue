<script setup>
import { ref, onMounted } from "vue";
import { ArrowPathIcon, 
        CreditCardIcon,
        BanknotesIcon, ClockIcon, ChartBarIcon, ExclamationCircleIcon } from "@heroicons/vue/24/solid";
import { Bar } from "vue-chartjs";
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
    payments: { type: Array, required: true },
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
const chartOptions = ref({
    responsive: true,
    plugins: {
        legend: { position: "top" },
        title: {
            display: true,
            text: "Payment Trends",
        },
    },
});

// Methods
const updateChartData = (payments) => {
    const groupedData = payments.reduce((acc, payment) => {
        const date = payment.payment_date.split("T")[0];
        acc[date] = (acc[date] || 0) + Number(payment.total_amount);
        return acc;
    }, {});
    chartData.value.labels = Object.keys(groupedData);
    chartData.value.datasets[0].data = Object.values(groupedData);
};

const refreshData = () => {
    refreshing.value = true;
    // Simulate refresh (replace with actual fetch logic if needed)
    setTimeout(() => {
        updateChartData(props.payments);
        refreshing.value = false;
    }, 1000);
};

const formatNumber = (number) => {
    return new Intl.NumberFormat().format(number);
};

// Lifecycle
onMounted(() => {
    updateChartData(props.payments);
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
                        {{ formatNumber(payments.reduce((sum, payment) => sum + Number(payment.total_amount), 0)) }} Birr
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
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ formatNumber(payments
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
                        {{ formatNumber(payments
                            .filter(payment => payment.status.trim().toLowerCase() === 'completed')
                            .reduce((sum, payment) => sum + Number(payment.total_amount), 0)) }} Birr
                    </h2>
                </div>
                </div>
            </div>
        </div>


        <!-- Recent Payments Table -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Payments</h3>
            <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4 overflow-x-auto">
                <table class="min-w-full table-auto border border-gray-300 dark:border-gray-600">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700">
                            <th class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">No.</th>
                            <th class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">Date</th>
                            <th class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">Amount (Birr)</th>
                            <th class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">Reference</th>
                            <th class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(payment, index) in payments"
                            :key="payment.id"
                            :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'"
                            class="border-b border-gray-300 dark:border-gray-600"
                        >
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">{{ index + 1 }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">{{ payment.payment_date }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">{{ formatNumber(payment.total_amount) }}.00</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">{{ payment.payment_reference }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r">
                                <span :class="{
                                    'text-yellow-500': payment.status === 'pending',
                                    'text-green-500': payment.status === 'completed',
                                    'text-red-500': payment.status === 'failed',
                                    'text-blue-500': payment.status === 'refunded',
                                }">
                                    {{ payment.status.charAt(0).toUpperCase() + payment.status.slice(1) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Chart Section -->
        <div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Trends</h3>
            <div class="h-64">
                <Bar :data="chartData" :options="chartOptions" />
            </div>
        </div>
    </div>
</template>
