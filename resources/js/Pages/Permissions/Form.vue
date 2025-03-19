<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { defineProps, defineEmits } from "vue";

const props = defineProps({ form: Object });
const emit = defineEmits(["submit"]);

// Ensure guard_name is included in form data
props.form.guard_name = props.form.guard_name || 'web';
</script>

<template>
    <form @submit.prevent="emit('submit')" class="">
        <div class="mb-4">
            <InputLabel
                for="name"
                value="Permission Name"
                class="block mb-1 dark:text-gray-200"
            />
            <TextInput
                id="name"
                type="text"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200"
            />
            <InputError
                :message="form.errors.name"
                class="mt-2 text-sm text-red-600 dark:text-red-400"
            />
        </div>
        
        
        <input type="hidden" v-model="form.guard_name" value="web"/>

        <PrimaryButton
            v-if="!form.processing"
            class="dark:bg-gray-700 dark:text-gray-100 m-auto"
            >Submit</PrimaryButton
        >
    </form>
</template>
