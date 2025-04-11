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
    tenants: {
        type: Object,
        required: true,
    },
    sortInfo: {
        type: Object,
    },
});

const refreshing = ref(false);

const refreshData = () => {
    refreshing.value = true;
    router.flush("/tenants", { method: "get" });

    router.visit(route("tenants.index"), {
        only: ["tenants"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Delete function with SweetAlert confirmation
const deletetenant = (id) => {
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

const search = ref(usePage().props.search || "");

const searchTenants = () => {
    router.get(
        route("tenants.index"),
        { search: search.value },
        { preserveState: true }
    );
};
</script>

<template>
    <AppLayout>
        <!-- Page Title -->
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Tenants
            </h1>
        </div>

        <!-- Header Toolbar -->
        <div class="flex justify-between items-center mb-3">
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
                    v-model="search"
                    placeholder="Search..."
                    class="pl-10 p-2 border rounded-lg text-gray-900 dark:text-white dark:bg-gray-700"
                    @input="searchTenants"
                />
            </div>

            <div class="flex space-x-2">
                <Link
                    :href="route('tenants.create')"
                    class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    + Add Tenant
                </Link>

                <button
                    @click="refreshData"
                    class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    title="Refresh Data"
                >
                    <ArrowPathIcon
                        class="w-5 h-5 mr-2"
                        :class="{ 'animate-spin': refreshing }"
                    />
                    Refresh Data
                </button>
            </div>
        </div>

        <!-- Tenants Table OR No Results Message -->
        <div v-if="tenants.data.length > 0" class="overflow-x-auto shadow-md sm:rounded-lg">
            <Table>
                <TableHeader>
                    <tr>
                        <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'name'">Name</Thead>
                        <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'code'">Code</Thead>
                        <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'email'">Email</Thead>
                        <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'phone'">Phone</Thead>
                        <Thead>Action</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <tr
                        v-for="tenant in tenants.data"
                        :key="tenant.id"
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200"
                    >
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <Link :href="route('tenants.show', { tenant: tenant.id })">
                                {{ tenant.name }}
                            </Link>
                        </th>
                        <td class="px-6 py-4">
                            {{ tenant.code }}
                        </td>
                        <td class="px-6 py-4">{{ tenant.email }}</td>
                        <td class="px-6 py-4">{{ tenant.phone }}</td>
                        <td class="px-6 py-4 flex justify-between">
                            <Link :href="route('tenants.show', { tenant: tenant.id })" class="text-blue-500 hover:text-blue-700">
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                            <Link :href="route('tenants.edit', { tenant: tenant.id })" class="text-green-500 hover:text-green-700">
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                            <button @click="deletetenant(tenant.id)" class="text-red-500 hover:text-red-700">
                                <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </Table>
        </div>

        <!-- No Search Results Message -->
        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
            <p>No search results found.</p>
        </div>

        <!-- Pagination Links -->
        <div class="mt-3 flex justify-center space-x-2">
            <Link
                v-for="link in tenants.meta.links"
                :key="link.label"
                :href="link.url ? `${link.url}&search=${search}` : '#'"
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

