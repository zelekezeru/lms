<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Select } from "primevue";

defineProps({
    form: Object,
    programs: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <InputLabel
                    for="name"
                    value="Select Mode You Want To Create"
                    class="block mb-1 text-gray-200"
                />
                <Select
                    id="modesList"
                    v-model="form.name"
                    :options="[
                        'REGULAR',
                        'EXTENSION', 
                        'DISTANCE', 
                        'ONLINE' 
                    ]"
                    checkmark
                    filter
                    placeholder="Select Study Mode Name"
                />
                <InputError
                    :message="form.errors.name"
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
