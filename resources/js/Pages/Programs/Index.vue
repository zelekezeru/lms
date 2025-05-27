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
import { AcademicCapIcon, CalendarIcon, PencilSquareIcon, LanguageIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "../../Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";

defineProps({
    programs: Object,
    user: Object,
    sortInfo: Object,
});

const refreshing = ref(false);
const viewMode = ref("card");
const search = ref(usePage().props.search || "");

const refreshData = () => {
    refreshing.value = true;
    router.visit(route("programs.index"), {
        only: ["programs"],
        onFinish: () => (refreshing.value = false),
    });
};

const deleteProgram = (id) => {
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
            router.delete(route("programs.destroy", { program: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The program has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};

const searchPrograms = () => {
    router.get(
        route("programs.index"),
        { ...route().params, search: search.value },
        { preserveState: true }
    );
};
</script>

<template>
    <AppLayout>
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $t('programs.title') }}
            </h1>
        </div>

        <!-- Header Toolbar -->
        <div class="flex items-center justify-between mb-3">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg
                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
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
                    :placeholder="$t('programs.search_placeholder')"
                    class="p-2 pl-10 text-gray-900 border rounded-lg dark:text-white dark:bg-gray-700"
                    @input="searchPrograms"
                />
            </div>

            <div class="flex space-x-4">
                <Link
                    v-if="userCan('create-programs')"
                    :href="route('programs.create')"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-green-600 rounded-md hover:bg-green-700"
                >
                    + {{ $t('programs.add_program') }}
                </Link>

                <button
                    @click="refreshData"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-blue-800 rounded-md hover:bg-blue-700"
                    title="Refresh Data"
                >
                    <ArrowPathIcon
                        class="w-5 h-5 mr-2"
                        :class="{ 'animate-spin': refreshing }"
                    />
                    {{ $t('programs.refresh') }}
                </button>
                <button
                    @click="viewMode = viewMode === 'table' ? 'card' : 'table'"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-gray-600 rounded-md hover:bg-gray-700"
                    title="Toggle View"
                >
                    <component
                        :is="
                            viewMode === 'table'
                                ? Squares2X2Icon
                                : TableCellsIcon
                        "
                        class="w-5 h-5 "
                    />
                </button>
            </div>
        </div>

        <!-- Table View -->
        <div v-if="viewMode === 'table'">
            <Table>
                <TableHeader>
                    <tr>
                        <Thead
                            :sortable="true"
                            :sort-info="sortInfo"
                            sortColumn="name"
                            >{{ $t('programs.name') }}</Thead
                        >
                        <Thead
                            :sortable="true"
                            :sort-info="sortInfo"
                            sortColumn="language"
                            >{{ $t('programs.language') }}</Thead
                        >
                        <Thead>{{ $t('programs.duration') }}</Thead>
                        <Thead>{{ $t('programs.action') }}</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows
                        v-for="program in programs.data"
                        :key="program.id"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                        >
                            <Link
                                :href="
                                    route('programs.show', {
                                        program: program.id,
                                    })
                                "
                                >{{ program.name }}</Link
                            >
                        </th>
                        <td class="px-6 py-4">{{ program.language }}</td>
                        <td class="px-6 py-4">{{ program.duration }}</td>
                        <td class="flex px-6 py-4 space-x-2">
                            <Link
                                v-if="userCan('view-programs')"
                                :href="
                                    route('programs.show', {
                                        program: program.id,
                                    })
                                "
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                            <Link
                                v-if="userCan('update-programs')"
                                :href="
                                    route('programs.edit', {
                                        program: program.id,
                                    })
                                "
                                class="text-green-500 hover:text-green-700"
                            >
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                        </td>
                    </TableZebraRows>
                </tbody>
            </Table>
        </div>

        <!-- Card View -->
        <div
            v-else
            class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
        >
            <div
                v-for="program in programs.data"
                :key="program.id"
                class="p-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-600"
            >
            
            <Link
                v-if="userCan('view-programs')"
                :href="route('programs.show', { program: program.id })"
                class="text-blue-500 hover:text-blue-700">

                    <div
                        class="flex items-center mb-3 font-bold text-gray-700 dark:text-gray-300">
                        <AcademicCapIcon class="w-5 h-5 mr-2 text-indigo-500" />
                            {{ program.name }}
                    </div>

                    <div
                        class="flex items-center mb-3 text-gray-700 dark:text-gray-300"
                    >
                        <LanguageIcon class="w-5 h-5 mr-2 text-teal-500" />
                        <span class="font-semibold">Language:</span>
                        <span class="ml-1">{{ program.language }}</span>
                    </div>
                    
                    <div
                        class="flex items-center mb-3 text-gray-700 dark:text-gray-300"
                    >
                        <CalendarIcon class="w-5 h-5 mr-2 text-orange-500" />
                        <span class="font-semibold">Duration :</span>
                        <span class="ml-1">{{ program.duration }}</span>
                    </div>

                    <div class="flex justify-end mt-4 space-x-3">
                        <Link
                            :href="
                                route('programs.show', { program: program.id })
                            "
                            class="text-blue-500 hover:text-blue-700"
                        >
                            <EyeIcon class="w-5 h-5" />
                        </Link>
                        <Link
                            v-if="userCan('update-programs')"
                            :href="
                                route('programs.edit', { program: program.id })
                            "
                            class="text-green-500 hover:text-green-700"
                        >
                            <PencilSquareIcon class="w-5 h-5" />
                        </Link>
                    </div>
                    
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6 space-x-2">
            <Link
                v-for="link in programs.meta.links"
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
