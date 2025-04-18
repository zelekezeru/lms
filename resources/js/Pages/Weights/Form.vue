<script setup>
import { defineProps } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  form: { type: Object, required: true },
  courses: Array,
  sections: Array,
  years: Array,
  semesters: Array,
});
</script>

<template>
    <form @submit.prevent="emit('submit')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Name -->
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput id="name" type="text"class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" v-model="form.name" required />
                <InputError :message="form.errors.name" />
            </div>

            <!-- Weight Point -->
            <div>
                <InputLabel for="weight_point" value="Weight Point" />
                <TextInput
                    id="weight_point"
                    type="number"
                   class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" v-model="form.weight_point"
                    required
                    min="0"
                    max="100"
                />
                <InputError :message="form.errors.weight_point" />
            </div>

            <!-- Weight Description -->
            <div>
                <InputLabel for="weight_description" value="Weight Description" />
                <TextInput
                    id="weight_description"
                    type="text"
                   class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" v-model="form.weight_description"
                />
                <InputError :message="form.errors.weight_description" />
            </div>

            <!-- Year Dropdown -->
            <div>
                <InputLabel for="year_id" value="Year" />
                <select id="year_id"class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" v-model="form.year_id" required>
                    <option value="">Select Year</option>
                    <option v-for="year in years" :key="year.id" :value="year.id">
                        {{ year.name }}
                    </option>
                </select>
                <InputError :message="form.errors.year_id" />
            </div>

            <!-- Semester Dropdown -->
            <div>
                <InputLabel for="semester_id" value="Semester" />
                <select id="semester_id"class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" v-model="form.semester_id" required>
                    <option value="">Select Semester</option>
                    <option
                        v-for="semester in semesters"
                        :key="semester.id"
                        :value="semester.id"
                    >
                        {{ semester.name }}
                    </option>
                </select>
                <InputError :message="form.errors.semester_id" />
            </div>

            <!-- Course Dropdown -->
            <div>
                <InputLabel for="course_id" value="Course" />
                <select id="course_id"class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" v-model="form.course_id" required>
                    <option value="">Select Course</option>
                    <option
                        v-for="course in courses"
                        :key="course.id"
                        :value="course.id"
                    >
                        {{ course.name }}
                    </option>
                </select>
                <InputError :message="form.errors.course_id" />
            </div>
        </div>

        <div class="mt-6">
            <button
                type="submit"
                :disabled="form.processing"
                class="bg-indigo-600 text-white px-4 py-2 rounded"
            >
                Submit
            </button>
        </div>
    </form>
</template>
