<script setup>
import { ref } from "vue";
import ShowResults from "@/Pages/InstructorPortal/Components/ShowResults.vue";
import ShowWeights from "../../Components/ShowWeights.vue";
import ShowGrades from "../../Components/ShowGrades.vue";
import {
    DocumentTextIcon,
    PresentationChartBarIcon,
    CheckBadgeIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    section: { type: Object, required: true },
    course: { type: Object, required: true },
    semester: { type: Object, required: true },
    instructor: { type: Object, required: true },
    weights: { type: Object, required: true },
    grades: { type: Object, required: true },
    students: { type: Array, required: true },
    studentResults: { type: Array },
    courseOffering: { type: Object, required: true },
});

const tabs = [
    { key: "results",  label: "Results",  icon: DocumentTextIcon },
    { key: "weights",  label: "Weights",  icon: PresentationChartBarIcon },
    { key: "grades",   label: "Grades",   icon: CheckBadgeIcon },
];

const selectedTab = ref("results");
</script>

<template>
    <div class="space-y-4">
        <!-- Sub-tab pills -->
        <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="selectedTab = tab.key"
                :class="[
                    'flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold whitespace-nowrap transition-all duration-200 shrink-0',
                    selectedTab === tab.key
                        ? 'bg-violet-600 text-white shadow-md shadow-violet-200 dark:shadow-violet-900/30'
                        : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:border-violet-300 dark:hover:border-violet-700',
                ]"
            >
                <component :is="tab.icon" class="w-4 h-4" />
                {{ tab.label }}
            </button>
        </div>

        <!-- Panel -->
        <transition
            mode="out-in"
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div :key="selectedTab" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                <ShowResults
                    v-if="selectedTab === 'results'"
                    :section="section"
                    :course="course"
                    :semester="semester"
                    :instructor="instructor"
                    :weights="weights"
                    :studentsList="students"
                    :student-results="studentResults"
                    :course-offering="courseOffering"
                />
                <ShowWeights
                    v-else-if="selectedTab === 'weights'"
                    :weights="weights"
                    :section="section"
                    :course="course"
                    :semester="semester"
                    :instructor="instructor"
                    :course-offering="courseOffering"
                />
                <ShowGrades
                    v-else-if="selectedTab === 'grades'"
                    :section="section"
                    :weights="weights"
                    :course="course"
                    :semester="semester"
                    :instructor="instructor"
                    :grades="grades"
                    :studentsList="students"
                    :student-results="studentResults"
                    :course-offering="courseOffering"
                />
            </div>
        </transition>
    </div>
</template>
