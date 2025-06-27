<script setup>
import { defineProps, ref, computed, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { EyeIcon, FunnelIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";
import Modal from "@/Components/Modal.vue";
import Select from "primevue/select";

const props = defineProps({
    students: Object,
    sortInfo: Object,
    deleteStudent: Function,
    search: String,
    programs: Array,
    years: Array,
});

const showFilterModal = ref(false);

const params = new URLSearchParams(window.location.search);

const filterForm = useForm({
    payment: params.get("payment") || null,
    statuses: params.getAll("statuses") || [],
    year: params.has("year") ? Number(params.get("year")) : null,
    program: params.has("program") ? Number(params.get("program")) : null,
    track: params.has("track") ? Number(params.get("track")) : null,
    study_mode: params.has("study_mode")
        ? Number(params.get("study_mode"))
        : null,
    section: params.has("section") ? Number(params.get("section")) : null,
});

const selectedProgramTracks = computed(() => {
    const selectedProgram = props.programs.find(
        (program) => program.id == filterForm.program
    );
    return selectedProgram ? selectedProgram.tracks : [];
});

const selectedProgramStudyModes = computed(() => {
    const selectedProgram = props.programs.find(
        (program) => program.id == filterForm.program
    );
    return selectedProgram ? selectedProgram.studyModes : [];
});

const selectedTrackSections = computed(() => {
    const trackId = filterForm.track;
    const studyModeId = filterForm.study_mode;

    // Find the selected track by ID
    const selectedTrack = selectedProgramTracks.value?.find(
        (track) => track.id == trackId
    );

    if (!selectedTrack) return [];

    // If no study mode selected, return all sections for the track
    if (!studyModeId) return selectedTrack.sections;

    // if  filter sections by selected study mode
    return selectedTrack.sections.filter((section) => {
        if (filterForm.year) {
            return (
                section.studyMode.id == studyModeId &&
                section.year.id == filterForm.year
            );
        }
        return section.studyMode.id == studyModeId;
    });
});

const applyFilters = () => {
    filterForm.get(route("students.index"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (showFilterModal.value = false),
    });
};

watch(
    () => filterForm.program,
    (newVal) => {
        if (!newVal) {
            filterForm.study_mode = null;
            filterForm.track = null;
        }
    }
);

watch(
    () => filterForm.track,
    (newVal) => {
        if (!newVal) {
            filterForm.section = null;
        }
    }
);
const hasActiveFilters = computed(() => {
    return (
        filterForm.payment ||
        filterForm.year ||
        filterForm.program ||
        filterForm.track ||
        filterForm.study_mode ||
        filterForm.section
    );
});

// Helper functions to get names
const getProgramName = (id) => {
    return props.programs.find((p) => p.id == id)?.name || "Unknown";
};

const getYearName = (id) => {
    return props.years.find((y) => y.id == id)?.name || "Unknown";
};

const getTrackName = (id) => {
    const program = props.programs.find((p) => p.id == filterForm.program);
    return program?.tracks.find((t) => t.id == id)?.name || "Unknown";
};

const getStudyModeName = (id) => {
    const program = props.programs.find((p) => p.id == filterForm.program);
    return program?.studyModes.find((s) => s.id == id)?.name || "Unknown";
};

const getSectionName = (id) => {
    const track = selectedProgramTracks.value.find(
        (t) => t.id == filterForm.track
    );
    const sections = track?.sections || [];
    return sections.find((s) => s.id == id)?.name || "Unknown";
};
</script>

<template>
    <div class="overflow-x-auto shadow-md sm:rounded-lg mt-3">
        <!-- Export list to Excel -->
        <div class="flex justify-between items-center my-4">
            <button
                @click="showFilterModal = true"
                class="flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition"
            >
                <FunnelIcon class="w-5 h-5 mr-2" />
                Filters
            </button>
        </div>

        <!-- Active Filters Summary -->
        <div
            v-if="hasActiveFilters"
            class="mb-4 bg-gray-100 dark:bg-gray-800 p-4 rounded shadow-sm"
        >
            <h2 class="font-semibold text-gray-700 dark:text-gray-200 mb-2">
                Active Filters:
            </h2>
            <div class="flex flex-wrap gap-3 text-sm">
                <span
                    v-if="filterForm.payment"
                    class="flex items-center gap-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white px-3 py-1 rounded-full shadow"
                >
                    Payment: <strong>{{ filterForm.payment }}</strong>
                    <XMarkIcon
                        @click="filterForm.payment = null"
                        class="w-4 h-4 cursor-pointer text-gray-500 hover:text-red-500 transition"
                    />
                </span>

                <span
                    v-if="filterForm.year"
                    class="flex items-center gap-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white px-3 py-1 rounded-full shadow"
                >
                    Year: <strong>{{ getYearName(filterForm.year) }}</strong>
                    <XMarkIcon
                        @click="
                            () => {
                                filterForm.year = null;
                                filterForm.get(route('students.index'), {
                                    preserveScroll: true,
                                    preserveState: true,
                                });
                            }
                        "
                        class="w-4 h-4 cursor-pointer text-gray-500 hover:text-red-500 transition"
                    />
                </span>

                <span
                    v-if="filterForm.program"
                    class="flex items-center gap-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white px-3 py-1 rounded-full shadow"
                >
                    Program:
                    <strong>{{ getProgramName(filterForm.program) }}</strong>
                    <XMarkIcon
                        @click="
                            () => {
                                filterForm.program = null;
                                filterForm.get(route('students.index'), {
                                    preserveScroll: true,
                                    preserveState: true,
                                });
                            }
                        "
                        class="w-4 h-4 cursor-pointer text-gray-500 hover:text-red-500 transition"
                    />
                </span>

                <span
                    v-if="filterForm.track"
                    class="flex items-center gap-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white px-3 py-1 rounded-full shadow"
                >
                    Track: <strong>{{ getTrackName(filterForm.track) }}</strong>
                    <XMarkIcon
                        @click="
                            () => {
                                filterForm.track = null;
                                filterForm.get(route('students.index'), {
                                    preserveScroll: true,
                                    preserveState: true,
                                });
                            }
                        "
                        class="w-4 h-4 cursor-pointer text-gray-500 hover:text-red-500 transition"
                    />
                </span>

                <span
                    v-if="filterForm.study_mode"
                    class="flex items-center gap-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white px-3 py-1 rounded-full shadow"
                >
                    Study Mode:
                    <strong>{{
                        getStudyModeName(filterForm.study_mode)
                    }}</strong>
                    <XMarkIcon
                        @click="
                            () => {
                                filterForm.study_mode = null;
                                filterForm.get(route('students.index'), {
                                    preserveScroll: true,
                                    preserveState: true,
                                });
                            }
                        "
                        class="w-4 h-4 cursor-pointer text-gray-500 hover:text-red-500 transition"
                    />
                </span>

                <span
                    v-if="filterForm.section"
                    class="flex items-center gap-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-white px-3 py-1 rounded-full shadow"
                >
                    Section:
                    <strong>{{ getSectionName(filterForm.section) }}</strong>
                    <XMarkIcon
                        @click="
                            () => {
                                filterForm.section = null;
                                filterForm.get(route('students.index'), {
                                    preserveScroll: true,
                                    preserveState: true,
                                });
                            }
                        "
                        class="w-4 h-4 cursor-pointer text-gray-500 hover:text-red-500 transition"
                    />
                </span>
            </div>
        </div>

        <Table>
            <TableHeader>
                <tr>
                    <Thead>No.</Thead>
                    <Thead
                        :sortable="true"
                        :sort-info="sortInfo"
                        :sortColumn="'first_name'"
                        >Student Name</Thead
                    >
                    <Thead
                        :sortable="true"
                        :sort-info="sortInfo"
                        :sortColumn="'id_no'"
                        >ID Number</Thead
                    >
                    <Thead
                        :sortable="true"
                        :sort-info="sortInfo"
                        :sortColumn="'mobile_phone'"
                        >Phone</Thead
                    >
                    <Thead>Actions</Thead>
                </tr>
            </TableHeader>
            <tbody>
                <TableZebraRows
                    v-for="(student, index) in students.data"
                    :key="student.id"
                >
                    <td class="px-6 py-4">
                        {{
                            index +
                            1 +
                            (students.meta.current_page - 1) *
                                students.meta.per_page
                        }}
                    </td>
                    <th
                        scope="row"
                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                    >
                        <Link
                            :href="
                                route('students.show', { student: student.id })
                            "
                        >
                            {{ student.firstName }} {{ student.middleName }}
                            {{ student.lastName }}
                        </Link>
                    </th>
                    <td class="px-6 py-4">{{ student.idNo }}</td>
                    <td class="px-6 py-4">{{ student.mobilePhone }}</td>
                    <td class="px-6 py-4 flex space-x-6">
                        <div v-if="userCan('view-students')">
                            <Link
                                :href="
                                    route('students.show', {
                                        student: student.id,
                                    })
                                "
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                        </div>
                        <div v-if="userCan('update-students')">
                            <Link
                                :href="
                                    route('students.edit', {
                                        student: student.id,
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

    <!-- Pagination Links -->
    <div class="mt-3 flex justify-center space-x-6">
        <Link
            v-for="link in students.meta.links"
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

    <!-- Filter Modal -->
    <Modal :show="showFilterModal" @close="showFilterModal = false">
        <div
            class="p-6 space-y-6 bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-xl shadow-xl"
        >
            <h2
                class="text-2xl font-bold text-blue-700 dark:text-blue-300 flex items-center gap-2"
            >
                <FunnelIcon class="w-6 h-6 text-blue-500 dark:text-blue-300" />
                Filter Students
            </h2>

            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
                    >By Payment</label
                >
                <Select
                    append-to="self"
                    v-model="filterForm.payment"
                    :options="[
                        { label: 'All', value: null },
                        { label: 'Uncompleted (Pending)', value: 'pending' },
                        {
                            label: 'All Completed(both Scholarship and self-sponsor)',
                            value: 'paid',
                        },
                        {
                            label: 'Only Scholarship Students',
                            value: 'is_scholarship',
                        },
                    ]"
                    option-label="label"
                    option-value="value"
                    placeholder="Select payment type"
                    class="w-full"
                />
            </div>

            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
                    >By Status</label
                >
                <div class="flex flex-wrap gap-4">
                    <label
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-300"
                    >
                        <input
                            type="checkbox"
                            value="active"
                            v-model="filterForm.statuses"
                        />
                        Active
                    </label>
                    <label
                        class="flex items-center gap-2 text-gray-600 dark:text-gray-300"
                    >
                        <input
                            type="checkbox"
                            value="graduated"
                            v-model="filterForm.statuses"
                        />
                        Graduated
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
                        >Year</label
                    >

                    <Select
                        append-to="self"
                        v-model="filterForm.year"
                        :options="[{ name: 'All', value: null }, ...years]"
                        option-label="name"
                        option-value="id"
                        placeholder="Select year"
                        class="w-full"
                    />
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
                        >Program</label
                    >
                    <Select
                        append-to="self"
                        v-model="filterForm.program"
                        :options="[{ name: 'All', value: null }, ...programs]"
                        option-label="name"
                        option-value="id"
                        placeholder="Select program"
                        class="w-full"
                    />
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
                        >Track</label
                    >
                    <Select
                        append-to="self"
                        v-model="filterForm.track"
                        :options="[
                            { name: 'All', value: null },
                            ...selectedProgramTracks,
                        ]"
                        option-label="name"
                        option-value="id"
                        placeholder="Select track"
                        class="w-full"
                    />
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
                        >StudyMode</label
                    >
                    <Select
                        append-to="self"
                        v-model="filterForm.study_mode"
                        :options="[
                            { name: 'All', value: null },
                            ...selectedProgramStudyModes,
                        ]"
                        option-label="name"
                        option-value="id"
                        placeholder="Select Study Mode"
                        class="w-full"
                    />
                </div>
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
                        >Section</label
                    >
                    <Select
                        append-to="self"
                        v-model="filterForm.section"
                        :options="[
                            { name: 'All', value: null },
                            ...selectedTrackSections,
                        ]"
                        option-label="name"
                        option-value="id"
                        placeholder="Select section"
                        class="w-full"
                    />
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
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
    </Modal>
</template>
