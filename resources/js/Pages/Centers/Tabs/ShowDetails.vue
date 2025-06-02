<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/solid";

// Define props for the center
const props = defineProps({
    center: {
        type: Object,
        required: true,
    },
    coordinator: {
        type: Object,
        required: false,
    },
});

// Delete function for Center
const deleteCenter = (id) => {
    Swal.fire({
        title: $t("centers.delete_confirm_title"),
        text: $t("centers.delete_confirm_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: $t("common.yes"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("centers.destroy", { center: id }), {
                onSuccess: () => {
                    Swal.fire(
                        $t("centers.deleted_title"),
                        $t("centers.deleted_text"),
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <div
        class="p-6 bg-white border border-gray-200 shadow-lg dark:bg-gray-800 rounded-xl dark:border-gray-700"
    >
        <div class="grid gap-4 sm:grid-cols-2">
            <!-- Center Name -->
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('centers.name') }}
                </span>
                <span class="block text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ center.name }}
                </span>
            </div>

            <!-- Center Code -->
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('centers.code') }}
                </span>
                <span class="block text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ center.code }}
                </span>
            </div>

            <!-- Address -->
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('centers.address') }}
                </span>
                <span class="block text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ center.address }}
                </span>
            </div>

            <!-- Coordinator -->
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('centers.coordinator') }}
                </span>
                <span class="block text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ coordinator ? coordinator.name : $t('centers.noCoordinator') }}
                </span>
            </div>

            <!-- Students Count -->
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('centers.students_count') }}
                </span>
                <span class="block text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ center.students_count }}
                </span>
            </div>

            <!-- Status -->
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400">
                    {{ $t('centers.status') }}
                </span>
                <span
                    class="inline-block px-3 py-1 mt-1 text-sm font-semibold rounded"
                    :class="center.status === 'Active'
                        ? 'bg-green-200 text-green-800 dark:bg-green-600 dark:text-white'
                        : 'bg-gray-300 text-gray-800 dark:bg-gray-600 dark:text-white'"
                >
                    {{ center.status }}
                </span>
            </div>
        </div>

        <!-- Edit and Delete Buttons -->
        <div class="flex justify-end col-span-2 mt-4">
            <Link
                v-if="userCan('update-centers')"
                :href="route('centers.edit', { center: center.id })"
                class="inline-flex items-center space-x-2 text-blue-500 hover:text-blue-700"
            >
                <PencilIcon class="w-5 h-5" />
                <span>{{ $t('actions.edit') }}</span>
            </Link>

            <button
                v-if="userCan('delete-centers')"
                @click="deleteCenter(center.id)"
                class="inline-flex items-center ml-4 space-x-2 text-red-500 hover:text-red-700"
            >
                <TrashIcon class="w-5 h-5" />
                <span>{{ $t('actions.delete') }}</span>
            </button>
        </div>
    </div>
</template>
