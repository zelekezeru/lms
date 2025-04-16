<script setup>
import { defineProps, defineEmits } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    form: Object,
    users: Object,
});
</script>

<template>
    <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel
                    for="name"
                    value="Program Name"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.name"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <div>
                <InputLabel
                    for="language"
                    value="Program Language"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <TextInput
                    id="language"
                    type="text"
                    v-model="form.language"
                    required
                    autocomplete="language"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.language"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <div>
                <InputLabel
                    for="description"
                    value="Description"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <TextInput
                    id="description"
                    type="text"
                    v-model="form.description"
                    autocomplete="description"
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

            <!-- Program Director Dropdown -->
            <div>
                <InputLabel
                    for="user_id"
                    value="Select Program Director"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <select
                    id="user_id"
                    v-model="form.user_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                >
                    <option value="">Select Program Director</option>
                    <option
                        v-for="user in users"
                        :key="user.id"
                        :value="user.id"
                    >
                        {{ user.name }}
                    </option>
                </select>
                <InputError
                    :message="form.errors.user_id"
                    class="mt-2 text-sm text-red-500"
                />
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
