<script setup>
import { defineProps } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    form: Object,
    weights: Object,
    students: Object,
    years: Object,
    semesters: Object,
    sections: Object,
    courses: Object,
    users: Object,
    grades: Object,
});
</script>

<template>
    <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Result Point -->
            <div>
                <InputLabel for="point" value="Result Point" />
                <TextInput
                    id="point"
                    type="text"
                    v-model="form.point"
                    required
                />
                <InputError :message="form.errors.point" />
            </div>

            <!-- Result Description -->
            <div>
                <InputLabel for="description" value="Result Description" />
                <TextInput
                    id="description"
                    type="text"
                    v-model="form.description"
                />
                <InputError :message="form.errors.description" />
            </div>

            <!-- Changed Point -->
            <div>
                <InputLabel for="changed_point" value="Changed Point" />
                <TextInput
                    id="changed_point"
                    type="text"
                    v-model="form.changed_point"
                />
                <InputError :message="form.errors.changed_point" />
            </div>

            <!-- Weight Dropdown -->
            <div>
                <InputLabel for="weight_id" value="Weight" />
                <select id="weight_id" v-model="form.weight_id" required>
                    <option value="">Select Weight</option>
                    <option v-for="weight in weights" :key="weight.id" :value="weight.id">
                        {{ weight.name }}
                    </option>
                </select>
                <InputError :message="form.errors.weight_id" />
            </div>

            <!-- Student Dropdown -->
            <div>
                <InputLabel for="student_id" value="Student" />
                <select id="student_id" v-model="form.student_id" required>
                    <option value="">Select Student</option>
                    <option v-for="student in students" :key="student.id" :value="student.id">
                        {{ student.name }}
                    </option>
                </select>
                <InputError :message="form.errors.student_id" />
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

            <!-- Section Dropdown -->
            <div>
                <InputLabel for="section_id" value="Section" />
                <select id="section_id" v-model="form.section_id" required>
                    <option value="">Select Section</option>
                    <option v-for="section in sections" :key="section.id" :value="section.id">
                        {{ section.name }}
                    </option>
                </select>
                <InputError :message="form.errors.section_id" />
            </div>

            <!-- Course Dropdown -->
            <div>
                <InputLabel for="course_id" value="Course" />
                <select id="course_id" v-model="form.course_id" required>
                    <option value="">Select Course</option>
                    <option v-for="course in courses" :key="course.id" :value="course.id">
                        {{ course.name }}
                    </option>
                </select>
                <InputError :message="form.errors.course_id" />
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

            <!-- Grade Dropdown -->
            <div>
                <InputLabel for="grade_id" value="Grade" />
                <select id="grade_id" v-model="form.grade_id" required>
                    <option value="">Select Grade</option>
                    <option v-for="grade in grades" :key="grade.id" :value="grade.id">
                        {{ grade.name }}
                    </option>
                </select>
                <InputError :message="form.errors.grade_id" />
            </div>

            <!-- Changed By Dropdown -->
            <div>
                <InputLabel for="changed_by" value="Changed By" />
                <select id="changed_by" v-model="form.changed_by">
                    <option value="">Select User</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">
                        {{ user.name }}
                    </option>
                </select>
                <InputError :message="form.errors.changed_by" />
            </div>

            <!-- Changed At -->
            <div>
                <InputLabel for="changed_at" value="Changed At" />
                <TextInput
                    id="changed_at"
                    type="date"
                    v-model="form.changed_at"
                />
                <InputError :message="form.errors.changed_at" />
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
