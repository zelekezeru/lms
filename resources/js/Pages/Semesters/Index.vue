<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    EyeIcon,
    ArrowPathIcon,
    Squares2X2Icon,
    TableCellsIcon,
} from "@heroicons/vue/24/solid";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";

defineProps({
    semesters: Object,
    sortInfo: Object,
});

const search = ref(usePage().props.search || "");
const refreshing = ref(false);

const searchSemesters = () => {
    router.get(
        route("semesters.index"),
        { ...route().params, search: search.value },
        { preserveState: true }
    );
};

const refreshData = () => {
    refreshing.value = true;
    router.visit(route("semesters.index"), {
        data: { search: search.value },
        only: ["semesters"],
        preserveState: true,
        onFinish: () => (refreshing.value = false),
    });
};

const deleteSemester = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This will permanently delete the semester.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("semesters.destroy", { semester: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "Semester deleted.", "success");
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <!-- Header -->
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $t("semester.title") }}
            </h1>
        </div>

        <!-- Toolbar -->
        <div class="flex flex-col justify-between gap-4 mb-4 md:flex-row">
            <!-- Search -->
            <div class="relative w-full max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg
                        class="w-5 h-5 text-gray-500"
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
                    v-model="search"
                    @input="searchSemesters"
                    type="text"
                    class="w-full p-2 pl-10 text-gray-900 border rounded-lg dark:text-white dark:bg-gray-700"
                    placeholder="Search semester..."
                />
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <Link
                    v-if="userCan('create-semesters')"
                    :href="route('semesters.create')"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white uppercase bg-green-600 rounded-md hover:bg-green-700"
                >
                    + Add Semester
                </Link>
                <button
                    @click="refreshData"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white uppercase bg-blue-800 rounded-md hover:bg-blue-700"
                >
                    <ArrowPathIcon
                        :class="{ 'animate-spin': refreshing }"
                        class="w-5 h-5 mr-2"
                    />
                    Refresh
                </button>
                <button
                    @click="viewMode = viewMode === 'table' ? 'card' : 'table'"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white uppercase bg-gray-600 rounded-md hover:bg-gray-700"
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
        <div class="overflow-x-auto shadow sm:rounded-lg">
            <Table>
                <TableHeader>
                    <tr>
                        <Thead sortable :sort-info="sortInfo" sortColumn="name"
                            >Semester</Thead
                        >
                        <Thead>Year</Thead>
                        <Thead>REGULAR</Thead>
                        <Thead>DISTANCE</Thead>
                        <Thead>ONLINE</Thead>
                        <Thead>EXTENTION</Thead>
                        <Thead>Actions</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows
                        v-for="semester in semesters.data"
                        :key="semester.id"
                        :i="0"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                        >
                            <Link
                                :href="
                                    route('semesters.show', {
                                        semester: semester.id,
                                    })
                                "
                                class="hover:underline"
                            >
                                {{ semester.name }}
                            </Link>
                        </th>
                        <td class="px-6 py-4">
                            {{ semester.year?.name ?? "N/A" }}
                        </td>
                        <td
                            v-for="modeName in [
                                'REGULAR',
                                'DISTANCE',
                                'ONLINE',
                                'EXTENSION',
                            ]"
                            :key="modeName"
                            class="px-6 py-4"
                        >
                            <span
                                v-if="
                                    semester.studyModes &&
                                    semester.studyModes.some(
                                        (m) => m.name === modeName
                                    )
                                "
                                class="font-bold text-green-600"
                            >
                                Active
                            </span>
                            <span v-else class="font-bold text-red-400">
                                Inactive
                            </span>
                        </td>
                        <td class="flex space-x-2">
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
                    </TableZebraRows>
                </tbody>
            </Table>
        </div>
        <!-- Pagination -->
        <div class="mt-4">
            <pagination
                :links="semesters.links"
                :meta="semesters.meta"
                :current-page="semesters.current_page"
                :last-page="semesters.last_page"
                @page-changed="
                    (page) =>
                        router.get(route('semesters.index'), {
                            page: page,
                            search: search.value,
                        })
                "
            />
        </div>
    </AppLayout>
</template>
