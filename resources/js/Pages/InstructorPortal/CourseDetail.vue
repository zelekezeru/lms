<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import StudentLayout from "@/Layouts/AppLayout.vue";
import {
    BookOpenIcon,
    DocumentIcon,
    ClipboardIcon,
    PencilSquareIcon,
    ChatBubbleBottomCenterTextIcon,
    MegaphoneIcon,
    AcademicCapIcon,
    ArrowRightIcon,
} from "@heroicons/vue/24/outline";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    course: Object,
    instructor: Object,
});

// Right menu navigation items
const rightMenu = [
    { name: "Outline", icon: BookOpenIcon },
    { name: "Sections", icon: DocumentIcon },
    { name: "Other Instructors", icon: ClipboardIcon },
    { name: "Assignments", icon: PencilSquareIcon },
    { name: "Forum", icon: ChatBubbleBottomCenterTextIcon },
    { name: "Announcements", icon: MegaphoneIcon },
];

// Function to determine progress bar color based on marks
const getProgressColor = (mark) => {
    if (mark >= 90) return "bg-green-500";
    if (mark >= 75) return "bg-yellow-500";
    return "bg-red-500";
};
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 flex flex-col lg:flex-row">
            <!-- Course Info -->
            <div class="flex-1">
                <h1
                    class="text-3xl font-bold mb-4 text-gray-900 dark:text-white flex"
                >
                    <AcademicCapIcon class="h-6 w-6 mr-2 text-blue-500" />
                    {{ course.name }} ({{ course.code }})
                </h1>
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4"
                >
                    <p>
                        <strong>Description:</strong>
                        {{ course.description || "No description available." }}
                    </p>
                    <p>
                        <strong>Sections:</strong> {{ course.sections.length }}
                    </p>
                    <p><strong>Credits:</strong> {{ course.credit_hours }}</p>
                </div>

                <!-- Sections Section -->
                <div class="mt-10">
                    <h2
                        class="text-2xl font-semibold text-gray-900 dark:text-white mb-6"
                    >
                        Sections Taught
                    </h2>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
                    >
                        <div
                            v-for="section in course.sections"
                            :key="section.id"
                            class="transition duration-300 transform hover:-translate-y-1 hover:shadow-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 space-y-4"
                        >
                            <!-- Section Title -->
                            <div class="flex justify-between items-center">
                                <h3
                                    class="text-xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ section.name }}
                                </h3>
                                <span
                                    class="text-xs font-medium text-white bg-blue-500 rounded-full px-3 py-1"
                                >
                                    {{ section.code }}
                                </span>
                            </div>

                            <!-- Program and Track Info -->
                            <div
                                class="text-sm text-gray-700 dark:text-gray-300 space-y-1"
                            >
                                <p>
                                    <strong>Program:</strong>
                                    {{ section.program?.name }} ({{
                                        section.program?.code
                                    }})
                                </p>
                                <p>
                                    <strong>Track:</strong>
                                    {{ section.track?.name }} ({{
                                        section.track?.code
                                    }})
                                </p>
                                <p>
                                    <strong>Year Level:</strong>
                                    {{ section.yearLevel }}
                                </p>
                                <p>
                                    <strong>Semester:</strong>
                                    {{ section.semesterLevel }}
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
                                <p
                                    class="text-yellow-600 dark:text-yellow-400"
                                    v-else
                                >
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
                                class="group inline-flex items-center justify-center w-full mt-3 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg shadow transition duration-300 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
                            >
                                <ArrowRightIcon
                                    class="w-4 h-4 mr-2 text-white transition-transform duration-300 group-hover:translate-x-1"
                                />
                                View Details
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
