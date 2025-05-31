<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { EyeIcon, TrashIcon, ArrowPathIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";

import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "../../Components/TableZebraRows.vue";

defineProps({
    permissions: {
        type: Object,
        required: true,
    },
});

const refreshing = ref(false);
const searchQuery = ref(""); // for search input

// Refresh data
const refreshData = () => {
    refreshing.value = true;
    router.flush("/permissions", { method: "get" });

    router.visit(route("permissions.index"), {
        only: ["permissions"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Handle search functionality
const searchPermissions = () => {
    router.get(route("permissions.index"), { search: searchQuery.value }, { preserveState: true });
};

// Delete function with SweetAlert confirmation
const deletepermission = (id) => {
    Swal.fire({
        title: $t("permission.delete_confirm_title"),
        text: $t("permission.delete_confirm_text"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: $t("common.yes"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("permissions.destroy", { permission: id }), {
                onSuccess: () => {
                    Swal.fire(
                        $t("permission.deleted_title"),
                        $t("permission.deleted_text"),
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
        <!-- Page Title -->
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $t('permission.title') }}
            </h1>
        </div>

        <!-- Search Bar and Header Toolbar -->
        <div class="flex items-center justify-between mb-3">
            <!-- Search Bar with Icon -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg
                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-4.35-4.35M9 17A8 8 0 109 1a8 8 0 000 16z"
                        />
                    </svg>
                </span>
                <input
                    type="text"
                    v-model="searchQuery"
                    :placeholder="$t('permission.search')"
                    class="p-2 pl-10 text-gray-900 border rounded-lg dark:text-white dark:bg-gray-700"
                    @input="searchPermissions"
                />
            </div>

            <!-- Button Group (Add & Refresh) -->
            <div class="flex space-x-6">
                <Link
                    :href="route('permissions.create')"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-green-600 rounded-md hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    + {{ $t('permission.add') }}
                </Link>

                <button
                    @click="refreshData"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-blue-800 rounded-md hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    :title="$t('permission.refresh')"
                >
                    <ArrowPathIcon
                        class="w-5 h-5 mr-2"
                        :class="{ 'animate-spin': refreshing }"
                    />
                    {{ $t('permission.refresh') }}
                </button>
            </div>
        </div>

        <!-- Permissions Table -->
        <Table>
            <TableHeader>
                <tr>
                    <th scope="col" class="px-6 py-3">{{ $t('permission.name') }}</th>
                    <th scope="col" class="px-6 py-3">{{ $t('permission.action') }}</th>
                </tr>
            </TableHeader>
            <tbody>
                <TableZebraRows v-for="permission in permissions.data" :key="permission.id">
                    <th
                        scope="row"
                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                    >
                        <Link :href="route('permissions.show', { permission: permission.id })">
                            {{ permission.name }}
                        </Link>
                    </th>
                    <td class="flex items-center justify-around py-4">
                        <Link
                            :href="route('permissions.show', { permission: permission.id })"
                            class="text-blue-500 hover:text-blue-700"
                            :title="$t('permission.view')"
                        >
                            <EyeIcon class="w-5 h-5" />
                        </Link>
                        <Link
                            :href="route('permissions.edit', { permission: permission.id })"
                            class="text-green-500 hover:text-green-700"
                            :title="$t('permission.edit')"
                        >
                            <PencilSquareIcon class="w-5 h-5" />
                        </Link>
                        <button
                            @click="deletepermission(permission.id)"
                            class="text-red-500 hover:text-red-700"
                            :title="$t('permission.delete')"
                        >
                            <TrashIcon class="w-5 h-5" />
                            <span>{{ $t('permission.delete') }}</span>
                        </button>
                    </td>
                </TableZebraRows>
            </tbody>
        </Table>

        <!-- Pagination Links -->
        <div class="flex justify-center mt-3 space-x-6">
            <Link
                v-for="link in permissions.links"
                :key="link.label"
                :href="link.url || '#'"
                class="p-2 px-4 text-sm font-medium transition-colors rounded-lg"
                :class="{
                    'text-gray-700 dark:text-gray-400': true,
                    'cursor-not-allowed opacity-50': !link.url,
                    '!bg-gray-100 !dark:bg-gray-800': link.active,
                }"
                v-html="link.label"
            />
        </div>
    </AppLayout>
</template>

