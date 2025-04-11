<script setup>
import { defineProps } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  form: { type: Object, required: true },
});

// Initialize the form with existing data or default values
const form = useForm({
  name: props.form.name || "",
  status: props.form.status || "inactive",
  is_approved: props.form.is_approved || false,
  is_completed: props.form.is_completed || false,
});

// Function to handle form submission
const submit = () => {
  if (props.form.id) {
    // Update functionality
    form.put(route("years.update", { year: props.form.id }));
  } else {
    // Create functionality
    form.post(route("years.store"));
  }
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label
                    for="name"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >Year Name</label
                >
                <input
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
                <div v-if="form.errors.name" class="text-red-500 text-sm">
                    {{ form.errors.name }}
                </div>
            </div>

            <div>
                <label
                    for="status"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >Status</label
                >
                <select
                    id="status"
                    v-model="form.status"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                    <option value="inactive">Inactive</option>
                    <option value="active">Active</option>
                </select>
                <div v-if="form.errors.status" class="text-red-500 text-sm">
                    {{ form.errors.status }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="flex items-center gap-2">
                <input
                    id="is_approved"
                    type="checkbox"
                    v-model="form.is_approved"
                    class="rounded text-indigo-600 border-gray-300 shadow-sm focus:ring-indigo-500"
                />
                <label
                    for="is_approved"
                    class="text-sm text-gray-700 dark:text-gray-300"
                    >Is Approved</label
                >
                <div v-if="form.errors.is_approved" class="text-red-500 text-sm">
                    {{ form.errors.is_approved }}
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input
                    id="is_completed"
                    type="checkbox"
                    v-model="form.is_completed"
                    class="rounded text-indigo-600 border-gray-300 shadow-sm focus:ring-indigo-500"
                />
                <label
                    for="is_completed"
                    class="text-sm text-gray-700 dark:text-gray-300"
                    >Is Completed</label
                >
                <div v-if="form.errors.is_completed" class="text-red-500 text-sm">
                    {{ form.errors.is_completed }}
                </div>  
            </div>
        </div>

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
