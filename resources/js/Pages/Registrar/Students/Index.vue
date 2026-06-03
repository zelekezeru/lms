<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import {
    MagnifyingGlassIcon,
    EyeIcon,
    FunnelIcon,
    ArrowPathIcon,
    PrinterIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";
import { PencilSquareIcon } from "@heroicons/vue/24/solid";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";
import Modal from "@/Components/Modal.vue";
import Select from "primevue/select";

const props = defineProps({
    students: { type: Object, required: true },
    studentsCount: { type: Number, default: 0 },
    sortInfo: { type: Object, required: false },
    programs: { type: Array, default: () => [] },
    studyModes: { type: Array, default: () => [] },
    years: { type: Array, default: () => [] },
    search: { type: String, default: "" },
});

const routeName = "registrar.students";
const params = new URLSearchParams(window.location.search);

// ----- Search -----
const search = ref(props.search || "");
let searchTimer = null;
const runSearch = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(
            route(routeName),
            { ...route().params, search: search.value || undefined },
            { preserveState: true, preserveScroll: true, replace: true }
        );
    }, 350);
};

// ----- Refresh -----
const refreshing = ref(false);
const refreshData = () => {
    refreshing.value = true;
    router.reload({
        only: ["students", "studentsCount"],
        onFinish: () => (refreshing.value = false),
    });
};

// ----- Filters -----
const showFilterModal = ref(false);
const filterForm = useForm({
    payment: params.get("payment") || null,
    statuses: params.getAll("statuses") || [],
    year: params.has("year") ? Number(params.get("year")) : null,
    program: params.has("program") ? Number(params.get("program")) : null,
    track: params.has("track") ? Number(params.get("track")) : null,
    study_mode: params.has("study_mode") ? Number(params.get("study_mode")) : null,
    section: params.has("section") ? Number(params.get("section")) : null,
});

const selectedProgramTracks = computed(() => {
    const program = props.programs.find((p) => p.id == filterForm.program);
    return program ? program.tracks : [];
});

const selectedProgramStudyModes = computed(() => {
    const program = props.programs.find((p) => p.id == filterForm.program);
    return program ? program.studyModes : props.studyModes;
});

const selectedTrackSections = computed(() => {
    const track = selectedProgramTracks.value?.find((t) => t.id == filterForm.track);
    if (!track) return [];
    if (!filterForm.study_mode) return track.sections;
    return track.sections.filter((section) => {
        if (filterForm.year) {
            return section.studyMode.id == filterForm.study_mode && section.year.id == filterForm.year;
        }
        return section.studyMode.id == filterForm.study_mode;
    });
});

const applyFilters = () => {
    filterForm
        .transform((data) => ({ ...data, search: search.value || undefined }))
        .get(route(routeName), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => (showFilterModal.value = false),
        });
};

const clearFilters = () => {
    filterForm.payment = null;
    filterForm.statuses = [];
    filterForm.year = null;
    filterForm.program = null;
    filterForm.track = null;
    filterForm.study_mode = null;
    filterForm.section = null;
    router.get(route(routeName), { search: search.value || undefined }, { preserveScroll: true });
};

const hasActiveFilters = computed(
    () =>
        filterForm.payment ||
        filterForm.statuses.length ||
        filterForm.year ||
        filterForm.program ||
        filterForm.track ||
        filterForm.study_mode ||
        filterForm.section
);

// ----- Helpers -----
const fullName = (s) => [s.firstName, s.middleName, s.lastName].filter(Boolean).join(" ");

const statusBadge = (student) => {
    const st = student.status;
    if (!st) return { label: "Unknown", cls: "bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200" };
    if (st.is_graduated) return { label: "Graduated", cls: "bg-indigo-100 text-indigo-800 dark:bg-indigo-700 dark:text-white" };
    if (st.is_active) return { label: "Active", cls: "bg-green-100 text-green-800 dark:bg-green-700 dark:text-white" };
    return { label: "Inactive", cls: "bg-red-100 text-red-800 dark:bg-red-700 dark:text-white" };
};

const printList = () => window.print();
</script>

