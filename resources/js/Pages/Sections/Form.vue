<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { ref, watch } from "vue";
import { Select } from "primevue";

const props = defineProps({
    form: { type: Object, required: true },
    users: Array,
    programs: Array,
    years: Array,
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

const emit = defineEmits(["submit"]);
</script>

<template>
    <form @submit.prevent="emit('submit')">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Section Name -->
            <div>
                <InputLabel for="name" value="Section Name" />
                <input
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
                <InputError :message="form.errors.name" />
            </div>

            <!-- Section Representative -->
            <div>
                <InputLabel for="user_id" value="Section Representative" />
                <select
                    v-model="form.user_id"
                    id="user_id"
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                    <option value="">Select User</option>
                    <option
                        v-for="user in users"
                        :key="user.id"
                        :value="user.id"
                    >
                        {{ user.name }}
                    </option>
                </select>
                <InputError :message="form.errors.user_id" />
            </div>

            <!-- Academic Year -->
            <div>
                <InputLabel for="year_id" value="Select Registration Year" />
                <Select
                    id="year_id"
                    v-model="form.year_id"
                    :options="years"
                    placeholder="Select Year"
                    option-value="id"
                    option-label="name"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError :message="form.errors?.year_id" class="mt-2" />
            </div>

            <!-- Semester -->
            <div>
                <InputLabel for="semester_id" value="Select Semester" />
                <Select
                    id="semester_id"
                    v-model="form.semester_id"
                    :options="selectedYearSemesters"
                    placeholder="Select Semester"
                    empty-message="First Select Year!"
                    option-value="id"
                    option-label="name"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError :message="form.errors?.semester_id" class="mt-2" />
            </div>

            <!-- Program -->
            <div>
                <InputLabel for="program_id" value="Select Program" />
                <Select
                    id="program_id"
                    v-model="form.program_id"
                    :options="programs"
                    option-value="id"
                    option-label="name"
                    filter
                    placeholder="Select Program"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError :message="form.errors?.program_id" class="mt-2" />
            </div>

            <!-- Track -->
            <div>
                <InputLabel for="track_id" value="Select Track" />
                <Select
                    id="track_id"
                    v-model="form.track_id"
                    :options="selectedProgramTracks"
                    option-value="id"
                    option-label="name"
                    filter
                    empty-message="First Select Program!"
                    placeholder="Select Track"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError :message="form.errors?.track_id" class="mt-2" />
            </div>

            <!-- Study Mode -->
            <div>
                <InputLabel for="study_mode_id" value="Select Study Mode" />
                <Select
                    id="study_mode_id"
                    v-model="form.study_mode_id"
                    :options="selectedProgramStudyModes"
                    option-value="id"
                    option-label="name"
                    filter
                    empty-message="First Select Program!"
                    placeholder="Select Study Mode"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors?.study_mode_id"
                    class="mt-2"
                />
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-center">
            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <span v-if="!form.processing">Submit</span>
                <span v-else>Submitting...</span>
            </button>
        </div>
    </form>
</template>
