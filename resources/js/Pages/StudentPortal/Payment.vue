<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    AcademicCapIcon,
    CurrencyDollarIcon,
    ClipboardDocumentListIcon,
    BookOpenIcon,
    ArrowPathIcon,
    ChartBarIcon,
    CalendarIcon,
    CogIcon,
    QuestionMarkCircleIcon,
} from "@heroicons/vue/24/outline";
</script>

<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow flex items-center space-x-3"
                >
                    <AcademicCapIcon class="h-6 w-6 text-gray-500" />
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total Paid
                        </p>
                        <p class="text-2xl font-bold text-green-500">
                            $1,200.00
                        </p>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow flex items-center space-x-3"
                >
                    <CurrencyDollarIcon class="h-6 w-6 text-gray-500" />
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Balance Due
                        </p>
                        <p class="text-2xl font-bold text-red-500">$300.00</p>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow flex items-center space-x-3"
                >
                    <ClipboardDocumentListIcon class="h-6 w-6 text-gray-500" />
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Last Payment
                        </p>
                        <p
                            class="text-2xl font-bold text-gray-900 dark:text-gray-100"
                        >
                            April 1, 2025
                        </p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Payments -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center space-x-3">
                        <BookOpenIcon class="h-6 w-6 text-gray-500" />
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-gray-100"
                        >
                            Upcoming Payments
                        </h2>
                    </div>
                    <button
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                    >
                        Pay Now
                    </button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Next Due
                        </p>
                        <p class="text-base font-medium text-yellow-500">
                            July 1, 2025
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Amount
                        </p>
                        <p
                            class="text-base font-medium text-gray-800 dark:text-gray-100"
                        >
                            $300.00
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Payment Type
                        </p>
                        <p
                            class="text-base font-medium text-gray-800 dark:text-gray-100"
                        >
                            Tuition
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Status
                        </p>
                        <p class="text-base font-medium text-orange-500">
                            Pending
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
                <div class="flex items-center space-x-3 mb-4">
                    <ChartBarIcon class="h-6 w-6 text-gray-500" />
                    <h2
                        class="text-lg font-semibold text-gray-800 dark:text-gray-100"
                    >
                        Payment History
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300"
                                >
                                    Date
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300"
                                >
                                    Amount
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300"
                                >
                                    Method
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300"
                                >
                                    Receipt
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr>
                                <td
                                    class="px-4 py-2 text-gray-800 dark:text-gray-100"
                                >
                                    25-jun
                                </td>
                                <td class="px-4 py-2 text-green-600">$2000</td>
                                <td
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300"
                                >
                                    Credit
                                </td>
                                <td class="px-4 py-2">
                                    <button
                                        class="text-blue-600 hover:underline"
                                    >
                                        Download
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    class="px-4 py-2 text-gray-800 dark:text-gray-100"
                                >
                                    25-jun
                                </td>
                                <td class="px-4 py-2 text-green-600">$2000</td>
                                <td
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300"
                                >
                                    Credit
                                </td>
                                <td class="px-4 py-2">
                                    <button
                                        class="text-blue-600 hover:underline"
                                    >
                                        Download
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
