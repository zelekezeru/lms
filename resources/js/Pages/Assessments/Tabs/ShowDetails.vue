<script setup>
import { Link } from "@inertiajs/vue3";
import { defineProps } from "vue";

const props = defineProps({
    course: {
        type: Object,
        required: true,
    },
    instructor: {
        type: Object,
        required: true,
    },
    section: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="grid grid-cols-2 gap-2">
        <!-- Section Name -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Section</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
            >
                <Link
                    :href="route('sections.show', { section: section.id })"
                    class="text-blue-600 hover:underline dark:text-blue-400"
                >
                    {{ section.name }}
                </Link>
            </span>
        </div>

        <!-- Program name -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Program name</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
            >
                {{ section.program.name }}
            </span>
        </div>

        <!-- Course Name -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Course</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
            >
                <Link
                    :href="route('courses.show', { course: course.id })"
                    class="text-blue-600 hover:underline dark:text-blue-400"
                >
                    {{ course.name }}
                </Link>
            </span>
        </div>

        <!-- Instructor Name -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Instructor</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
            >
            <div v-if ="instructor" class="flex items-center">
                {{ instructor.user.name }}
            </div>
            <div v-else class="text-red-500 dark:text-gray-400">
                No instructor assigned 
            </div>
                
            </span>
        </div>

        <!-- Credit Hours  -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Credit Hours</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
            >
                {{ course.credit_hours }}
            </span>
        </div>          
        
    </div>
</template>
