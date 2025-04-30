<script setup>
import { defineProps, defineEmits } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ArrowRightIcon, ArrowLeftIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    tracks: {
        type: Array,
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
    semesters: {
        type: Array,
        required: true,
    },
});


// const selectedProgramsTracks = ref(
//     props.form.programs.flatMap((programId) => {
//         const program = props.programs.find((p) => p.id == programId);
//         return program.tracks;
//     })
// );

// // watch and updated the list of tracks
// watch(
//     () => props.form.programs,
//     () => {
//         selectedProgramsTracks.value = props.form.programs.flatMap(
//             (programId) => {
//                 const program = props.programs.find((p) => p.id == programId);
//                 return program.tracks;
//             }
//         );
//         props.form.tracks = form.programs.find;
//     }
// );

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
                <select
                    id="year_id"
                    v-model="form.year_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                >
                    <option value="">Select Academic year</option>
                    <option
                        v-for="year in years"
                        :key="year.id"
                        :value="year.id"
                    >
                        {{ year.name }}
                    </option>
                </select>
                <InputError :message="form.errors?.year_id" class="mt-2" />
            </div>

            <!-- Semester Dropdown -->
            <div>
                <InputLabel for="semester_id" value="Select semester" />
                <select
                    id="semester_id"
                    v-model="form.semester_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                >
                    <option value="">Select semester</option>
                    <option
                        v-for="semester in semesters"
                        :key="semester.id"
                        :value="semester.id"
                    >
                        {{ semester.name }}
                    </option>
                </select>
                <InputError :message="form.errors?.semester_id" class="mt-2" />
            </div>
        </div>
    </div>

    <!-- Program Input -->

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-8">
        <div>
            <InputLabel for="program_id" value="Select Program" />
            <select
                id="program_id"
                v-model="form.program_id"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
            >
                <option value="">Select Program</option>
                <option
                    v-for="program in programs"
                    :key="program.id"
                    :value="program.id"
                >
                    {{ program.name }}
                </option>
            </select>
            <InputError :message="form.errors?.program_id" class="mt-2" />
        </div>

        <div>
            <InputLabel for="track_id" value="Select Track" />
            <select
                id="track_id"
                v-model="form.track_id"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
            >
                <option value="" disabled>Select Track</option>
                <option
                    v-for="track in tracks"
                    :key="track.id"
                    :value="track.id"
                >
                    {{ track.name }}
                </option>
            </select>
            <InputError :message="form.errors?.track_id" class="mt-2" />
        </div>

        <div>
            <InputLabel for="track_id" value="Select Study Mode" />
            <select
                id="track_id"
                v-model="form.track_id"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
            >
                <option value="" disabled>Select Study Mode</option>
                <option
                    v-for="track in tracks"
                    :key="track.id"
                    :value="track.id"
                >
                    {{ track.name }}
                </option>
            </select>
            <InputError :message="form.errors?.track_id" class="mt-2" />
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
