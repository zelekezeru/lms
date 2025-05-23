<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

// Define the props for the inventoryCategory
defineProps({
    inventoryCategory: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deleteInventoryCategory = (id) => {
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
            router.delete(
                route("inventoryCategories.destroy", { inventoryCategory: id }),
                {
                    onSuccess: () => {
                        Swal.fire(
                            "Deleted!",
                            "The inventoryCategory has been deleted.",
                            "success"
                        );
                    },
                }
            );
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-8xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                Inventory Supplier Details
            </h1>

            <div
                class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
            >
                <div class="grid sm:grid-cols-2 gap-4 lg:pl-36 sm:gap-2">
                    <!-- Inventory Category ID -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >ID</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ inventoryCategory.id }}</span
                        >
                    </div>

                    <!-- Inventory Category Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ inventoryCategory.name }}</span
                        >
                    </div>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <Link
                        v-if="userCan('update-inventory-categories')"
                        :href="
                            route('inventoryCategories.edit', {
                                inventoryCategory: inventoryCategory.id,
                            })
                        "
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                        <span>Edit</span>
                    </Link>
                    <button
                        v-if="userCan('delete-inventory-categories')"
                        @click="deleteInventoryCategory(inventoryCategory.id)"
                        class="text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
