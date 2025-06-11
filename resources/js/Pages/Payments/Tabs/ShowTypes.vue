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
import FormFields from "@/Pages/Finance/Partials/FormFields.vue";

const props = defineProps({
    paymentTypes: {
        type: Array,
        required: true,
    },
    paymentCategories: {
        type: Array,
        required: true,
    },
    studyModes: {
        type: Array,
        required: true,
    },
});

const refreshing = ref(false);
const createTypeModal = ref(false);
const editTypeModal = ref(false);
const currentEditId = ref(null);

const typeCreationForm = useForm({
    payment_category_id: "",
    study_mode_id: "",
    language: "",
    duration: "",
    amount: "",
    type: "",
});

const typeEditForm = useForm({
    payment_category_id: props.paymentTypes?.payment_category_id || "",
    study_mode_id: props.paymentTypes?.study_mode_id || "",
    language: props.paymentTypes?.language || "",
    duration: props.paymentTypes?.duration || "",
    amount: props.paymentTypes?.amount || "",
    type: props.paymentTypes?.type || "",
});

const refreshData = () => {
    refreshing.value = true;
    router.get(window.location.href, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => (refreshing.value = false),
    });
};

const deletePaymentType = (id) => {
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
            router.delete(route("paymentTypes.destroy", { paymentType: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The type has been deleted.",
                        "success"
                    );
                    refreshData();
                },
                onError: () => {
                    Swal.fire("Error!", "Failed to delete the type.", "error");
                    refreshData();
                },
            });
        }
    });
};

const submitNewType = () => {
    typeCreationForm.post(route("paymentTypes.store"), {
        onSuccess: () => {
            typeCreationForm.reset();
            createTypeModal.value = false;
            refreshData();
        },
    });
};

const editType = (type) => {
    currentEditId.value = type.id;
    typeEditForm.payment_category_id = type.payment_category_id;
    typeEditForm.study_mode_id = type.study_mode_id;
    typeEditForm.language = type.language;
    typeEditForm.type = type.type;
    typeEditForm.amount = type.amount;
    typeEditForm.duration = type.duration;
    editTypeModal.value = true;
};

const submitEditType = () => {
    if (!currentEditId.value) return;
    typeEditForm.put(
        route("paymentTypes.update", { paymentType: currentEditId.value }),
        {
            onSuccess: () => {
                editTypeModal.value = false;
                typeEditForm.reset();
                refreshData();
            },
        }
    );
};

const closeCreateTypeModal = () => {
    createTypeModal.value = false;
    typeCreationForm.reset();
};

const closeEditModal = () => {
    editTypeModal.value = false;
    typeEditForm.reset();
};
</script>

