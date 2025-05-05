<script setup>
import { ref, computed } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PlusCircleIcon } from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import PaymentList from "./List.vue";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    payments: {
        type: Array,
        required: true,
    },
    paymentMethods: Array,
    paymentCategories: Array,
    paymentStatuses: Array,
});

const createPaymentModal = ref(false);

const studentId = computed(() => props.student?.id);

const paymentCreationForm = useForm({
    student_id: studentId.value,
    payment_method_id: null,
    payment_category_id: null,
    payment_date: new Date().toISOString().slice(0, 10),
    total_amount: null,
    narration: null,
    status: null,
    payment_reference: null,
});

const closeCreatePaymentModal = () => {
    createPaymentModal.value = false;
    paymentCreationForm.reset();
};

const submitNewPayment = () => {
    paymentCreationForm.post(route("payments.store", { student: props.student.id }), {
        onSuccess: () => {
            Swal.fire("Success!", "Payment has been created successfully.", "success");
            createPaymentModal.value = false;
            paymentCreationForm.reset();
        },
        onError: (errors) => {
            Swal.fire("Error!", "Failed to create the payment. Please check the form for errors.", "error");
            console.log(errors);
        },
    });
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Payments</h2>
            <button
                @click="createPaymentModal = true"
                class="flex text-green-600 hover:text-green-800"
            >
                <PlusCircleIcon class="mx-2 w-8 h-8" />
                Create Payment
            </button>
        </div>

        <div class="overflow-x-auto">
            <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
                <div v-if="!payments || !payments.length" class="text-center">
                    <p class="text-gray-500 dark:text-gray-400">No payment information available.</p>
                </div>
                <div v-else>
                    <PaymentList :payments="payments" />
                </div>
            </div>
        </div>

        <Modal :show="createPaymentModal" @close="closeCreatePaymentModal" :maxWidth="'md'" class="p-6">
            <div class="w-full px-8 py-6">
                <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                    Create Payment for - <span class="text-yellow-700 underline">{{ student.first_name }} {{ student.middle_name }}</span>
                </h1>

                <form @submit.prevent="submitNewPayment">
                    <div class="mb-4">
                        <label for="payment_method_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Method</label>
                        <select v-model="paymentCreationForm.payment_method_id" id="payment_method_id" class="form-select">
                            <option :value="null" disabled>Select Payment Method</option>
                            <option v-for="type in paymentMethods" :key="type.id" :value="type.id">{{ type.bank_name }}</option>
                        </select>
                        <InputError :message="paymentCreationForm.errors.payment_method_id" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="payment_category_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Category (Optional)</label>
                        <select v-model="paymentCreationForm.payment_category_id" id="payment_category_id" class="form-select">
                            <option :value="null" disabled>Select Category</option>
                            <option v-for="category in paymentCategories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                        <InputError :message="paymentCreationForm.errors.payment_category_id" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="payment_date" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Date</label>
                        <input type="date" v-model="paymentCreationForm.payment_date" id="payment_date" class="form-input" />
                        <InputError :message="paymentCreationForm.errors.payment_date" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="total_amount" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Total Amount</label>
                        <input type="number" v-model="paymentCreationForm.total_amount" id="total_amount" class="form-input" />
                        <InputError :message="paymentCreationForm.errors.total_amount" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="narration" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Narration</label>
                        <input type="text" v-model="paymentCreationForm.narration" id="narration" class="form-input" />
                        <InputError :message="paymentCreationForm.errors.narration" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Status</label>
                        <select v-model="paymentCreationForm.status" id="status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                        <InputError :message="paymentCreationForm.errors.status" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="payment_reference" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Reference (Optional)</label>
                        <input type="text" v-model="paymentCreationForm.payment_reference" id="payment_reference" class="form-input" />
                        <InputError :message="paymentCreationForm.errors.payment_reference" class="mt-2" />
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