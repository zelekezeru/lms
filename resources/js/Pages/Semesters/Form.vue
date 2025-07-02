<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { DatePicker, Select } from "primevue";

const props = defineProps({
    form: Object,
    years: Array,
});

const emits = defineEmits(["submit"]);
</script>

<template>
    <form @submit.prevent="emits('submit')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel
                    for="name"
                    value="Name"
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
                    for="level"
                    value="Level"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <TextInput
                    id="level"
                    type="number"
                    v-model="form.level"
                    required
                    autofocus
                    autocomplete="level"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.name"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

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
            <div>
                <InputLabel for="start_date" value="Start Date" />
                <DatePicker
                    v-model="form.start_date"
                    dateFormat="yy-mm-dd"
                    showIcon
                    placeholder="Select Date of Birth"
                />
                <InputError :message="form.errors?.start_date" class="mt-2" />
            </div>

            <div>
                <InputLabel for="end_date" value="End Date" />
                <DatePicker
                    v-model="form.end_date"
                    dateFormat="yy-mm-dd"
                    showIcon
                    placeholder="Select Date of Birth"
                />
                <InputError :message="form.errors?.end_date" class="mt-2" />
            </div>
        </div>

        <div class="mt-6 flex justify-center">
            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <span v-if="!form.processing">{{
                    $t("common.save", "Submit")
                }}</span>
                <span v-else>{{ $t("common.loading", "Submitting...") }}</span>
            </button>
        </div>
    </form>
</template>
