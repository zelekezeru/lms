<script setup>
import { defineProps, ref, onMounted } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PlusCircleIcon, PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/outline";
import AppLayout from "@/Layouts/AppLayout.vue"; // Don't forget this import!

const props = defineProps({
    student: Object,
    status: Object,
    showVerifyModal: Boolean,
    paymentSchedules: Array, // Prop to receive payment schedules
    payments: Array, // Prop to receive individual payments
});

const deletePaymentSchedule = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert the deletion of this payment schedule!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("students.payment-schedules.destroy", { student: props.student.id, payment_schedule: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The payment schedule has been deleted.",
                        "success"
                    );
                    // Optionally, emit an event to refresh the parent component's data
                },
            });
        }
    });
};

const deletePayment = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert the deletion of this payment!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("payments.destroy", { payment: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The payment has been deleted.",
                        "success"
                    );
                    // Optionally, emit an event to refresh the parent component's data
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div>
            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Payment Schedules</h3>
                <Link :href="route('students.payment-schedules.create', { student: student.id })" class="inline-flex items-center rounded-md bg-indigo-600 text-white px-3 py-2 text-sm font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <PlusCircleIcon class="w-5 h-5 mr-2" />
                    Add Schedule
                </Link>
            </div>

            <div v-if="paymentSchedules && paymentSchedules.length > 0">
                <div v-for="schedule in paymentSchedules" :key="schedule.id" class="mb-6 bg-white dark:bg-gray-800 shadow rounded-md p-4 border dark:border-gray-700">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ schedule.description || 'Payment Schedule' }}</h4>
                        <div>
                            <Link :href="route('students.payment-schedules.edit', { student: student.id, payment_schedule: schedule.id })" class="text-green-500 hover:text-green-700 mr-2">
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                            <button @click="deletePaymentSchedule(schedule.id)" class="text-red-500 hover:text-red-700">
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">Total Expected: {{ schedule.total_expected_amount }}</p>

                    <div v-if="schedule.items && schedule.items.length > 0">
                        <ul class="list-disc pl-5">
                            <li v-for="item in schedule.items" :key="item.id" class="mb-1">
                                {{ item.name }} - Due: {{ item.due_date }} - Expected: {{ item.expected_amount }} - Paid: {{ item.paid_amount }} - Remaining: {{ item.remaining_amount }}
                            </li>
                        </ul>
                    </div>
                    <div v-else class="text-gray-500 dark:text-gray-500">No installments defined for this schedule.</div>
                </div>
            </div>
            <div v-else class="mb-6 text-gray-500 dark:text-gray-500">No payment schedules found for this student.</div>

            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Individual Payments</h3>
                <Link :href="route('students.payments.create', { student: student.id })" class="inline-flex items-center rounded-md bg-indigo-600 text-white px-3 py-2 text-sm font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <PlusCircleIcon class="w-5 h-5 mr-2" />
                    Record Payment
                </Link>
            </div>

            <div v-if="payments && payments.length > 0">
                <div v-for="payment in payments" :key="payment.id" class="mb-3 bg-white dark:bg-gray-800 shadow rounded-md p-4 border dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-800 dark:text-gray-200 font-semibold">#{{ payment.id }} - {{ payment.payment_type ? payment.payment_type.name : 'Payment' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Date: {{ payment.payment_date }} - Amount: {{ payment.total_amount }} - Status: {{ payment.status }}</p>
                            <p v-if="payment.paymentScheduleItem" class="text-sm text-indigo-500">Linked to: {{ payment.paymentScheduleItem.paymentSchedule.description }} - {{ payment.paymentScheduleItem.name }}</p>
                        </div>
                        <button @click="deletePayment(payment.id)" class="text-red-500 hover:text-red-700">
                            <TrashIcon class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
            <div v-else class="text-gray-500 dark:text-gray-500">No payments recorded for this student yet.</div>
        </div>
    </AppLayout>
</template>