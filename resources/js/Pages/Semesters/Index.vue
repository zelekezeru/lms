<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ref } from "vue";
import {
    ArrowPathIcon,
    TrashIcon,
    EyeIcon,
    PencilSquareIcon,
    Squares2X2Icon,
    TableCellsIcon,
} from "@heroicons/vue/24/solid";

import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import Thead from "@/Components/Thead.vue";

const { semesters, search: serverSearch } = usePage().props;
const search = ref(serverSearch || "");
const refreshing = ref(false);
const viewMode = ref("card");

const refreshData = () => {
    refreshing.value = true;
    router.visit(route("semesters.index"), {
        only: ["semesters"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

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
                    Swal.fire(
                        "Deleted!",
                        "Semester has been removed.",
                        "success"
                    );
                },
            });
        }
    });
};

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
        <!-- Title -->
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $t("semester.title") }}
            </h1>
        </div>

        <!-- Toolbar -->
        <div
            class="flex flex-col items-center justify-between gap-4 mb-4 md:flex-row"
        >
            <!-- Search -->
            <div class="relative w-full max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg
                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
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
                    :placeholder="$t('semester.search')"
                    class="w-full p-2 pl-10 text-gray-900 border rounded-lg dark:text-white dark:bg-gray-700"
                    @input="searchSemesters"
                />
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <Link
                    v-if="userCan('create-semesters')"
                    :href="route('semesters.create')"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-green-600 rounded-md hover:bg-green-700"
                >
                    + Add Semester
                </Link>
                <!-- Refresh -->
                <button
                    @click="refreshData"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-blue-800 rounded-md hover:bg-blue-700"
                    :title="$t('semester.refresh')"
                >
                    <ArrowPathIcon
                        class="w-5 h-5 mr-2"
                        :class="{ 'animate-spin': refreshing.value }"
                    />
                    {{ $t("semester.refresh") }}
                </button>

                <!-- View Toggle -->
                <button
                    @click="viewMode = viewMode === 'table' ? 'card' : 'table'"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-gray-600 rounded-md hover:bg-gray-700"
                    :title="$t('semester.toggleView')"
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
            v-if="viewMode === 'table' && semesters.data.length > 0"
            class="overflow-x-auto shadow-md sm:rounded-lg"
        >
            <Table>
                <TableHeader>
                    <tr>
                        <Thead>{{ $t("semester.semester") }}</Thead>
                        <Thead>{{ $t("semester.year") }}</Thead>
                        <Thead>{{ $t("semester.startDate") }}</Thead>
                        <Thead>{{ $t("semester.endDate") }}</Thead>
                        <Thead>{{ $t("semester.status") }}</Thead>
                        <Thead>{{ $t("semester.action") }}</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <tr
                        v-for="semester in semesters.data"
                        :key="semester.id"
                        class="border-b odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 dark:border-gray-700"
                    >
                        <td
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                        >
                            <Link
                                :href="
                                    route('semesters.show', {
                                        semester: semester.id,
                                    })
                                "
                                class="text-blue-500 hover:text-blue-700"
                            >
                                {{ semester.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4">
                            {{ semester.year?.name ?? "N/A" }}
                        </td>
                        <td class="px-6 py-4">
                            {{
                                new Date(
                                    semester.start_date
                                ).toLocaleDateString()
                            }}
                        </td>
                        <td class="px-6 py-4">
                            {{
                                new Date(semester.end_date).toLocaleDateString()
                            }}
                        </td>
                        <td>
                            <span
                                class="px-2 py-2 text-sm rounded"
                                :class="
                                    semester.status === 'Active'
                                        ? 'bg-green-400 text-green-800 dark:text-gray-200'
                                        : 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                "
                            >
                                {{ semester.status }}
                            </span>
                        </td>
                        <td class="flex px-6 py-4">
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
            </Table>
        </div>

        <!-- Card View -->
        <div
            v-if="viewMode === 'card' && semesters.data.length > 0"
            class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3"
        >
            <div
                v-for="semester in semesters.data"
                :key="semester.id"
                class="flex flex-col justify-between p-5 transition bg-white border border-gray-200 shadow-sm dark:bg-gray-800 rounded-2xl dark:border-gray-700 hover:shadow-md"
            >
                <div>
                    <h2
                        class="my-4 text-xl font-semibold text-gray-900 dark:text-white"
                    >
                        {{ $t("semester.semester") }}: {{ semester.name }}
                    </h2>
                    <p class="font-semibold text-gray-700 dark:text-gray-300">
                        {{ $t("semester.year") }}:
                        {{ semester.year?.name ?? "N/A" }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        {{ $t("semester.startDate") }}:
                        {{ new Date(semester.start_date).toLocaleDateString() }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        {{ $t("semester.endDate") }}:
                        {{ new Date(semester.end_date).toLocaleDateString() }}
                    </p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $t("semester.status") }}:
                        <span
                            class="px-2 py-1 text-sm rounded"
                            :class="
                                semester.status === 'Active'
                                    ? 'bg-green-400 text-green-800 dark:bg-green-600 dark:text-gray-100 font-bold'
                                    : 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                            "
                        >
                            {{ semester.status }}
                        </span>
                    </p>
                </div>
                <div class="flex justify-end gap-3 mt-4">
                    <Link
                        :href="
                            route('semesters.show', { semester: semester.id })
                        "
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <EyeIcon class="w-5 h-5" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-if="semesters.data.length === 0"
            class="py-6 text-center text-gray-500 dark:text-gray-400"
        >
            <p class="text-lg font-medium text-gray-700 dark:text-gray-300">
                {{ $t("semester.noSemesters") }}
            </p>
        </div>
    </AppLayout>
</template>
