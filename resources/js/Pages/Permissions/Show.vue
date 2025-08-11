<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { useI18n } from "vue-i18n";

// Define the props for the permission
defineProps({
    permission: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();
// Delete function with SweetAlert confirmation
const deletePermission = (id) => {
    Swal.fire({
        title: t("common.delete_confirm"),
        text: t("common.delete_confirm_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: t("common.delete_confirm_button"),
        cancelButtonText: t("common.delete_cancel_button"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("permissions.destroy", { permission: id }), {
                onSuccess: () => {
                    Swal.fire(
                        t("common.delete_success"),
                        t("permissions.deleted_succesfully_message"),
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <Head :title="$t('permission.detailsTitle')" />
    <AppLayout>
        <div class="p-6 mx-auto max-w-8xl">
            <h1
                class="mb-6 text-3xl font-semibold text-center text-gray-900 dark:text-gray-100"
            >
                {{ permission.name }} {{ $t("permission.permission") }}
            </h1>

            <div
                class="p-6 border shadow-lg dark:bg-gray-800 rounded-xl dark:border-gray-700"
            >
                <div class="grid grid-cols-2 gap-4">
                    <!-- Permission ID -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("permission.id") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ permission.id }}
                        </span>
                    </div>

                    <!-- Permission Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $t("permission.name") }}
                        </span>
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ permission.name }}
                        </span>
                    </div>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <Link
                        :href="
                            route('permissions.edit', {
                                permission: permission.id,
                            })
                        "
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                        <span>{{ $t("permission.edit") }}</span>
                    </Link>
                    <button
                        @click="deletePermission(permission.id)"
                        class="text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                        <span>{{ $t("permission.delete") }}</span>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
