<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { MultiSelect } from "primevue";
import { ref, watch } from "vue";

const props = defineProps({
    form: Object,
    programs: {
        type: Object,
        required: true,
    },
});

const selectedProgramsTracks = ref(
    props.form.programs.flatMap((programId) => {
        const program = props.programs.find((p) => p.id == programId);
        return program.tracks;
    })
);

// watch and updated the list of tracks
watch(
    () => props.form.programs,
    () => {
        selectedProgramsTracks.value = props.form.programs.flatMap(
            (programId) => {
                const program = props.programs.find((p) => p.id == programId);
                return program.tracks;
            }
        );
        props.form.tracks = form.programs.find;
    }
);
const emits = defineEmits(["submit"]);
</script>

<template>
    <form @submit.prevent="emits('submit')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Course Name -->
            <div>
                <InputLabel
                    for="name"
                    value="Course Name"
                    class="block mb-1 text-gray-200"
                />
                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autocomplete="name"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.name"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Credit Hours -->
            <div>
                <InputLabel
                    for="credit_hours"
                    value="Credit Hours"
                    class="block mb-1 text-gray-200"
                />
                <TextInput
                    id="credit_hours"
                    type="number"
                    v-model="form.credit_hours"
                    required
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.credit_hours"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Program field -->
            <div>
                <InputLabel
                    for="programs"
                    value="Select Programs (at least one)"
                    class="block mb-1 text-gray-200"
                />

                <MultiSelect
                    v-model="form.programs"
                    :options="programs"
                    optionLabel="name"
                    option-value="id"
                    filter
                    placeholder="Select Programs"
                    :maxSelectedLabels="3"
                    class="w-full md:w-80"
                />

                <InputError
                    :message="form.errors.programs"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Track field -->
            <div>
                <InputLabel
                    for="tracks"
                    value="Select Tracks In the Programs You Selected (at least one)"
                    class="block mb-1 text-gray-200"
                />

                <MultiSelect
                    v-model="form.tracks"
                    :options="selectedProgramsTracks"
                    optionLabel="name"
                    option-value="id"
                    empty-message="No tracks available (make sure you select program first)"
                    empty-filter-message="No tracks available"
                    filter
                    placeholder="Select Tracks (Select Program First)"
                    :maxSelectedLabels="3"
                    class="w-full md:w-80"
                />

                <InputError
                    :message="form.errors.tracks"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Duration -->
            <div>
                <InputLabel
                    for="duration"
                    value="Duration (in months)"
                    class="block mb-1 text-gray-200"
                />
                <TextInput
                    id="duration"
                    type="number"
                    v-model="form.duration"
                    required
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.duration"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Description -->
            <div>
                <InputLabel
                    for="description"
                    value="Description"
                    class="block mb-1 text-gray-200"
                />
                <TextInput
                    id="description"
                    type="text"
                    v-model="form.description"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.description"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Boolean Toggles -->
            <div class="flex flex-wrap gap-8 mt-2 md:col-span-2 mx-auto">
                <label
                    class="flex items-center gap-2 dark:text-gray-100 text-gray-700"
                >
                    <input
                        type="checkbox"
                        v-model="form.is_training"
                        class="form-checkbox"
                    />
                    Training
                </label>
                <label
                    class="flex items-center gap-2 dark:text-gray-100 text-gray-700"
                >
                    <input
                        type="checkbox"
                        v-model="form.status"
                        class="form-checkbox"
                    />
                    Active
                </label>
                <label
                    class="flex items-center gap-2 dark:text-gray-100 text-gray-700"
                >
                    <input
                        type="checkbox"
                        v-model="form.is_published"
                        class="form-checkbox"
                    />
                    Published
                </label>
                <label
                    class="flex items-center gap-2 dark:text-gray-100 text-gray-700"
                >
                    <input
                        type="checkbox"
                        v-model="form.is_approved"
                        class="form-checkbox"
                    />
                    Approved
                </label>
                <label
                    class="flex items-center gap-2 dark:text-gray-100 text-gray-700"
                >
                    <input
                        type="checkbox"
                        v-model="form.is_completed"
                        class="form-checkbox"
                    />
                    Completed
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-center">
            <PrimaryButton :disabled="form.processing">
                <span v-if="!form.processing">Submit</span>
                <span v-else>Submitting...</span>
            </PrimaryButton>
        </div>
    </form>
</template>
