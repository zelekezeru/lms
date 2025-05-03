<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { EyeIcon, TrashIcon, ArrowPathIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";

defineProps({
    payments: {
        type: Object,
        required: true,
    },
});

const refreshing = ref(false);

const refreshData = () => {
    refreshing.value = true;
    router.flush("/payments", { method: "get" });

    router.visit(route("payments.index"), {
        only: ["payments"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

const deletePayment = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("payments.destroy", { payment: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "The payment has been deleted.", "success");
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Payments
            </h1>
        </div>

        <div class="flex justify-between items-center mb-3">
            <Link
                v-if="userCan('create-payments')"
                :href="route('payments.create')"
                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 text-white dark:bg-gray-700 dark:text-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Add New Payment
            </Link>
            <button
                @click="refreshData"
                class="inline-flex items-center rounded-md border border-transparent bg-blue-800 text-white dark:bg-blue-700 dark:text-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                title="Refresh Data"
            >
                <ArrowPathIcon class="w-5 h-5 mr-2" :class="{ 'animate-spin': refreshing }" />
                Refresh Data
            </button>
        </div>

        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Amount</th>
                        <th scope="col" class="px-6 py-3">Payment Method</th>
                        <th scope="col" class="px-6 py-3">Payment Status</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="payment in payments.data" :key="payment.id" class="border-b dark:border-gray-700">
                        <td class="px-6 py-4">{{ payment.id }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            <Link :href="route('payments.show', { payment: payment.id })">
                                {{ payment.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4">{{ payment.total_amount }}</td>
                        <td class="px-6 py-4">
                            {{
                            payment.payment_method === 1
                                ? "Cash"
                                : payment.payment_method === 2
                                ? "Bank Transfer"
                                : payment.payment_method === 3
                                ? "Cheque"
                                : "Credit Card"
                            }}
                        </td>
                        <td class="px-6 py-4">
                            {{
                            payment.payment_status === 1
                                ? "Pending"
                                : payment.payment_status === 2
                                ? "Completed"
                                : payment.payment_status === 3
                                ? "Failed"
                                : "Cancelled"
                            }}
                        </td>
                        <td class="px-6 py-4 flex space-x-3">
                            <Link
                                v-if="userCan('view-payments')"
                                :href="route('payments.show', { payment: payment.id })"
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                            <Link
                                v-if="userCan('update-payments')"
                                :href="route('payments.edit', { payment: payment.id })"
                                class="text-green-500 hover:text-green-700"
                            >
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                            <button
                                v-if="userCan('delete-payments')"
                                @click="deletePayment(payment.id)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <TrashIcon class="w-5 h-5" />
                                <span>Delete</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-3 flex justify-center space-x-6" v-if="payments && payments.meta && payments.meta.links">
            <Link
                v-for="link in payments.meta.links"
                :key="link.label"
                :href="link.url || '#'"
                class="p-2 px-4 text-sm font-medium rounded-lg transition-colors"
                :class="{
                    'text-gray-700 dark:text-gray-400': true,
                    'cursor-not-allowed opacity-50': !link.url,
                    '!bg-gray-100 !dark:bg-gray-800': link.active,
                }"
                v-html="link.label"
            />
        </div>
        <div v-else class="mt-4 text-center text-gray-500 dark:text-gray-400">
            No pagination information available.
        </div>
    </AppLayout>
</template>
