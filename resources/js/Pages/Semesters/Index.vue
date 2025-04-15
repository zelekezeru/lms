<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ArrowPathIcon, TrashIcon, EyeIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";

// Extract server-provided props
const { semesters, search: serverSearch } = usePage().props;

// Reactive state for search input
const search = ref(serverSearch || "");

// Refresh indicator
const refreshing = ref(false);

// Function to refresh data
const refreshData = () => {
    refreshing.value = true;
    router.visit(route("semesters.index"), {
        only: ["semesters"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Function to delete a semester with confirmation
const deleteSemester = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("semesters.destroy", { semester: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "Semester has been removed.", "success");
                },
            });
        }
    });
};

// Function to search semesters dynamically
const searchSemesters = () => {
    router.get(
        route("semesters.index"),
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
                Semesters
            </h1>
        </div>

        <!-- Header Toolbar -->
        <div class="flex justify-between items-center mb-3">
            <!-- Search Input -->
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
                    @input="searchSemesters"
                />
            </div>

            <div class="flex space-x-2">
                <!-- <Link
                    :href="route('semesters.create')"
                    class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-green-700"
                >
                    + Add Semester
                </Link> -->

                <button
                    @click="refreshData"
                    class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-blue-700"
                    title="Refresh Data"
                >
                    <ArrowPathIcon
                        class="w-5 h-5 mr-2"
                        :class="{ 'animate-spin': refreshing }"
                    />
                    Refresh
                </button>
            </div>
        </div>

        <!-- Semester Table -->
        <div
            v-if="semesters.data.length > 0"
            class="overflow-x-auto shadow-md sm:rounded-lg"
        >
            <table
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3">Semester</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="semester in semesters.data"
                        :key="semester.id"
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200"
                    >
                        <td
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                        >
                            <Link
                                :href="route('semesters.show', { semester: semester.id })"
                                class="text-blue-500 hover:text-blue-700"
                            >
                                {{ semester.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4">{{ semester.status }}</td>
                        <td class="px-6 py-4 flex">
                            <Link
                                :href="
                                    route('semesters.show', {
                                        semester: semester.id,
                                    })
                                "
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty Message -->
        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
            <p class="text-lg font-medium text-gray-700 dark:text-gray-300">
                No semesters found. 
            </p>
        </div>
    </AppLayout>
</template>
