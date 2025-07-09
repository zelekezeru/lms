<script setup>
import { computed, defineProps, ref, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";
import { EyeIcon } from "@heroicons/vue/24/outline";
import { Select } from "primevue";
import TextInput from "@/Components/TextInput.vue";

// Define the props for the track
const props = defineProps({
    track: {
        type: Object,
        required: true,
    },
    years: {
        type: Object,
        required: true,
    },
    centers: {
        type: Array,
        required: true,
    },
});

const sectionForm = useForm({
    name: "",
    track_id: props.track.id,
    program_id: props.track.program.id,
    user_id: "",
    year_id: "",
    semester_id: "",
    center_id: "",
    study_mode_id: "",
    fees: "",
});

const selectedYearSemesters = ref();
const createSection = ref(false);
const showCenters = computed(() => sectionForm.study_mode_id == 4);

// watch and updated the list of semsters
watch(
    () => [sectionForm.year_id, sectionForm.study_mode_id],
    ([newYearId, newStudyModeId]) => {
        if (newYearId && newStudyModeId) {
            const selectedStudyMode = props.track.program.studyModes.find(
                (studyMode) => studyMode.id == newStudyModeId
            );

            selectedYearSemesters.value =
                selectedStudyMode?.semesters.filter(
                    (semester) => semester.year.id == sectionForm.year_id
                ) || [];
        }
    }
);

const addSection = () => {
    sectionForm.post(
        route("sections.store", {
            redirectTo: route("tracks.show", { track: props.track.id }),
            params: { track: props.track.id },
        }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Added!",
                    "Study Section added successfully.",
                    "success"
                );
                sectionForm.reset();
                createSection.value = false;
            },
        }
    );
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ $t("sections.title") }}
            </h2>
            <button
                @click="createSection = !createSection"
                class="flex items-center space-x-6 text-indigo-600 hover:text-indigo-800 transition"
            >
                <PlusCircleIcon class="w-8 h-8" />
                <span class="hidden sm:inline">{{ $t("sections.add") }}</span>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table
                class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
            >
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <th
                            class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                        >
                            {{ $t("sections.no") }}
                        </th>
                        <th
                            class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                        >
                            {{ $t("sections.name") }}
                        </th>
                        <th
                            class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                        >
                            {{ $t("sections.study_mode") }}
                        </th>
                        <th
                            class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            {{ $t("sections.year") }}
                        </th>
                        <th
                            class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            {{ $t("sections.semester") }}
                        </th>
                        <th
                            class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                        >
                            {{ $t("sections.actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(section, index) in track.sections"
                        :key="section.id"
                        :class="
                            index % 2 === 0
                                ? 'bg-white dark:bg-gray-800'
                                : 'bg-gray-50 dark:bg-gray-700'
                        "
                        class="border-b border-gray-300 dark:border-gray-600"
                    >
                        <td
                            class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                        >
                            {{ index + 1 }}
                        </td>
                        <td
                            class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                        >
                            <Link
                                :href="
                                    route('sections.show', {
                                        section: section.id,
                                    })
                                "
                            >
                                {{ section.name }}
                            </Link>
                        </td>
                        <td
                            class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                        >
                            {{
                                section.studyMode
                                    ? section.studyMode.name
                                    : $t("sections.na")
                            }}
                        </td>
                        <td
                            class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                        >
                            {{ section.year.name }}
                        </td>
                        <td
                            class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                        >
                            {{ section.semester.name }}
                        </td>
                        <td
                            class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                        >
                            <div
                                class="flex items-center justify-center h-full"
                            >
                                <Link
                                    :href="
                                        route('sections.show', {
                                            section: section.id,
                                        })
                                    "
                                    class="text-blue-500 hover:text-blue-700"
                                >
                                    <EyeIcon class="w-5 h-5" />
                                </Link>
                            </div>
                        </td>
                    </tr>
                    <transition
                        enter-active-class="transition duration-300 ease-out"
                        enter-from-class="opacity-0 -translate-y-2"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 -translate-y-2"
                    >
                        <tr
                            v-if="createSection"
                            class="bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600"
                        >
                            <td class="px-4 py-2">+</td>
                            <td class="px-4 py-2">
                                <TextInput
                                    v-model="sectionForm.name"
                                    type="text"
                                    :placeholder="$t('sections.name')"
                                    class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                />
                            </td>
                            <td class="px-4 py-2">
                                <Select
                                    id="studyModesList"
                                    v-model="sectionForm.study_mode_id"
                                    :options="track.program.studyModes"
                                    option-value="id"
                                    option-label="name"
                                    checkmark
                                    filter
                                    :placeholder="
                                        $t('sections.select_study_mode')
                                    "
                                    :maxSelectevdLabels="3"
                                    class="w-40"
                                />
                                <Select
                                    v-if="showCenters"
                                    id="centers"
                                    v-model="sectionForm.center_id"
                                    :options="centers"
                                    option-value="id"
                                    option-label="name"
                                    checkmark
                                    filter
                                    :placeholder="
                                        $t('sections.select_study_mode')
                                    "
                                    :maxSelectevdLabels="3"
                                    class="w-40 mt-4"
                                />
                            </td>
                            <td class="px-4 py-2">
                                <Select
                                    id="yearsList"
                                    v-model="sectionForm.year_id"
                                    :options="years"
                                    option-value="id"
                                    option-label="name"
                                    checkmark
                                    filter
                                    :placeholder="$t('sections.select_year')"
                                    :maxSelectevdLabels="3"
                                    class="w-40"
                                />
                            </td>
                            <td
                                class="px-4 py-2 flex justify-between item-center"
                            >
                                <Select
                                    id="semstersList"
                                    v-model="sectionForm.semester_id"
                                    :options="selectedYearSemesters"
                                    option-value="id"
                                    option-label="name"
                                    checkmark
                                    filter
                                    :placeholder="
                                        $t('sections.select_semester')
                                    "
                                    :maxSelectevdLabels="3"
                                    class="w-40"
                                />
                            </td>
                            <td>
                                <button
                                    class="px-4 py-1 h-9 bg-green-500 text-white rounded-md hover:bg-green-600"
                                    @click="addSection"
                                >
                                    {{ $t("common.save") }}
                                </button>
                            </td>
                        </tr>
                    </transition>
                </tbody>
            </table>
        </div>
    </div>
</template>
