<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    form: { type: Object, required: true },
    users: Array,
    courses: Array,
    years: Array,
    semesters: Array,
    sections: Array,
});

const emit = defineEmits(["submit"]);
</script>

<template>
    <form @submit.prevent="emit('submit')">

        <!-- Instructor Hidden Id -->
        <input type="hidden" v-model="form.instructor_id" />

        <!-- Name & Weight Point -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <InputLabel for="name" value="Weight Name" />
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
                <InputLabel for="point" value="Weight Point" />
                <input
                    id="point"
                    type="number"
                    v-model="form.point"
                    required
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
                <InputError :message="form.errors.point" />
            </div>
        </div>

        <!-- Foreign Keys -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <InputLabel for="course_id" value="Course" />
                <select
                    v-model="form.course_id"
                    id="course_id"
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                    <option value="">Select Course</option>
                    <option v-for="course in courses" :key="course.id" :value="course.id">
                        {{ course.name }}
                    </option>
                </select>
                <InputError :message="form.errors.course_id" />
            </div>

            <div>
                <InputLabel for="section_id" value="Section" />
                <select
                    v-model="form.section_id"
                    id="section_id"
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                    <option value="">Select section</option>
                    <option v-for="section in sections" :key="section.id" :value="section.id">
                        {{ section.name }} - {{ section.program.name }}
                    </option>
                </select>
                <InputError :message="form.errors.section_id" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

            <div>
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

            <!-- Description -->
            <div class="mt-4">
                <InputLabel for="description" value="Description" />
                <textarea
                    id="description"
                    v-model="form.description"
                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                ></textarea>
                <InputError :message="form.errors.description" />
            </div>
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
