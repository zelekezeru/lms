<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, defineProps, onMounted } from "vue";

const props = defineProps({
    section: Object,
    courses: Array,
    attachedcourses: Array,
});
const form = ref({ courses: [] });

// Initialize selectedcourses with existing attached courses
const selectedcourses = ref([...props.attachedcourses]);

const togglecourse = (courseId) => {
    if (selectedcourses.value.includes(courseId)) {
        selectedcourses.value = selectedcourses.value.filter(
            (id) => id !== courseId
        );
    } else {
        selectedcourses.value.push(courseId);
    }
};

const submit = () => {
    form.value.courses = selectedcourses.value;
    router.put(route("assignments.section-courses.assign", props.section.id), form.value);
};
</script>

<template>
    <Head title="Assign courses" />
    <AppLayout>
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Assign Courses to
                <span class="text-info">{{ section.name }}</span>
            </h2>
        </div>

        <form
            @submit.prevent="submit"
            class="dark:bg-gray-800 p-4 shadow rounded"
        >
            <div class="w-full py-5">
                <label class="block text-gray-100 dark:text-white mb-2"
                    >Select Courses For: <span class="text-info">"{{ section.name }}"</span></label
                >
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
                >
                    <div
                        v-for="course in courses"
                        :key="course.id"
                        class="flex items-center"
                    >
                        <input
                            type="checkbox"
                            :id="'perm-' + course.id"
                            :value="course.id"
                            :checked="
                                selectedcourses.includes(course.id)
                            "
                            v-model="selectedcourses"
                            class="text-primary focus:ring-primary border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        />
                        <label
                            :for="'perm-' + course.id"
                            class="ml-2 text-gray-700 dark:text-gray-100"
                        >
                            {{ course.name }}
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="px-6 py-2 text-white bg-blue-800 mt-4">Save</button>
        </form>
    </AppLayout>
</template>
