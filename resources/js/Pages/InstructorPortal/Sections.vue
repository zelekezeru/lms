<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    instructor: {
        required: true,
        type: Object,
    },
});
</script>

<template>
    <AppLayout>
        <div class="mb-8">
            <h1
                class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white"
            >
                My Sections
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                Sections you're teaching in.
            </p>
        </div>
        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
        >
            <div
                v-for="section in instructor.sections"
                :key="section.id"
                class="transition duration-300 transform hover:-translate-y-1 hover:shadow-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 space-y-4"
            >
                <!-- Section Title -->
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ section.name }}
                    </h3>
                    <span
                        class="text-xs font-medium text-white bg-blue-500 rounded-full px-3 py-1"
                    >
                        {{ section.code }}
                    </span>
                </div>

                <!-- Program and Track Info -->
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                    <p>
                        <strong>Program:</strong>
                        {{ section.program?.name }} ({{
                            section.program?.code
                        }})
                    </p>
                    <p>
                        <strong>Track:</strong>
                        {{ section.track?.name }} ({{ section.track?.code }})
                    </p>
                    <p>
                        <strong>Year Level:</strong>
                        {{ section.yearLevel }}
                    </p>
                    <p>
                        <strong>Semester:</strong>
                        {{ section.semester?.level }}
                    </p>
                </div>

                <!-- Status / Completion -->
                <div class="text-sm">
                    <p
                        class="text-green-600 dark:text-green-400"
                        v-if="section.isCompleted"
                    >
                        ✅ Completed
                    </p>
                    <p class="text-yellow-600 dark:text-yellow-400" v-else>
                        ⏳ In Progress
                    </p>
                </div>

                <!-- View Details Button -->
                <Link
                    :href="
                        route('instructor.sections.detail', {
                            section: section.id,
                        })
                    "
                    class="w-full mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg shadow"
                >
                    View Details
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
