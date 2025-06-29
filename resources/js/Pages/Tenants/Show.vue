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
        title: $t("tenant.delete_confirm_title"),
        text: $t("tenant.delete_confirm_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: $t("tenant.delete_confirm_confirm"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("tenants.destroy", { tenant: id }), {
                onSuccess: () => {
                    Swal.fire(
                        $t("tenant.delete_success_title"),
                        $t("tenant.delete_success_text"),
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
        <div class="p-6 mx-auto max-w-8xl">
            <h1
                class="mb-6 text-3xl font-semibold text-center text-gray-900 dark:text-gray-100"
            >
                {{ $t("tenant.details") }}
            </h1>

            <div
                class="p-6 border shadow-lg dark:bg-gray-800 rounded-xl dark:border-gray-700"
            >
                <div class="flex justify-center mb-8">
                    <div
                        v-if="!imageLoaded"
                        class="bg-gray-300 rounded-full w-44 h-44 dark:bg-gray-700 animate-pulse"
                    ></div>

                    <img
                        v-show="imageLoaded"
                        class="object-contain bg-gray-400 rounded-full w-44 h-44"
                        :src="tenant.logo"
                        :alt="`Logo of ` + tenant.name"
                        @load="handleImageLoad"
                    />
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:pl-36 sm:gap-2">
                    <!-- Tenant Code -->
                    <div class="flex flex-col">
                        <span
                            class="text-sm text-gray-500 dark:text-gray-400"
                            >{{ $t("tenant.code") }}</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.code }}</span
                        >
                    </div>

                    <!-- Tenant Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("tenant.name") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.name }}</span
                        >
                    </div>

                    <!-- Tenant Email -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("tenant.email") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.email }}</span
                        >
                    </div>

                    <!-- Tenant Phone -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("tenant.phone") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.phone }}</span
                        >
                    </div>

                    <!-- Representative Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("tenant.rep_name") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.contact_person }}</span
                        >
                    </div>

                    <!-- Representative Phone -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("tenant.phone") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.contact_phone }}</span
                        >
                    </div>

                    <!-- Status -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("tenant.status") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            <div v-if="tenant.status == 0" class="text-red-500">
                                {{ $t("tenant.inactive") }}
                            </div>
                            <div v-else class="text-green-500">
                                {{ $t("tenant.active") }}
                            </div>
                        </span>
                    </div>

                    <!-- Payment -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("tenant.paid_status") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            <div v-if="tenant.paid == 0" class="text-red-500">
                                {{ $t("tenant.not_paid") }}
                            </div>
                            <div v-else class="text-green-500">
                                {{ $t("tenant.paid") }}
                            </div></span
                        >
                    </div>

                    <!-- Password -->
                    <div
                        v-if="tenant.password_changed === 0"
                        class="flex flex-col"
                    >
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("tenant.default_password") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.default_password }}</span
                        >
                    </div>

                    <!-- Aggrement -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("tenant.agreement") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ tenant.aggrement }}</span
                        >
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <!-- Edit Button -->
                    <div v-if="userCan('update-tenants')">
                        <Link
                            :href="
                                route('tenants.edit', {
                                    tenant: tenant.id,
                                })
                            "
                            class="flex items-center space-x-1 text-blue-500 hover:text-blue-700"
                        >
                            <PencilIcon class="w-5 h-5" />
                            <span>{{ $t("tenant.edit") }}</span>
                        </Link>
                    </div>
                    <!-- Delete Button -->
                    <div v-if="userCan('delete-tenants')">
                        <button
                            @click="deletetenant(tenant.id)"
                            class="flex items-center space-x-1 text-red-500 hover:text-red-700"
                        >
                            <TrashIcon class="w-5 h-5" />
                            <span>{{ $t("tenant.delete") }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>