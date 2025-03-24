<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
    form: Object,
    departments: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Program Dropdown -->
            <div>
                <InputLabel
                    for="department_id"
                    value="Select Department"
                    class="block mb-1 text-gray-200"
                />
                <select
                    id="department_id"
                    v-model="form.department_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                >
                    <option value="">Select Department</option>
                    <option
                        v-for="department in departments"
                        :key="department.id"
                        :value="department.id"
                    >
                        {{ department.name }} in {{ department.language }}
                    </option>
                </select>
                <InputError
                    :message="form.errors.department_id"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Mode Dropdown -->
            <div>
                <InputLabel
                    for="mode"
                    value="Select Department"
                    class="block mb-1 text-gray-200"
                />
                <select
                    id="mode"
                    v-model="form.mode"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                >
                    <option value="">Select Mode</option>
                    <option value="REGULAR">REGULAR</option>
                    <option value="EXTENSION">EXTENSION</option>
                    <option value="DISTANCE">DISTANCE</option>
                    <option value="ONLINE">ONLINE</option>
                </select>
                <InputError
                    :message="form.errors.mode"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Duration -->
            <div>
                <InputLabel
                    for="duration"
                    value="Duration"
                    class="block mb-1 text-gray-200"
                />
                <TextInput
                    id="duration"
                    type="number"
                    v-model="form.duration"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.duration"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Fees -->
            <div>
                <InputLabel
                    for="fees"
                    value="Fees"
                    class="block mb-1 text-gray-200"
                />
                <TextInput
                    id="fees"
                    type="number"
                    v-model="form.fees"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.fees"
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
