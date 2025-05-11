<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    EyeIcon,
    TrashIcon,
    ArrowPathIcon,
    Squares2X2Icon,
    TableCellsIcon,
} from "@heroicons/vue/24/solid";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";

defineProps({
    tracks: {
        type: Object,
        required: true,
    },
    sortInfo: {
        type: Object,
    },
});

const refreshing = ref(false);
const search = ref(usePage().props.search || "");
const viewMode = ref("table"); // Default to table view

// Refresh function
const refreshData = () => {
    refreshing.value = true;
    router.flush("/tracks", { method: "get" });

    router.visit(route("tracks.index"), {
        only: ["tracks"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Search function
const searchTracks = () => {
    router.get(
        route("tracks.index"),
        { ...route().params, search: search.value },
        { preserveState: true }
    );
};

// Delete function with SweetAlert confirmation
const deleteTrack = (id) => {
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
            router.delete(route("tracks.destroy", { track: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The track has been deleted.",
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
        <h1
            class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
        >
            Track Details
        </h1>

        <!-- View Mode Toggle -->
        <div class="flex justify-between items-center mb-3">
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
                    placeholder="Search Tracks..."
                    class="pl-10 p-2 border rounded-lg text-gray-900 dark:text-white dark:bg-gray-700"
                    @input="searchTracks"
                />
            </div>

            <div class="flex space-x-6">
                <Link
                    v-if="userCan('create-tracks')"
                    :href="route('tracks.create')"
                    class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    + Add Track
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
                <!-- Toggle View Button -->
                <button
                    @click="viewMode = viewMode === 'table' ? 'card' : 'table'"
                    class="inline-flex items-center rounded-md bg-gray-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-gray-700"
                    title="Toggle View"
                >
                    <component
                        :is="
                            viewMode === 'table'
                                ? Squares2X2Icon
                                : TableCellsIcon
                        "
                        class="w-5 h-5"
                    />
                </button>
            </div>
        </div>

        <!-- Table View -->
        <div
            v-if="viewMode === 'table'"
            class="overflow-x-auto shadow-md sm:rounded-lg mt-3"
        >
            <Table>
                <TableHeader>
                    <tr>
                        <Thead
                            :sortable="true"
                            :sort-info="sortInfo"
                            :sortColumn="'name'"
                            >Track Name</Thead
                        >
                        <Thead
                            :sortable="true"
                            :sort-info="sortInfo"
                            :sortColumn="'code'"
                            >Code</Thead
                        >
                        <Thead
                            :sortable="true"
                            :sort-info="sortInfo"
                            :sortColumn="'description'"
                            >Description</Thead
                        >
                        <Thead>Actions</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows
                        v-for="track in tracks.data"
                        :key="track.id"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                        >
                            <Link
                                :href="
                                    route('tracks.show', { track: track.id })
                                "
                            >
                                {{ track.name }}
                            </Link>
                        </th>
                        <td class="px-6 py-4">{{ track.code }}</td>
                        <td class="px-6 py-4">{{ track.description }}</td>
                        <td class="px-6 py-4 flex space-x-6">
                            <div v-if="userCan('view-tracks')">
                                <Link
                                    :href="
                                        route('tracks.show', {
                                            track: track.id,
                                        })
                                    "
                                    class="text-blue-500 hover:text-blue-700"
                                >
                                    <EyeIcon class="w-5 h-5" />
                                </Link>
                            </div>
                            <div v-if="userCan('update-tracks')">
                                <Link
                                    :href="
                                        route('tracks.edit', {
                                            track: track.id,
                                        })
                                    "
                                    class="text-green-500 hover:text-green-700"
                                >
                                    <PencilSquareIcon class="w-5 h-5" />
                                </Link>
                            </div>
                        </td>
                    </TableZebraRows>
                </tbody>
            </Table>
        </div>

        <!-- Card View -->
        <div
            v-else
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-3"
        >
            <div
                v-for="track in tracks.data"
                :key="track.id"
                class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-lg shadow-md p-6 flex flex-col transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg"
            >
                <h3
                    class="text-lg font-semibold text-gray-900 dark:text-white mb-2"
                >
                    {{ track.name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                    <strong>Code:</strong> {{ track.code }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 flex-grow">
                    {{ track.description }}
                </p>
                <div class="mt-4 flex space-x-4">
                    <Link
                        v-if="userCan('view-tracks')"
                        :href="route('tracks.show', { track: track.id })"
                        class="text-blue-500 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        <EyeIcon class="w-5 h-5" />
                    </Link>
                    <Link
                        v-if="userCan('update-tracks')"
                        :href="route('tracks.edit', { track: track.id })"
                        class="text-green-500 hover:text-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    >
                        <PencilSquareIcon class="w-5 h-5" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- Pagination Links -->
        <div class="mt-3 flex justify-center space-x-6">
            <Link
                v-for="link in tracks.meta.links"
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
