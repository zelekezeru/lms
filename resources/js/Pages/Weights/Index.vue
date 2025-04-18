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
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";

defineProps({
    weights: Array,
    sortInfo: Object,
});

const refreshing = ref(false);
const search = ref(usePage().props.search || "");

// Refresh function
const refreshData = () => {
    refreshing.value = true;
    router.visit(route("weights.index"), {
        only: ["weights"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Search function
const searchWeights = () => {
    router.get(
        route("weights.index"),
        { ...route().params, search: search.value },
        { preserveState: true }
    );
};

// Delete function with SweetAlert confirmation
const deleteWeight = (id) => {
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
            router.delete(route("weights.destroy", { weight: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The weight has been deleted.",
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
        <h1 class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center">
            Weights
        </h1>

        <!-- Search Bar and Button Section -->
        <div class="flex justify-between items-center mb-3">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M9 17A8 8 0 109 1a8 8 0 000 16z"/>
                    </svg>
                </span>
                <input
                    type="text"
                    v-model="search"
                    placeholder="Search Weights..."
                    class="pl-10 p-2 border rounded-lg text-gray-900 dark:text-white dark:bg-gray-700"
                    @input="searchWeights"
                />
            </div>

            <div class="flex space-x-6">
                <Link
                    :href="route('weights.create')"
                    class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    + Add Weight
                </Link>

                <button
                    @click="refreshData"
                    class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    title="Refresh Data"
                >
                    <ArrowPathIcon class="w-5 h-5 mr-2" :class="{ 'animate-spin': refreshing }"/>
                    Refresh Data
                </button>
            </div>
        </div>

        <div class="overflow-x-auto shadow-md sm:rounded-lg mt-3">
            <Table>
                <TableHeader>
                    <tr>
                        <Thead>ID</Thead>
                        <Thead>Name</Thead>
                        <Thead>Weight Point</Thead>
                        <Thead>Actions</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows
                        v-for="weight in weights.data"
                        :key="weight.id"
                    >
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <Link :href="route('weights.show', { weight: weight.id })">
                                {{ weight.id }}
                            </Link>
                        </th>
                        <td class="px-6 py-4">{{ weight.name }}</td>
                        <td class="px-6 py-4">{{ weight.weight_point }}</td>
                        <td class="px-6 py-4 flex space-x-6">
                            <Link :href="route('weights.show', { weight: weight.id })" class="text-blue-500 hover:text-blue-700">
                                <EyeIcon class="w-5 h-5"/>
                            </Link>
                            <Link :href="route('weights.edit', { weight: weight.id })" class="text-green-500 hover:text-green-700">
                                <PencilSquareIcon class="w-5 h-5"/>
                            </Link>
                            <button @click="deleteWeight(weight.id)" class="text-red-500 hover:text-red-700">
                                <TrashIcon class="w-5 h-5"/>
                            </button>
                        </td>
                    </TableZebraRows>
                </tbody>
            </Table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-3 flex justify-center space-x-6">
            <Link
                v-for="link in weights.meta.links"
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
