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
import Thead from "@/Components/Thead.vue";

defineProps({
    users: {
        type: Object,
        required: true,
    },
});

const refreshing = ref(false);

const refreshData = () => {
    refreshing.value = true;
    router.flush("/users", { method: "get" });

    router.visit(route("users.index"), {
        only: ["users"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Delete function with SweetAlert confirmation
const deleteuser = (id) => {
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
            router.delete(route("users.destroy", { user: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The user has been deleted.",
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
                users
            </h1>
        </div>

        <!-- Header Toolbar -->
        <div class="flex justify-between items-center mb-3">
            <Link
                v-if="userCan('create-users')"
                :href="route('users.create')"
                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 text-white dark:bg-gray-700 dark:text-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Add New user
            </Link>
            <button
                @click="refreshData"
                class="inline-flex items-center rounded-md border border-transparent bg-blue-800 text-white dark:bg-blue-700 dark:text-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                title="Refresh Data"
            >
                <ArrowPathIcon
                    class="w-5 h-5 mr-2"
                    :class="{ 'animate-spin': refreshing }"
                />
                Refresh Data
            </button>
        </div>

        <!-- users Table -->
        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <Table>
                <TableHeader>
                    <tr>
                        <Thead>Name</Thead>
                        <Thead>Email</Thead>
                        <Thead>Role</Thead>
                        <Thead>Job Position</Thead>
                        <Thead>Action</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <tr
                        v-for="user in users.data"
                        :key="user.id"
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700"
                    >
                        <td
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                        >
                            <Link
                                :href="route('users.show', { user: user.id })"
                            >
                                {{ user.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4">
                            {{ user.email }}
                        </td>
                        <td class="px-6 py-4">
                            {{ user.userRole }}
                        </td>
                        <td class="px-6 py-4">
                            {{ user.jobPosition }}
                        </td>
                        <td class="flex px-6 py-4 gap-3">
                            <Link
                                :href="route('users.show', { user: user.id })"
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                            <Link
                                v-if="userCan('update-users')"
                                :href="route('users.edit', { user: user.id })"
                                class="text-green-500 hover:text-green-700"
                            >
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                            <button
                                v-if="userCan('delete-users')"
                                @click="deleteuser(user.id)"
                                class="text-red-500 hover:text-red-700 flex items-center gap-1"
                            >
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </Table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-3 flex justify-center space-x-6">
            <Link
                v-for="link in users.meta.links"
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
    </AppLayout>
</template>
