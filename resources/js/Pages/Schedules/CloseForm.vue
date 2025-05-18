<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { watch } from "vue";

const props = defineProps({
  semester: Object,
  semesters: Array,
});

// Initialize form with inertia useForm for posting
const form = useForm({
  approval: false,
  new_semester_id: null,
  new_semester_start_date: '',
  new_semester_end_date: '',
  errors: {},
});

// When component loads, set the form to current active semester info by default
if (props.semester) {
  form.new_semester_id = props.semester.id;
  form.new_semester_start_date = props.semester.start_date || '';
  form.new_semester_end_date = props.semester.end_date || '';
}

// Watch selected semester id changes and update start/end dates accordingly
watch(() => form.new_semester_id, (newId) => {
  if (newId === props.semester.id) {
    // Selected is the current active semester
    form.new_semester_start_date = props.semester.start_date || '';
    form.new_semester_end_date = props.semester.end_date || '';
  } else {
    // Find selected semester from semesters array
    const selectedSemester = props.semesters.find(s => s.id === newId);
    if (selectedSemester) {
      form.new_semester_start_date = selectedSemester.start_date || '';
      form.new_semester_end_date = selectedSemester.end_date || '';
    } else {
      form.new_semester_start_date = '';
      form.new_semester_end_date = '';
    }
  }
});

const submit = () => {
  form.post(route("semesters.close"), {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>

<template>
  <AppLayout>
    <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Close Active Semester</h2>

    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow space-y-4">
      <p class="text-gray-700 dark:text-gray-300 underline">
        <strong class="text-gray-900 dark:text-white">Current Active Semester: </strong> 
                        <span class="mb-1 text-md font-bold text-green-900 dark:text-green-200"> {{ semester.name }} </span>
      </p>
      
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-gray-200">Select Next Opening Semester</label>
          <select
            v-model="form.new_semester_id"
            class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          >
            <!-- Disabled Current Active Semester -->
            <option
              :value="semester.id"
              disabled
              class="text-green-500 dark:text-green-400 bg-white dark:bg-gray-700"
            >
              🟢 {{ semester.name }} of Year {{ semester.year?.name ?? 'N/A' }} - Current Active
            </option>

            <!-- Loop Through Inactive Semesters -->
            <option
              v-for="sem in semesters"
              :key="sem.id"
              :value="sem.id"
              class="bg-white text-green-500 dark:text-green-400 bg-white dark:bg-gray-700"
            >
              🔴 <span class="text-red-600 dark:text-red-400">{{ sem.name }}</span> of Year {{ sem.year?.name ?? 'N/A' }}
            </option>
          </select>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-gray-200">Start Date</label>
              <input
                v-model="form.new_semester_start_date"
                type="date"
                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
              />
              <div class="text-sm text-red-500 dark:text-red-400" v-if="form.errors.new_semester_start_date">
                {{ form.errors.new_semester_start_date }}
              </div>
            </div>

            <div>
              <label class="block mb-1 text-sm font-medium text-gray-900 dark:text-gray-200">End Date</label>
              <input
                v-model="form.new_semester_end_date"
                type="date"
                class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
              />
              <div class="text-sm text-red-500 dark:text-red-400" v-if="form.errors.new_semester_end_date">
                {{ form.errors.new_semester_end_date }}
              </div>
            </div>
          </div>
        </div>

        <div>
          <label class="inline-flex items-center text-gray-900 dark:text-gray-200">
            <input
              type="checkbox"
              v-model="form.approval"
              class="mr-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
            />
            I confirm to close the current semester and activate a new one.
          </label>
          <div class="text-sm text-red-500 dark:text-red-400" v-if="form.errors.approval">
            {{ form.errors.approval }}
          </div>
        </div>

        <button
          type="submit"
          class="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 text-white px-6 py-2 rounded"
          :disabled="form.processing"
        >
          Close Semester
        </button>
      </form>
    </div>
  </AppLayout>
</template>
