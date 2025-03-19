<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

// Define the props for the inventory
defineProps({
    inventory: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deleteinventory = (id) => {
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
            router.delete(route("inventories.destroy", { inventory: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The inventory has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto p-6">
            <h1 class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center">
                Inventory Details
            </h1>

            <div class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700">
                <div class="grid sm:grid-cols-2 gap-4 lg:pl-36 sm:gap-2">
                    <div v-for="(value, key) in inventory" :key="key" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400 capitalize">{{ key }}</span>
                        <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ value }}
                        </span>
                    </div>
                </div>

                <!-- Edit and Delete Buttons with permission check -->
                <div class="flex justify-end mt-6 space-x-2">
                    <!-- Edit button -->
                    <Link 
                        v-if="userCan('update-inventories')" 
                        :href="route('inventories.edit', { inventory: inventory.id })" 
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                    </Link>
                    <!-- Delete button -->
                    <button 
                        v-if="userCan('delete-inventories')" 
                        @click="deleteInventory(inventory.id)" 
                        class="text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>