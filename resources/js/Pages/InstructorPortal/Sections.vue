<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { 
    BookmarkIcon, 
    ArrowRightIcon, 
    UserGroupIcon 
} from "@heroicons/vue/24/outline";

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
                Sections you're teaching in during this semester.
            </p>
        </div>

        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
        >
            <div
                v-for="section in instructor.sections"
                :key="section.id"
                class="group transition duration-300 transform hover:-translate-y-1 hover:shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-2xl p-6 flex flex-col justify-between"
            >
                <div class="space-y-4">
                    <!-- Section Title & Code -->
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ section.name }}
                            </h3>
                            <span class="text-[10px] uppercase font-semibold tracking-wider text-gray-400 dark:text-gray-500">Section Code</span>
                        </div>
                        <span
                            class="text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 rounded-full px-2.5 py-1"
                        >
                            {{ section.code }}
                        </span>
                    </div>

                    <!-- Program and Track Info -->
                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700/60">
                        <p class="flex items-center gap-2">
                            <BookmarkIcon class="w-4 h-4 text-gray-400" />
                            <span><strong>Program:</strong> {{ section.program?.name }}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <BookmarkIcon class="w-4 h-4 text-gray-400" />
                            <span><strong>Track:</strong> {{ section.track?.name }}</span>
                        </p>
                        <div class="flex gap-4 pt-1 font-semibold text-gray-500">
                            <p>Year: <span class="text-gray-800 dark:text-gray-200">{{ section.yearLevel }}</span></p>
                            <p>Semester: <span class="text-gray-800 dark:text-gray-200">{{ section.semester?.level }}</span></p>
                        </div>
                    </div>

                    <!-- Status / Completion -->
                    <div class="text-xs font-semibold pt-1">
                        <span 
                            v-if="section.isCompleted" 
                            class="inline-flex items-center px-2 py-0.5 rounded text-green-700 bg-green-50 dark:bg-green-950/20 dark:text-green-400"
                        >
                            Completed
                        </span>
                        <span 
                            v-else 
                            class="inline-flex items-center px-2 py-0.5 rounded text-yellow-700 bg-yellow-50 dark:bg-yellow-950/20 dark:text-yellow-400"
                        >
                            In Progress
                        </span>
                    </div>
                </div>

                <!-- View Details Button -->
                <Link
                    :href="
                        route('instructor.sections.detail', {
                            section: section.id,
                        })
                    "
                    class="inline-flex items-center justify-center w-full mt-5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition space-x-2"
                >
                    <span>View Section Details</span>
                    <ArrowRightIcon class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" />
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