<template>
    <div>
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Payment Types
            </h2>
            <button
                @click="createTypeModal = true"
                class="flex text-green-600 hover:text-green-800"
            >
                <PlusCircleIcon class="mx-2 w-8 h-8" />
                Create Payment Type
            </button>
        </div>

        <!-- Table -->
        <div
            class="overflow-x-auto mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
        >
            <div
                v-if="!paymentTypes?.length"
                class="text-center text-gray-500 dark:text-gray-400"
            >
                No payment type information available.
            </div>
            <div v-else>
                <Table>
                    <TableHeader>
                        <tr>
                            <Thead>No.</Thead>
                            <Thead>Type</Thead>
                            <Thead>Study Mode</Thead>
                            <Thead>Language</Thead>
                            <Thead>Duration</Thead>
                            <Thead>Amount</Thead>
                            <Thead>Status</Thead>
                            <Thead>Actions</Thead>
                        </tr>
                    </TableHeader>
                    <tbody>
                        <TableZebraRows
                            v-for="(type, index) in paymentTypes || []"
                            :key="type.id"
                        >
                            <th
                                scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                            >
                                {{ index + 1 }}
                            </th>
                            <td class="px-6 py-4">{{ type.type }}</td>
                            <td class="px-6 py-4">
                                {{ type.studyMode?.name || "N/A" }}
                            </td>
                            <td class="px-6 py-4">
                                {{ type.language || "N/A" }}
                            </td>
                            <td class="px-6 py-4">
                                {{ type.duration || "N/A" }}
                            </td>
                            <td class="px-6 py-4">
                                {{ type.amount || "N/A" }}
                            </td>
                            <td class="px-6 py-4">
                                {{
                                    type.is_active == "1"
                                        ? "Active"
                                        : "Inactive"
                                }}
                            </td>
                            <td class="px-6 py-4 flex justify-start space-x-4">
                                <button
                                    v-if="userCan('update-paymentTypes')"
                                    @click="editType(type)"
                                    class="text-green-500 hover:text-green-700"
                                >
                                    <PencilSquareIcon class="w-5 h-5" />
                                </button>
                                <button
                                    v-if="userCan('delete-paymentTypes')"
                                    @click="deletePaymentType(type.id)"
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
        <div
            v-if="paymentTypes?.meta?.links"
            class="mt-3 flex justify-center space-x-6"
        >
            <Link
                v-for="link in paymentTypes.meta.links"
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

        <!-- Create Type Modal -->
        <Modal
            :show="createTypeModal"
            @close="closeCreateTypeModal"
            :maxWidth="'md'"
            class="p-6"
        >
            <div class="w-full px-8 py-6">
                <h1
                    class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100"
                >
                    Create New Payment Item
                </h1>
                <form @submit.prevent="submitNewType">
                    <div>
                        <!-- Type -->
                        <div class="mb-4">
                            <label
                                for="type"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Type
                            </label>
                            <input
                                v-model="typeCreationForm.type"
                                id="type"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError
                                :message="typeCreationForm.errors.type"
                                class="mt-2"
                            />
                        </div>

                        <!-- Payment Category Select -->
                        <div class="mb-4">
                            <label
                                for="payment_category_id"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Payment Category
                            </label>
                            <select
                                v-model="typeCreationForm.payment_category_id"
                                id="payment_category_id"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option disabled value="">
                                    Select Category
                                </option>
                                <option
                                    v-for="paymentCategory in paymentCategories"
                                    :key="paymentCategory.id"
                                    :value="paymentCategory.id"
                                >
                                    {{ paymentCategory.name }}
                                </option>
                            </select>
                            <InputError
                                :message="
                                    typeCreationForm.errors[
                                        'payment_category_id'
                                    ]
                                "
                                class="mt-2"
                            />
                        </div>

                        <!-- Study Mode Select -->
                        <div class="mb-4">
                            <label
                                for="study_mode_id"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Study Mode
                            </label>
                            <select
                                v-model="typeCreationForm.study_mode_id"
                                id="study_mode_id"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option disabled value="">
                                    Select Study Mode
                                </option>
                                <option
                                    v-for="mode in studyModes"
                                    :key="mode.id"
                                    :value="mode.id"
                                >
                                    {{ mode.name }}
                                </option>
                            </select>
                            <InputError
                                :message="typeCreationForm.errors.study_mode_id"
                                class="mt-2"
                            />
                        </div>

                        <!-- Pay Duration Select -->
                        <div class="mb-4">
                            <label
                                for="duration"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Payment Duration
                            </label>
                            <select
                                v-model="typeCreationForm.duration"
                                id="duration"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option disabled value="">
                                    Select Pay Duration
                                </option>
                                <option
                                    v-for="duration in [
                                        'One-Time',
                                        'Per-Semester',
                                        'Per-Course',
                                        'Monthly',
                                        'Anually',
                                        'Other',
                                    ]"
                                    :key="duration"
                                    :value="duration"
                                >
                                    {{ duration }}
                                </option>
                            </select>
                            <InputError
                                :message="typeCreationForm.errors.duration"
                                class="mt-2"
                            />
                        </div>

                        <!-- Language -->
                        <div class="mb-4">
                            <label
                                for="language"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Language
                            </label>
                            <input
                                v-model="typeCreationForm.language"
                                id="language"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError
                                :message="typeCreationForm.errors.language"
                                class="mt-2"
                            />
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <label
                                for="amount"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Amount
                            </label>
                            <input
                                v-model="typeCreationForm.amount"
                                id="amount"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError
                                :message="typeCreationForm.errors.amount"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button
                            type="submit"
                            :disabled="typeCreationForm.processing"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-4"
                        >
                            {{
                                typeCreationForm.processing
                                    ? "Creating..."
                                    : "Create Payment Item"
                            }}
                        </button>
                        <button
                            type="button"
                            @click="closeCreateTypeModal"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                        >
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Edit Type Modal -->
        <Modal
            :show="editTypeModal"
            @close="closeEditModal"
            :maxWidth="'md'"
            class="p-6"
        >
            <div class="w-full px-8 py-6">
                <h1
                    class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100"
                >
                    Edit Payment Type
                </h1>
                <form @submit.prevent="submitEditType">
                    <div>
                        <!-- Type -->
                        <div class="mb-4">
                            <label
                                for="type"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Type
                            </label>
                            <input
                                v-model="typeEditForm.type"
                                id="type"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError
                                :message="typeEditForm.errors.type"
                                class="mt-2"
                            />
                        </div>

                        <!-- Payment Category Select -->
                        <div class="mb-4">
                            <label
                                for="payment_category_id"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Payment Category
                            </label>
                            <select
                                v-model="typeEditForm.payment_category_id"
                                id="payment_category_id"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option disabled value="">
                                    Select Category
                                </option>
                                <option
                                    v-for="paymentCategory in paymentCategories"
                                    :key="paymentCategory.id"
                                    :value="paymentCategory.id"
                                >
                                    {{ paymentCategory.name }}
                                </option>
                            </select>
                            <InputError
                                :message="
                                    typeEditForm.errors.payment_category_id
                                "
                                class="mt-2"
                            />
                        </div>

                        <!-- Study Mode Select -->
                        <div class="mb-4">
                            <label
                                for="study_mode_id"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Study Mode
                            </label>
                            <select
                                v-model="typeEditForm.study_mode_id"
                                id="study_mode_id"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option disabled value="">
                                    Select Study Mode
                                </option>
                                <option
                                    v-for="mode in studyModes"
                                    :key="mode.id"
                                    :value="mode.id"
                                >
                                    {{ mode.name }}
                                </option>
                            </select>
                            <InputError
                                :message="typeEditForm.errors.study_mode_id"
                                class="mt-2"
                            />
                        </div>

                        <!-- Pay Duration Select -->
                        <div class="mb-4">
                            <label
                                for="duration"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Payment Duration
                            </label>
                            <select
                                v-model="typeEditForm.duration"
                                id="duration"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option disabled value="">
                                    Select Pay Duration
                                </option>
                                <option
                                    v-for="duration in [
                                        'One-Time',
                                        'Per-Semester',
                                        'Per-Course',
                                        'Monthly',
                                        'Anually',
                                        'Other',
                                    ]"
                                    :key="duration"
                                    :value="duration"
                                >
                                    {{ duration }}
                                </option>
                            </select>
                            <InputError
                                :message="typeEditForm.errors.duration"
                                class="mt-2"
                            />
                        </div>

                        <!-- Language -->
                        <div class="mb-4">
                            <label
                                for="language"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Language
                            </label>
                            <input
                                v-model="typeEditForm.language"
                                id="language"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError
                                :message="typeEditForm.errors.language"
                                class="mt-2"
                            />
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <label
                                for="amount"
                                class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300"
                            >
                                Amount
                            </label>
                            <input
                                v-model="typeEditForm.amount"
                                id="amount"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError
                                :message="typeEditForm.errors.amount"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button
                            type="submit"
                            :disabled="typeEditForm.processing"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-4"
                        >
                            {{
                                typeEditForm.processing
                                    ? "Updating..."
                                    : "Update Payment Type"
                            }}
                        </button>
                        <button
                            type="button"
                            @click="closeEditModal"
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
