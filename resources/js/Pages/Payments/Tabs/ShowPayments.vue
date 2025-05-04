<script setup>
import { ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ArrowPathIcon, PlusCircleIcon, XMarkIcon } from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import PaymentList from "./List.vue"; // Adjusted path to "./List.vue"

defineProps({
    payments: {
        type: Object,
        required: true,
    },
    paymentTypes: Array,
    paymentCategories: Array,
});

const refreshing = ref(false);
const createPaymentModal = ref(false);

const paymentCreationForm = useForm({
    payment_type_id: "",
    payment_category_id: null,
    payment_date: new Date().toISOString().slice(0, 10),
    total_amount: "",
    payment_method: "",
    status: "pending",
    payment_reference: "",
});

const refreshData = () => {
    refreshing.value = true;
    setTimeout(() => {
        refreshing.value = false;
    }, 1000);
};

const closeCreatePaymentModal = () => {
    createPaymentModal.value = false;
    paymentCreationForm.reset();
};

const submitNewPayment = () => {
    router.post(route("payments.store"), paymentCreationForm, {
        onSuccess: () => {
            createPaymentModal.value = false;
            paymentCreationForm.reset();
        },
        onError: (errors) => {
            console.log(errors);
        },
    });
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Payments
            </h2>
            <button
                @click="createPaymentModal = !createPaymentModal"
                class="flex text-green-600 hover:text-green-800"
            >
                <PlusCircleIcon class="mx-2 w-8 h-8" />
                Create Payment
            </button>
        </div>

        <div class="overflow-x-auto">
            <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
                <div v-if="!payments.data.length" class="text-center">
                    <p class="text-gray-500 dark:text-gray-400">
                        No payment information available.
                    </p>
                </div>
                <div v-else>
                    <PaymentList
                        :payments="payments"
                        :sort-info="null"
                        :deletepayment="null"
                        :search="null"
                    />
                </div>
            </div>
        </div>

        <Modal
            :show="createPaymentModal"
            @close="closeCreatePaymentModal"
            :maxWidth="'md'"
            class="p-6"
        >
            <div class="w-full px-8 py-6">
                <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                    Create New Payment
                </h1>

                <form @submit.prevent="submitNewPayment">
                    <div class="mb-4">
                        <label for="payment_type_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Type</label>
                        <select v-model="paymentCreationForm.payment_type_id" id="payment_type_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="" disabled>Select Payment Type</option>
                            <option v-for="type in paymentTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                        </select>
                        <InputError :message="paymentCreationForm.errors.payment_type_id" class="mt-2 text-sm text-red-500" />
                    </div>

                    <div class="mb-4">
                        <label for="payment_category_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Category (Optional)</label>
                        <select v-model="paymentCreationForm.payment_category_id" id="payment_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                            <option :value="null">-- Select Category --</option>
                            <option v-for="category in paymentCategories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                        <InputError :message="paymentCreationForm.errors.payment_category_id" class="mt-2 text-sm text-red-500" />
                    </div>

                    <div class="mb-4">
                        <label for="payment_date" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Date</label>
                        <input type="date" v-model="paymentCreationForm.payment_date" id="payment_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" />
                        <InputError :message="paymentCreationForm.errors.payment_date" class="mt-2 text-sm text-red-500" />
                    </div>

                    <div class="mb-4">
                        <label for="total_amount" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Total Amount</label>
                        <input type="number" v-model="paymentCreationForm.total_amount" id="total_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" />
                        <InputError :message="paymentCreationForm.errors.total_amount" class="mt-2 text-sm text-red-500" />
                    </div>

                    <div class="mb-4">
                        <label for="payment_method" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Method</label>
                        <input type="text" v-model="paymentCreationForm.payment_method" id="payment_method" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" />
                        <InputError :message="paymentCreationForm.errors.payment_method" class="mt-2 text-sm text-red-500" />
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Status</label>
                        <select v-model="paymentCreationForm.status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                        <InputError :message="paymentCreationForm.errors.status" class="mt-2 text-sm text-red-500" />
                    </div>

                    <div class="mb-4">
                        <label for="payment_reference" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Reference (Optional)</label>
                        <input type="text" v-model="paymentCreationForm.payment_reference" id="payment_reference" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" />
                        <InputError :message="paymentCreationForm.errors.payment_reference" class="mt-2 text-sm text-red-500"/>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button
                            type="submit"
                            :disabled="paymentCreationForm.processing"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                        >
                            {{ paymentCreationForm.processing ? "Creating..." : "Create Payment" }}
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
    </div>
</template>