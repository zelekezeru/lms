<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
  form: Object,
  submit: Function,
  users: Array,
  programs: Array,
});
</script>

<template>
  <div class="dark:bg-gray-800 shadow-lg rounded-md p-6 max-w-lg w-full">
    <h2 class="text-xl font-semibold mb-4 dark:text-gray-200">Department Form</h2>
    <form @submit.prevent="submit">
      <!-- Program Director Dropdown -->
      <div class="mb-4">
        <InputLabel for="program_id" value="Select Program Director" />
        <select v-model="form.program_id" class="w-full px-3 py-2 border rounded-md">
          <option value="">Select Program</option>
          <option v-for="program in programs" :key="program.id" :value="program.id">
            {{ program.name }} in {{ program.language }}
          </option>
        </select>
        <InputError :message="form.errors.program_id" />
      </div>

      <!-- Department Name Field -->
      <div class="mb-4">
        <InputLabel for="name" value="Department Name" />
        <TextInput id="name" type="text" v-model="form.name" required autocomplete="name" />
        <InputError :message="form.errors.name" />
      </div>

      <!-- Description Field -->
      <div class="mb-4">
        <InputLabel for="description" value="Description" />
        <TextInput id="description" type="text" v-model="form.description" />
        <InputError :message="form.errors.description" />
      </div>

      <!-- Department Head Dropdown -->
      <div class="mb-4">
        <InputLabel for="user_id" value="Select Department Head" />
        <select v-model="form.user_id" class="w-full px-3 py-2 border rounded-md">
          <option value="">Select Head</option>
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.name }}
          </option>
        </select>
        <InputError :message="form.errors.user_id" />
      </div>

      <!-- Duration Field -->
      <div class="mb-4">
        <InputLabel for="duration" value="Duration" />
        <TextInput id="duration" type="number" v-model="form.duration" />
        <InputError :message="form.errors.duration" />
      </div>

      <!-- Submit Button -->
      <PrimaryButton v-if="!form.processing">Submit</PrimaryButton>
    </form>
  </div>
</template>