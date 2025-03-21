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

const emit = defineEmits(["submit"]);
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <div>
            <InputLabel for="name" value="Program Name" />
            <TextInput id="name" v-model="form.name" required />
            <InputError :message="form.errors.name" />
        </div>

        <div>
            <InputLabel for="language" value="Program Language" />
            <TextInput id="language" v-model="form.language" required />
            <InputError :message="form.errors.language" />
        </div>

        <div>
            <InputLabel for="description" value="Description" />
            <TextInput id="description" v-model="form.description" />
            <InputError :message="form.errors.description" />
        </div>

        <div>
            <InputLabel for="user_id" value="Select Program Director" />
            <select id="user_id" v-model="form.user_id" class="w-full p-2 border rounded">
                <option disabled value="">Select Program Director</option>
                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
            </select>
            <InputError :message="form.errors.user_id" />
        </div>

        <div class="flex justify-center">
            <PrimaryButton :disabled="form.processing">Submit</PrimaryButton>
        </div>
    </form>
</template>