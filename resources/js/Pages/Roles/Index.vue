<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { EyeIcon, TrashIcon, ArrowPathIcon, ShieldCheckIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";

import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "../../Components/TableZebraRows.vue";

defineProps({
    roles: {
        type: Object,
        required: true,
    },
});

const refreshing = ref(false);

const refreshData = () => {
    refreshing.value = true;
    router.flush("/roles", { method: "get" });

    router.visit(route("roles.index"), {
        only: ["roles"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Delete function with SweetAlert confirmation
const deleterole = (id) => {
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
            router.delete(route("roles.destroy", { role: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The role has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};

const search = ref(usePage().props.search || "");

const searchRoles = () => {
    router.get(
        route("roles.index"),
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
                Roles
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
                    @input="searchRoles"
                    class="pl-10 p-2 border rounded-lg text-gray-900 dark:text-white dark:bg-gray-700"
                />
            </div>

            <div class="flex space-x-6">
                <Link
                    :href="route('roles.create')"
                    class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    + Add Role
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

        <!-- Roles Table OR No Results Message -->
         <!-- Sections Table -->
        
        <div v-if="roles.data.length > 0" class="overflow-x-auto shadow-md sm:rounded-lg" >
        
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="role in roles.data"
                    :key="role.id"
                    class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4 border dark:border-gray-700"
                >
                    <Link
                        v-if="role && role.id"
                        :href="route('roles.show', { role: role.id })"
                        class="ml-1 text-blue-600"
                    >
                        <div
                            class="mb-3 flex items-center text-gray-700 dark:text-gray-300"
                        >
                            <ViewColumnsIcon class="w-5 h-5 mr-2 text-teal-500" />
                            <span class="font-semibold">Role:</span>
                            <span class="ml-1">{{ role.name }}</span>
                        </div>

                        <div
                            class="mb-3 flex text-gray-700 dark:text-gray-300  space-x-10 mt-4"
                        >

                            <Link
                                :href="
                                    route('roles.permissions', {
                                        role: role.id,
                                    })
                                "
                                class="inline-flex items-center rounded-md text-white px-4 py-2 text-xs font-semibold uppercase"
                            >
                                <ShieldCheckIcon class="w-5 h-5 mr-2 text-teal-500" />
                                Manage Permissions
                            </Link>
                            
                            <Link
                                :href="
                                    route('roles.show', { role: role.id })
                                "
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                            <Link
                                v-if="userCan('update-roles')"
                                :href="
                                    route('roles.edit', { role: role.id })
                                "
                                class="text-green-500 hover:text-green-700"
                            >
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                        </div>
                    </Link>
                    <div v-else class="text-red-500">Role data is missing or invalid.</div>
                </div>
            </div>
        </div>

        <!-- No Search Results Message -->
        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
            <p class="text-lg font-semibold">No roles found.</p>
            <p class="text-sm">Try adjusting your search or adding a new role.</p>
        </div>

        <!-- Pagination Links -->
        <div class="mt-3 flex justify-center space-x-6">
            <Link
                v-for="link in roles.links"
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
