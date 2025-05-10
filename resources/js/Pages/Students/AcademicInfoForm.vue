<script setup>
import { defineProps, defineEmits, ref, watch } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ArrowRightIcon, ArrowLeftIcon } from "@heroicons/vue/24/solid";
import { Select } from "primevue";

// Props
const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    programs: {
        type: Array,
        required: true,
    },
    years: {
        type: Array,
        required: true,
    },
});

const selectedYearSemesters = ref(
    props.years.find((year) => year.id == props.form.year_id)?.semesters ?? []
);

// watch and updated the list of semesters
watch(
    () => props.form.year_id,
    () => {
        props.form.semester_id = "";
        selectedYearSemesters.value = props.years.find(
            (year) => year.id == props.form.year_id
        )?.semesters;
    }
);

const selectedProgramTracks = ref(
    props.programs.find((program) => program.id == props.form.program_id)
        ?.tracks ?? []
);

const selectedProgramStudyModes = ref(
    props.programs.find((program) => program.id == props.form.program_id)
        ?.studyModes ?? []
);

// watch for changes in value of props.form.program_id and updated the list of tracks and study modes
watch(
    () => props.form.program_id,
    () => {
        props.form.track_id = "";
        props.form.study_mode_id = "";

        selectedProgramTracks.value = props.programs.find(
            (program) => program.id == props.form.program_id
        )?.tracks;

        selectedProgramStudyModes.value = props.programs.find(
            (program) => program.id == props.form.program_id
        )?.studyModes;
    }
);


// Emits
const emit = defineEmits(["next", "previous"]);
</script>

<template>
    <div>
        <h2 class="text-lg font-bold mb-4">Academic Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Academic Year Dropdown -->
            <div>
                <InputLabel for="year_id" value="Select Registration year" />
                <Select
                    id="year_id"
                    v-model="form.year_id"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                    :options="years"
                    placeholder="Select Year"
                    option-value="id"
                    option-label="name"
                >
                </Select>
                <InputError :message="form.errors?.year_id" class="mt-2" />
            </div>

            <!-- Semester Dropdown -->
            <div>
                <InputLabel for="semester_id" value="Select semester" />
                <Select
                    id="semester_id"
                    v-model="form.semester_id"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                    :options="selectedYearSemesters"
                    placeholder="Select Semester"
                    empty-message="First Select Year!"
                    option-value="id"
                    option-label="name"
                >
                </Select>
                <InputError :message="form.errors?.semester_id" class="mt-2" />
            </div>

            <!-- Programs Dropdown-->
            <div>
                <InputLabel
                    for="program_id"
                    value="Select Program"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />

                <Select
                    id="cousesList"
                    v-model="form.program_id"
                    :options="programs"
                    option-value="id"
                    option-label="name"
                    checkmark
                    filter
                    placeholder="Select Program"
                    :maxSelectevdLabels="3"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors?.program_id"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Tracks Dropdown-->
            <div>
                <InputLabel
                    for="track_id"
                    value="Select Track"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />

                <Select
                    id="cousesList"
                    v-model="form.track_id"
                    :options="selectedProgramTracks"
                    option-value="id"
                    option-label="name"
                    checkmark
                    filter
                    empty-message="First Select Program!"
                    placeholder="Select Track"
                    :maxSelectevdLabels="3"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors?.track_id"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Tracks Dropdown-->
            <div>
                <InputLabel
                    for="track_id"
                    value="Select Study Mode"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                
                <Select
                    id="cousesList"
                    v-model="form.study_mode_id"
                    :options="selectedProgramStudyModes"
                    option-value="id"
                    option-label="mode"
                    checkmark
                    filter
                    empty-message="First Select Program!"
                    placeholder="Select Study Mode"
                    :maxSelectevdLabels="3"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors?.study_mode_id"
                    class="mt-2 text-sm text-red-500"
                />
            </div>
        </div>
    </div>

    <div class="flex justify-center mt-4">
        <div>
            <!-- Add other basic fields here -->
            <button
                @click="$emit('previous')"
                class="inline-flex items-center rounded-md bg-blue-600 mx-2 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-blue-700"
            >
                <ArrowLeftIcon class="w-5 h-5 mr-2" /> Previous
            </button>

            <button
                @click="$emit('next')"
                class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-green-700"
            >
                <ArrowRightIcon class="w-5 h-5 mr-2" /> Next
            </button>
        </div>
    </div>
</template>
