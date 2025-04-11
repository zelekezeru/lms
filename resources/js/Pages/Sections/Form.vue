<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  form: { type: Object, required: true },
  users: Array,
  programs: Array,
  departments: Array,
  years: Array,
  semesters: Array,
});

const emit = defineEmits(['submit']);
</script>

<template>
  <form @submit.prevent="emit('submit')">
    <!-- Name & Code -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <InputLabel for="name" value="Section Name" />
        <input
          id="name"
          type="text"
          v-model="form.name"
          required
          class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <InputError :message="form.errors.name" />
      </div>

      <div>
        <InputLabel for="code" value="Section Code" />
        <input
          id="code"
          type="text"
          v-model="form.code"
          required
          class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <InputError :message="form.errors.code" />
      </div>
    </div>

    <!-- Foreign Keys -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div>
        <InputLabel for="user_id" value="Assigned User" />
        <select
          v-model="form.user_id"
          id="user_id"
          class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option value="">Select User</option>
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.name }}
          </option>
        </select>
        <InputError :message="form.errors.user_id" />
      </div>

      <div>
        <InputLabel for="program_id" value="Program" />
        <select
          v-model="form.program_id"
          id="program_id"
          class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option value="">Select Program</option>
          <option v-for="program in programs" :key="program.id" :value="program.id">
            {{ program.name }}
          </option>
        </select>
        <InputError :message="form.errors.program_id" />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div>
        <InputLabel for="department_id" value="Department" />
        <select
          v-model="form.department_id"
          id="department_id"
          class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option value="">Select Department</option>
          <option v-for="department in departments" :key="department.id" :value="department.id">
            {{ department.name }}
          </option>
        </select>
        <InputError :message="form.errors.department_id" />
      </div>

      <div>
        <InputLabel for="year_id" value="Year" />
        <select
          v-model="form.year_id"
          id="year_id"
          class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option value="">Select Year</option>
          <option v-for="year in years" :key="year.id" :value="year.id">
            {{ year.name }}
          </option>
        </select>
        <InputError :message="form.errors.year_id" />
      </div>
    </div>

    <div class="mt-4">
      <InputLabel for="semester_id" value="Semester" />
      <select
        v-model="form.semester_id"
        id="semester_id"
        class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
      >
        <option value="">Select Semester</option>
        <option v-for="semester in semesters" :key="semester.id" :value="semester.id">
          {{ semester.name }}
        </option>
      </select>
      <InputError :message="form.errors.semester_id" />
    </div>

    <!-- Submit Button -->
    <div class="mt-6 flex justify-center">
      <button
        type="submit"
        :disabled="form.processing"
        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <span v-if="!form.processing">Submit</span>
        <span v-else>Submitting...</span>
      </button>
    </div>
  </form>
</template>
