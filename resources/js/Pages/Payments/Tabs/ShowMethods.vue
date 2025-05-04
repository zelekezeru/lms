<script setup>
import { ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

import {
    PlusCircleIcon,
    PencilSquareIcon,
    TrashIcon,
    XMarkIcon,
} from "@heroicons/vue/24/solid";

import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import FormFields from "@/Pages/Finance/Partials/FormFields.vue"; // Adjusted path for FormFields

const props = defineProps({
    paymentMethods: {
        type: Object,
        required: true,
    },
});

const refreshing = ref(false);
const createMethodModal = ref(false);
const editMethodModal = ref(false);
const currentEditId = ref(null);

const methodCreationForm = useForm({
    payment_type: "",
    bank_name: "",
    account_number: "",    
    is_active: "1",
});

const methodEditForm = useForm({
    payment_type: props.paymentMethods?.payment_type || "",
    bank_name: props.paymentMethods?.bank_name || "",
    account_number: props.paymentMethods?.account_number || "",
    is_active: props.paymentMethods?.is_active?.toString() || "1",
});

const refreshData = () => {
    refreshing.value = true;
    router.get(window.location.href, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => (refreshing.value = false),
    });
};

const deletePaymentMethod = (id) => {
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
            router.delete(route("paymentMethods.destroy", { paymentMethod: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "The payment method has been deleted.", "success");
                    refreshData();
                },
                onError: () => {
                    Swal.fire("Error!", "Failed to delete the payment method.", "error");
                    refreshData();
                },
            });
        }
    });
};

const submitNewMethod = () => {
    methodCreationForm.post(route("paymentMethods.store"), {
        onSuccess: () => {
            Swal.fire("Success!", "The payment method has been created.", "success");
            methodCreationForm.reset();
            createMethodModal.value = false;
            refreshData();
        },
    });
};

const editMethod = (method) => {
    currentEditId.value = method.id;
    methodEditForm.payment_type = method.payment_type;
    methodEditForm.bank_name = method.bank_name; // Ensure bank_name is set
    methodEditForm.account_number = method.account_number;
    methodEditForm.is_active = method.is_active.toString();
    editMethodModal.value = true;
};

const submitEditMethod = () => {
    if (!currentEditId.value) return;
    methodEditForm.put(route("paymentMethods.update", { paymentMethod: currentEditId.value }), {
        onSuccess: () => {
            editMethodModal.value = false;
            methodEditForm.reset();
            refreshData();
        },
    });
};

const closeCreateMethodModal = () => {
    createMethodModal.value = false;
    methodCreationForm.reset();
};

const closeEditModal = () => {
    editMethodModal.value = false;
    methodEditForm.reset();
};
</script>

