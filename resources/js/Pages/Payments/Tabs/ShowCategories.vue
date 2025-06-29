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
    paymentCategories: {
        type: Object, // Changed from Array to Object to match pagination structure
        required: true,
    },
});

const refreshing = ref(false);
const createCategoryModal = ref(false);
const editCategoryModal = ref(false);
const currentEditId = ref(null);

const categoryCreationForm = useForm({
    name: "",
    description: "",
    is_active: "1",
});

const categoryEditForm = useForm({
    name: props.paymentCategories.name || "",
    description: props.paymentCategories.description || "",
    is_active: props.paymentCategories.is_active || "1",
});

const refreshData = () => {
    refreshing.value = true;
    router.get(window.location.href, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => (refreshing.value = false),
    });
};

const deletePaymentCategory = (id) => {
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
            router.delete(route("paymentCategories.destroy", { paymentCategory: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "The category has been deleted.", "success");
                    refreshData();
                },
                onError: () => {
                    Swal.fire("Error!", "Failed to delete the category.", "error");
                    refreshData();
                },
            });
        }
    });
};

const submitNewCategory = () => {
    categoryCreationForm.post(route("paymentCategories.store"), {
        onSuccess: () => {
            categoryCreationForm.reset();
            createCategoryModal.value = false;
            refreshData();
        },
    });
};

const editCategory = (category) => {
    currentEditId.value = category.id;
    categoryEditForm.name = category.name; // Fixed property assignment
    categoryEditForm.description = category.description;
    categoryEditForm.is_active = category.is_active.toString();
    editCategoryModal.value = true;
};

const submitEditCategory = () => {
    if (!currentEditId.value) return;
    categoryEditForm.put(route("paymentCategories.update", { paymentCategory: currentEditId.value }), {
        onSuccess: () => {
            editCategoryModal.value = false;
            categoryEditForm.reset();
            refreshData();
        },
    });
};

const closeCreateCategoryModal = () => {
    createCategoryModal.value = false;
    categoryCreationForm.reset();
};

const closeEditModal = () => {
    editCategoryModal.value = false;
    categoryEditForm.reset();
};
</script>

<template>
    <div>
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Categories</h2>
            <button @click="createCategoryModal = true" class="flex text-green-600 hover:text-green-800">
                <PlusCircleIcon class="mx-2 w-8 h-8" />
                
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
            <div v-if="!paymentCategories || !paymentCategories.length" class="text-center text-gray-500 dark:text-gray-400">
                No category information available.
            </div>
            <div v-else>
                <Table>
                    <TableHeader>
                        <tr>
                            <Thead>No.</Thead>
                            <Thead>Title</Thead>
                            <Thead>Description</Thead>
                            <Thead>Status</Thead>
                            <Thead>Actions</Thead>
                        </tr>
                    </TableHeader>
                    <tbody>
                        <TableZebraRows v-for="(category, index) in paymentCategories" :key="category.id">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ index + 1 }}
                            </th>
                            <td class="px-6 py-4">{{ category.name }}</td>
                            <td class="px-6 py-4">{{ category.description }}</td>
                            <td class="px-6 py-4">
                                <span
                                    v-if="category.is_deleted"
                                    class="inline-block px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded dark:bg-red-900 dark:text-red-200"
                                >
                                    Deleted
                                </span>
                                <span v-else>
                                    <span
                                        :class="{
                                            'inline-block px-2 py-1 text-xs font-semibold rounded': true,
                                            'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200': category.is_active == '1',
                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200': category.is_active != '1'
                                        }"
                                    >
                                        {{ category.is_active == "1" ? "Active" : "Inactive" }}
                                    </span>
                                </span>
                            </td>
                            <td class="px-6 py-4 flex justify-start space-x-4">
                                <button
                                    v-if="userCan('update-paymentCategories')"
                                    @click="editCategory(category)"
                                    class="text-green-500 hover:text-green-700"
                                >
                                    <PencilSquareIcon class="w-5 h-5" />
                                </button>
                                <button
                                    v-if="userCan('delete-paymentCategories')"
                                    @click="deletePaymentCategory(category.id)"
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
        <div v-if="paymentCategories.meta?.links" class="mt-3 flex justify-center space-x-6">
            <Link
                v-for="link in paymentCategories.meta.links"
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

        <!-- Create Category Modal -->
        <Modal :show="createCategoryModal" @close="closeCreateCategoryModal" :maxWidth="'md'" class="p-6">
            <div class="w-full px-8 py-6">
                <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Create New Category</h1>
                <form @submit.prevent="submitNewCategory">
                    <div>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Category Title</label>
                            <input
                                v-model="categoryCreationForm.name"
                                id="name"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError :message="categoryCreationForm.errors.name" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Category Description</label>
                            <input
                                v-model="categoryCreationForm.description"
                                id="description"
                                type="text"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            />
                            <InputError :message="categoryCreationForm.errors.description" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label for="is_active" class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Status</label>
                            <select
                                v-model="categoryCreationForm.is_active"
                                id="is_active"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none"
                            >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <InputError :message="categoryCreationForm.errors.is_active" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" :disabled="categoryCreationForm.processing" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-4">
                            {{ categoryCreationForm.processing ? "Creating..." : "Create Category" }}
                        </button>
                        <button type="button" @click="closeCreateCategoryModal" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Edit Category Modal -->
        <Modal :show="editCategoryModal" @close="closeEditModal" :maxWidth="'md'" class="p-6">
            <form @submit.prevent="submitEditCategory">
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Category</h2>
                        <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <XMarkIcon class="w-5 h-5" />
                        </button>
                    </div>

                    <div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Category Description</label>
                            <input v-model="categoryEditForm.name" type="text" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none" />
                            <InputError :message="categoryEditForm.errors.name" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Category Description</label>
                            <input v-model="categoryEditForm.description" type="text" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none" />
                            <InputError :message="categoryEditForm.errors.description" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Status</label>
                            <select v-model="categoryEditForm.is_active" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <InputError :message="categoryEditForm.errors.is_active" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <SecondaryButton @click="closeEditModal">Cancel</SecondaryButton>
                        <PrimaryButton :disabled="categoryEditForm.processing" :class="{ 'opacity-25': categoryEditForm.processing }">
                            Update Category
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </Modal>
    </div>
</template>
