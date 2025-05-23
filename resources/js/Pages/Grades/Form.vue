<script setup>
import { defineProps } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    form: Object,
    users: Object,
    years: Object,
    semesters: Object,
    courses: Object,
});
</script>

<template>
    <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput id="name" type="text" v-model="form.name" required />
                <InputError :message="form.errors.name" />
            </div>

            <!-- Grade Point -->
            <div>
                <InputLabel for="grade_point" value="Grade Point" />
                <TextInput
                    id="grade_point"
                    type="number"
                    v-model="form.grade_point"
                    required
                    min="0"
                    max="100"
                />
                <InputError :message="form.errors.grade_point" />
            </div>

            <!-- Description -->
            <div>
                <InputLabel for="description" value="Description" />
                <TextInput
                    id="description"
                    type="text"
                    v-model="form.description"
                />
                <InputError :message="form.errors.description" />
            </div>

            <!-- User Dropdown -->
            <div>
                <InputLabel for="user_id" value="User" />
                <select id="user_id" v-model="form.user_id" required>
                    <option value="">Select User</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">
                        {{ user.name }}
                    </option>
                </select>
                <InputError :message="form.errors.user_id" />
            </div>

            <!-- Year Dropdown -->
            <div>
                <InputLabel for="year_id" value="Year" />
                <select id="year_id" v-model="form.year_id" required>
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
                <select id="semester_id" v-model="form.semester_id" required>
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
                <select id="course_id" v-model="form.course_id" required>
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