<template>
    <div>
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Payment Methods</h2>
            <button @click="createMethodModal = true" class="flex text-green-600 hover:text-green-800">
                <PlusCircleIcon class="mx-2 w-8 h-8" />
                Create Payment Method
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
            <div v-if="!paymentMethods?.data?.length" class="text-center text-gray-500 dark:text-gray-400">
                No payment method information available.
            </div>
            <div v-else>
                <Table>
                    <TableHeader>
                        <tr>
                            <Thead>No.</Thead>
                            <Thead>Method Name</Thead>
                            <Thead>Account Number</Thead>
                            <Thead>Payment Type</Thead>
                            <Thead>Status</Thead>
                            <Thead>Actions</Thead>
                        </tr>
                    </TableHeader>
                    <tbody>
                        <TableZebraRows v-for="(method, index) in paymentMethods.data" :key="method.id">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ index + 1 }}
                            </th>
                            <td class="px-6 py-4">{{ method.bank_name }}</td>
                            <td class="px-6 py-4">{{ method.account_number }}</td>
                            <td class="px-6 py-4">{{ method.payment_type }}</td>
                            <td class="px-6 py-4">
                                {{ method.is_active == "1" ? "Active" : "Inactive" }}
                            </td>
                            <td class="px-6 py-4 flex justify-start space-x-4">
                                <button
                                    v-if="userCan('update-paymentMethods')"
                                    @click="editMethod(method)"
                                    class="text-green-500 hover:text-green-700"
                                >
                                    <PencilSquareIcon class="w-5 h-5" />
                                </button>
                                <button
                                    v-if="userCan('delete-paymentMethods')"
                                    @click="deletePaymentMethod(method.id)"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <TrashIcon class="w-5 h-5" />
                                </button>
                            </td>
                        </TableZebraRows>
                    </tbody>
                </Table>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="paymentMethods?.meta?.links" class="mt-3 flex justify-center space-x-6">
            <Link
                v-for="link in paymentMethods.meta.links"
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

        <!-- Create Payment Method Modal -->
        <Modal :show="createMethodModal" @close="closeCreateMethodModal" :maxWidth="'md'" class="p-6">
            <div class="w-full px-8 py-6">
                <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Create New Payment Method</h1>
                <form @submit.prevent="submitNewMethod">                    
                    <div>                        
                        <!-- Payment Type 1: cash, 2: cheque, 3: bank transfer'-->
                        <div class="mb-4">
                            <label for="payment_type" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Payment Type</label>
                            <select
                                v-model="methodCreationForm.payment_type"
                                id="payment_type"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option value="" disabled>Select Payment Type</option>
                                <option value="Bank">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Mobile Money">Mobile Money</option>
                                <option value="Other">Other</option>
                            </select>
                            <InputError :message="methodCreationForm.errors.payment_type" class="mt-2" />
                        </div>

                        <!-- Bank Name -->
                        <div class="mb-4">
                            <label for="bank_name" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Bank Name (Optional)</label>
                            <input
                                v-model="methodCreationForm.bank_name"
                                id="bank_name"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError :message="methodCreationForm.errors.bank_name" class="mt-2" />
                        </div>

                        <!-- Account Number -->
                        <div class="mb-4">
                            <label for="account_number" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Account Number (Optional)</label>
                            <input
                                v-model="methodCreationForm.account_number"
                                id="account_number"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError :message="methodCreationForm.errors.account_number" class="mt-2" />
                        </div>


                        <div class="mb-4">
                            <label for="is_active" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Status</label>
                            <select
                                v-model="methodCreationForm.is_active"
                                id="is_active"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <InputError :message="methodCreationForm.errors.is_active" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" :disabled="methodCreationForm.processing" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-4">
                            {{ methodCreationForm.processing ? "Creating..." : "Create Payment Method" }}
                        </button>
                        <button type="button" @click="closeCreateMethodModal" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Edit Payment Method Modal -->
        <Modal :show="editMethodModal" @close="closeEditModal" :maxWidth="'md'" class="p-6">
            <form @submit.prevent="submitEditMethod">
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Payment Method</h2>
                        <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <XMarkIcon class="w-5 h-5" />
                        </button>
                    </div>

                    <div>
                        <!-- Payment Type -->
                        <div class="mb-4">
                            <label for="payment_type" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Payment Type</label>
                            <select
                                v-model="methodEditForm.payment_type"
                                id="payment_type"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option value="" disabled>Select Payment Type</option>
                                <option value="Bank">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Mobile Money">Mobile Money</option>
                                <option value="Other">Other</option>
                            </select>
                            <InputError :message="methodEditForm.errors.payment_type" class="mt-2" />
                        </div>

                        <!-- Bank Name -->
                        <div class="mb-4">
                            <label for="bank_name" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Bank Name (Optional)</label>
                            <input
                                v-model="methodEditForm.bank_name"
                                id="bank_name"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError :message="methodEditForm.errors.bank_name" class="mt-2" />
                        </div>

                        <!-- Account Number -->
                        <div class="mb-4">
                            <label for="account_number" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Account Number (Optional)</label>
                            <input
                                v-model="methodEditForm.account_number"
                                id="account_number"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError :message="methodEditForm.errors.account_number" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label for="is_active" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Status</label>
                            <select
                                v-model="methodEditForm.is_active"
                                id="is_active"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <InputError :message="methodEditForm.errors.is_active" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="submit" :disabled="methodEditForm.processing" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-md transition">
                            {{ methodEditForm.processing ? "Saving..." : "Save Changes" }}
                        </button>
                        <button @click="closeEditModal" type="button" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition">
                            Close
                        </button>
                    </div>
                </div>
            </form>
        </Modal>
    </div>
</template>
