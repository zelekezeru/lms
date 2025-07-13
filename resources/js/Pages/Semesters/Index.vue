<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    EyeIcon,
    TrashIcon,
    PencilSquareIcon,
    ArrowPathIcon,
    Squares2X2Icon,
    TableCellsIcon,
    CalendarIcon,
} from "@heroicons/vue/24/solid";
import { AcademicCapIcon } from "@heroicons/vue/24/outline";
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
const viewMode = ref("card");

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
        <div
            v-if="viewMode === 'table'"
            class="overflow-x-auto shadow sm:rounded-lg"
        >
            <Table>
                <TableHeader>
                    <tr>
                        <Thead sortable :sort-info="sortInfo" sortColumn="name"
                            >Semester</Thead
                        >
                        <Thead>Year</Thead>
                        <Thead
                            sortable
                            :sort-info="sortInfo"
                            sortColumn="start_date"
                            >Start Date</Thead
                        >
                        <Thead
                            sortable
                            :sort-info="sortInfo"
                            sortColumn="end_date"
                            >End Date</Thead
                        >
                        <Thead>Actions</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows
                        v-for="semester in semesters.data"
                        :key="semester.id"
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
                            <button
                                v-if="userCan('delete-semesters')"
                                @click="deleteSemester(semester.id)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </td>
                    </TableZebraRows>
                </tbody>
            </Table>
        </div>

        <!-- Card View -->
        <div
            v-else
            class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 md:grid-cols-3"
        >
            <div
                v-for="semester in semesters.data"
                :key="semester.id"
                class="p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-600"
            >
                <Link
                    :href="route('semesters.show', { semester: semester.id })"
                    class="block"
                >
                    <div
                        class="flex items-center mb-2 font-bold text-gray-900 dark:text-white"
                    >
                        <AcademicCapIcon class="w-5 h-5 mr-2 text-indigo-500" />
                        {{ semester.name }}
                    </div>
                    <div
                        class="flex items-center text-gray-700 dark:text-gray-300"
                    >
                        <CalendarIcon class="w-5 h-5 mr-2 text-orange-500" />
                        <span class="font-semibold mr-1">Start:</span>
                        {{ new Date(semester.start_date).toLocaleDateString() }}
                    </div>
                    <div
                        class="flex items-center text-gray-700 dark:text-gray-300"
                    >
                        <CalendarIcon class="w-5 h-5 mr-2 text-rose-500" />
                        <span class="font-semibold mr-1">End:</span>
                        {{ new Date(semester.end_date).toLocaleDateString() }}
                    </div>
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6 space-x-2">
            <Link
                v-for="link in semesters.meta.links"
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
