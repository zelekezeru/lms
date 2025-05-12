<script setup>
import StudentLayout from "@/Layouts/StudentLayout.vue";
import {
    BookOpenIcon,
    DocumentIcon,
    ClipboardIcon,
    PencilSquareIcon,
    ChatBubbleBottomCenterTextIcon,
    MegaphoneIcon,
    AcademicCapIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    course: Object,
});

// Right menu navigation items
const rightMenu = [
    { name: "Outline", icon: BookOpenIcon },
    { name: "Materials", icon: DocumentIcon },
    { name: "Assessments", icon: ClipboardIcon },
    { name: "Assignments", icon: PencilSquareIcon },
    { name: "Forum", icon: ChatBubbleBottomCenterTextIcon },
    { name: "Announcements", icon: MegaphoneIcon },
];

// Sample assessments data with marks
const assessments = [
    {
        id: 1,
        title: "Midterm Exam",
        date: "2025-05-15",
        description: "The midterm exam covering all topics so far.",
        due_date: "2025-05-14",
        mark: 85, // Mark out of 100
    },
    {
        id: 2,
        title: "Final Project",
        date: "2025-06-01",
        description:
            "A final project that includes a detailed report and presentation.",
        due_date: "2025-05-30",
        mark: 92, // Mark out of 100
    },
    {
        id: 3,
        title: "Quiz 1",
        date: "2025-05-10",
        description: "A short quiz on the first 5 lectures.",
        due_date: "2025-05-09",
        mark: 75, // Mark out of 100
    },
];

// Function to determine progress bar color based on marks
const getProgressColor = (mark) => {
    if (mark >= 90) return "bg-green-500";
    if (mark >= 75) return "bg-yellow-500";
    return "bg-red-500";
};
</script>

<template>
    <StudentLayout>
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
                    <p><strong>Credits:</strong> {{ course.credit_hours }}</p>
                    <p><strong>Instructor:</strong> Dr. Jane Doe</p>
                    <p>
                        <strong>Description:</strong>
                        {{ course.description || "No description available." }}
                    </p>
                </div>

                <!-- Assessments Section -->
                <div class="mt-4">
                    <h2
                        class="text-2xl font-semibold text-gray-900 dark:text-white mb-6"
                    >
                        Assessments
                    </h2>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <div
                            v-for="assessment in assessments"
                            :key="assessment.id"
                            :class="`bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col space-y-4 ${getProgressColor(
                                assessment.mark
                            )}`"
                        >
                            <h3
                                class="text-xl font-semibold text-gray-900 dark:text-white"
                            >
                                {{ assessment.title }}
                            </h3>

                            <!-- Mark and Progress Bar -->
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-white">
                                    {{ assessment.mark }} / 100
                                </span>
                                <div
                                    class="w-full bg-gray-300 rounded-full h-2 mt-2"
                                >
                                    <div
                                        :style="{
                                            width: assessment.mark + '%',
                                        }"
                                        class="h-2 rounded-full"
                                        :class="
                                            getProgressColor(assessment.mark)
                                        "
                                    ></div>
                                </div>
                            </div>

                            <button
                                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Navigation -->
            <div class="w-full lg:w-1/3 mt-8 lg:mt-0 lg:relative z-50">
                <div
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg p-4 max-h-[70vh] lg:fixed lg:top-32 lg:right-12 lg:w-64 overflow-y-auto"
                >
                    <h2
                        class="text-lg font-bold text-gray-800 dark:text-white mb-4 lg:mb-4"
                    >
                        Course Navigation
                    </h2>

                    <ul
                        class="space-y-2 lg:space-y-2 flex flex-col lg:flex-col"
                    >
                        <li
                            v-for="item in rightMenu"
                            :key="item.name"
                            class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-800 dark:text-gray-200 transition duration-200 cursor-pointer"
                        >
                            <component :is="item.icon" class="w-5 h-5 mr-3" />
                            <span>{{ item.name }}</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </StudentLayout>
</template>
