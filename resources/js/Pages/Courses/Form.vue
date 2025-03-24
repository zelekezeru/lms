<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
    form: Object,
    submit: Function,
    departments: Object,
});
</script>

<template>
    <form @submit.prevent="submit">
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

            <!-- Duration -->
            <div>
                <InputLabel
                    for="duration"
                    value="Duration (in weeks/months)"
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

            <!-- Department -->
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
                        v-for="dept in departments"
                        :key="dept.id"
                        :value="dept.id"
                    >
                        {{ dept.name }}
                    </option>
                </select>
                <InputError
                    :message="form.errors.department_id"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
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
            <div class="flex flex-wrap gap-4 mt-2 md:col-span-2">
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
