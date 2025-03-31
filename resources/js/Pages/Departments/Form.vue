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
  <form @submit.prevent="submit">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Program Dropdown -->
      <div>
        <InputLabel
          for="program_id"
          value="Select Program"
          class="block mb-1 text-gray-200"
        />
        <select
          id="program_id"
          v-model="form.program_id"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
        >
          <option value="">Select Program</option>
          <option
            v-for="program in programs"
            :key="program.id"
            :value="program.id"
          >
            {{ program.name }} in {{ program.language }}
          </option>
        </select>
        <InputError
          :message="form.errors.program_id"
          class="mt-2 text-sm text-red-500"
        />
      </div>

      <!-- Department Name -->
      <div>
        <InputLabel
          for="name"
          value="Department Name"
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

      <!-- Description -->
      <div>
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

      <!-- Department Head Dropdown -->
      <div>
        <InputLabel
          for="user_id"
          value="Select Department Head"
          class="block mb-1 text-gray-200"
        />
        <select
          id="user_id"
          v-model="form.user_id"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
        >
          <option value="">Select Department Head</option>
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.name }}
          </option>
        </select>
        <InputError
          :message="form.errors.user_id"
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