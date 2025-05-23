<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
    form: Object,
    submit: Function,
    users: Array,
    programs: { type: Object, required: false },
    showPrograms: { type: Boolean, required: false, default: true },
});

const emits = defineEmits(["submit"]);
</script>

<template>
    <form @submit.prevent="emits('submit')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Program Dropdown -->
            <div v-if="showPrograms">
                <InputLabel
                    for="program_id"
                    value="Select Program"
                    class="block mb-1 text-gray-200"
                />
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
                        {{ program.name }} in {{ program.language }}
                    </option>
                </select>
                <InputError
                    :message="form.errors.program_id"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Track Name -->
            <div>
                <InputLabel
                    for="name"
                    value="Track Name"
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

            <!-- Duration -->
            <div>
                <InputLabel
                    for="duration"
                    value="Duration (Years)"
                    class="block mb-1 text-gray-200"
                />
                <TextInput
                    id="duration"
                    type="number"
                    v-model="form.duration"
                    required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.duration"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-center">
            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <span v-if="!form.processing">Submit</span>
                <span v-else>Submitting...</span>
            </button>
        </div>
    </form>
</template>
