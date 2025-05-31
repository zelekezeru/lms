<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { MultiSelect } from "primevue";
import { ref, watch } from "vue";

const props = defineProps({
    form: Object,
});
const emits = defineEmits(["submit"]);
</script>

<template>
    <form @submit.prevent="emits('submit')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Room Name -->
            <div>
                <InputLabel
                    for="name"
                    value="Room Name"
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

            <div>
                <InputLabel
                    for="capacity"
                    value="Room Capacity"
                    class="block mb-1 text-gray-200"
                />
                <TextInput
                    id="capacity"
                    type="number"
                    v-model="form.capacity"
                    required
                    autocomplete="capacity"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.capacity"
                    class="mt-2 text-sm text-red-500"
                />
            </div>
            <div>
                <InputLabel
                    for="location"
                    value="Room Location"
                    class="block mb-1 text-gray-200"
                />
                <TextInput
                    id="location"
                    type="text"
                    v-model="form.location"
                    required
                    autocomplete="location"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.location"
                    class="mt-2 text-sm text-red-500"
                />
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