<template>
    <AppLayout>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 print:hidden">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Student Directory</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Total found: <strong>{{ studentsCount }}</strong>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button
                    @click="showFilterModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                >
                    <FunnelIcon class="w-5 h-5" />
                    Filters
                    <span v-if="hasActiveFilters" class="ml-1 w-2 h-2 rounded-full bg-blue-500"></span>
                </button>
                <button
                    @click="printList"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                >
                    <PrinterIcon class="w-5 h-5" />
                    Print
                </button>
                <button
                    @click="refreshData"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue-800 text-white hover:bg-blue-700 transition"
                >
                    <ArrowPathIcon class="w-5 h-5" :class="{ 'animate-spin': refreshing }" />
                    Refresh
                </button>
                <Link
                    v-if="userCan('create-students')"
                    :href="route('students.create')"
                    class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-sm font-semibold hover:bg-green-700 transition"
                >
                    + Add Student
                </Link>
            </div>
        </div>

        <!-- Search -->
        <div class="relative w-full sm:w-1/2 mb-4 print:hidden">
            <MagnifyingGlassIcon class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            <input
                v-model="search"
                @input="runSearch"
                type="text"
                placeholder="Search by name, ID number or phone..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>

        <!-- Active filter chips -->
        <div v-if="hasActiveFilters" class="flex flex-wrap items-center gap-2 mb-4 print:hidden">
            <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Active filters:</span>
            <button
                @click="clearFilters"
                class="inline-flex items-center gap-1 text-xs px-3 py-1 rounded-full bg-red-50 text-red-600 dark:bg-red-900/40 dark:text-red-300 hover:bg-red-100 transition"
            >
                <XMarkIcon class="w-3.5 h-3.5" /> Clear all
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <Table>
                <TableHeader>
                    <tr>
                        <Thead>No.</Thead>
                        <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'first_name'">Student Name</Thead>
                        <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'id_no'">ID Number</Thead>
                        <Thead>Program</Thead>
                        <Thead>Study Mode</Thead>
                        <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'mobile_phone'">Phone</Thead>
                        <Thead>Status</Thead>
                        <Thead class="print:hidden">Actions</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows v-for="(student, index) in students.data" :key="student.id">
                        <td class="px-6 py-4">
                            {{ index + 1 + (students.meta.current_page - 1) * students.meta.per_page }}
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <Link :href="route('students.show', { student: student.id })" class="hover:text-blue-600">
                                {{ fullName(student) }}
                            </Link>
                        </th>
                        <td class="px-6 py-4">{{ student.idNo }}</td>
                        <td class="px-6 py-4">{{ student.program?.name || "—" }}</td>
                        <td class="px-6 py-4">{{ student.studyMode?.name || "—" }}</td>
                        <td class="px-6 py-4">{{ student.mobilePhone || "—" }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="statusBadge(student).cls">
                                {{ statusBadge(student).label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 print:hidden">
                            <div class="flex space-x-4">
                                <Link
                                    v-if="userCan('view-students')"
                                    :href="route('students.show', { student: student.id })"
                                    class="text-blue-500 hover:text-blue-700"
                                    title="View"
                                >
                                    <EyeIcon class="w-5 h-5" />
                                </Link>
                                <Link
                                    v-if="userCan('update-students')"
                                    :href="route('students.edit', { student: student.id })"
                                    class="text-green-500 hover:text-green-700"
                                    title="Edit"
                                >
                                    <PencilSquareIcon class="w-5 h-5" />
                                </Link>
                            </div>
                        </td>
                    </TableZebraRows>
                    <tr v-if="!students.data.length">
                        <td colspan="8" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            No students match your search or filters.
                        </td>
                    </tr>
                </tbody>
            </Table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex flex-wrap justify-center gap-2 print:hidden">
            <Link
                v-for="link in students.meta.links"
                :key="link.label"
                :href="link.url || '#'"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors text-gray-700 dark:text-gray-300"
                :class="{
                    'cursor-not-allowed opacity-50': !link.url,
                    'bg-blue-600 text-white dark:bg-blue-600': link.active,
                    'hover:bg-gray-100 dark:hover:bg-gray-700': link.url && !link.active,
                }"
                v-html="link.label"
            />
        </div>

        <!-- Filter Modal -->
        <Modal :show="showFilterModal" @close="showFilterModal = false" max-width="4xl">
            <div class="p-6 space-y-6 bg-white dark:bg-gray-800 rounded-xl">
                <h2 class="text-xl font-bold text-blue-700 dark:text-blue-300 flex items-center gap-2">
                    <FunnelIcon class="w-6 h-6" /> Filter Students
                </h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">By Payment</label>
                    <Select
                        append-to="self"
                        v-model="filterForm.payment"
                        :options="[
                            { label: 'All', value: null },
                            { label: 'Pending (uncompleted)', value: 'pending' },
                            { label: 'Completed', value: 'paid' },
                            { label: 'Scholarship only', value: 'is_scholarship' },
                        ]"
                        option-label="label"
                        option-value="value"
                        placeholder="Select payment status"
                        class="w-full"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">By Status</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                            <input type="checkbox" value="active" v-model="filterForm.statuses" /> Active
                        </label>
                        <label class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                            <input type="checkbox" value="graduated" v-model="filterForm.statuses" /> Graduated
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Year</label>
                        <Select append-to="self" v-model="filterForm.year" :options="years" option-label="name" option-value="id" placeholder="Select year" class="w-full" show-clear />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Program</label>
                        <Select append-to="self" v-model="filterForm.program" :options="programs" option-label="name" option-value="id" placeholder="Select program" class="w-full" show-clear />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Track</label>
                        <Select append-to="self" v-model="filterForm.track" :options="selectedProgramTracks" option-label="name" option-value="id" placeholder="Select track" class="w-full" show-clear />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Study Mode</label>
                        <Select append-to="self" v-model="filterForm.study_mode" :options="selectedProgramStudyModes" option-label="name" option-value="id" placeholder="Select study mode" class="w-full" show-clear />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Section</label>
                        <Select append-to="self" v-model="filterForm.section" :options="selectedTrackSections" option-label="name" option-value="id" placeholder="Select section" class="w-full" show-clear />
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <button
                        @click="clearFilters"
                        class="px-5 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition font-semibold"
                    >
                        Clear
                    </button>
                    <div class="flex gap-3">
                        <button
                            @click="showFilterModal = false"
                            class="px-5 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold"
                        >
                            Cancel
                        </button>
                        <button
                            @click="applyFilters"
                            class="px-5 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-400 text-white font-semibold shadow hover:from-blue-700 hover:to-blue-500 transition"
                        >
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
