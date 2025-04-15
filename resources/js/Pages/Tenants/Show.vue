<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

// Define the props for the tenant
defineProps({
    tenant: {
        type: Object,
        required: true,
    },
});

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

// Delete function with SweetAlert confirmation
const deleteTenant = (id) => {
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
            router.delete(route("tenants.destroy", { tenant: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The tenant has been deleted.",
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
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                Tenant Details
            </h1>

            <div
                class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
            >
                <div class="flex justify-center mb-8">
                    <div
                        v-if="!imageLoaded"
                        class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                    ></div>
                    
                    <img
                        v-show="imageLoaded"
                        class="rounded-full w-44 h-44 object-contain bg-gray-400"
                        :src="tenant.logo"
                        :alt="`Logo of ` + tenant.name"
                        @load="handleImageLoad"
                    />
                </div>
                <div class="grid sm:grid-cols-2 gap-4 lg:pl-36 sm:gap-2">
                    <!-- Tenant Code -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Code</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.code }}</span
                        >
                    </div>

                    <!-- Tenant Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Institution Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.name }}</span
                        >
                    </div>

                    <!-- Tenant Email -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Institution Email</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.email }}</span
                        >
                    </div>

                    <!-- Tenant Phone -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Institution Phone</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.phone }}</span
                        >
                    </div>

                    <!-- Representative Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Representative Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.contact_person }}</span
                        >
                    </div>

                    <!-- Representative Phone -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Representative Phone</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.contact_phone }}</span
                        >
                    </div>
                    
                    <!-- Status -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Status</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                            <div v-if="tenant.status == 0" class="text-red-500">
                                Inactive
                            </div>
                            <div v-else class="text-green-500">
                                Active
                            </div>
                        </span>
                    </div>
                    
                    <!-- Payment -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Payment</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >
                            <div v-if="tenant.paid == 0" class="text-red-500">
                                Not Paid
                            </div>
                            <div v-else class="text-green-500">
                                Paid
                            </div></span
                        >
                    </div>
                    
                    <!-- Password -->
                    <div v-if="tenant.password_changed === 0" class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Default Password</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.default_password }}</span
                        >
                    </div>
                                                      
                    <!-- Aggrement -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Aggrement</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.aggrement }}</span
                        >
                    </div>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <Link
                        :href="
                            route('tenants.edit', { tenant: tenant.id })
                        "
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                    </Link>
                    <button
                        @click="deleteTenant(tenant.id)"
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
