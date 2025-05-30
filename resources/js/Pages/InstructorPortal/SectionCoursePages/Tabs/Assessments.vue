<script setup>
import ShowResults from "@/Pages/InstructorPortal/Components/ShowResults.vue";
import ShowWeights from "../../Components/ShowWeights.vue";
import ShowGrades from "../../Components/ShowGrades.vue";
import {
    CheckBadgeIcon,
    CogIcon,
    DocumentTextIcon,
    PresentationChartBarIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    course: {
        type: Object,
        required: true,
    },
    semester: {
        type: Object,
        required: true,
    },
    instructor: {
        type: Object,
        required: true,
    },
    weights: {
        type: Object,
        required: true,
    },
    grades: {
        type: Object,
        required: true,
    },
    students: {
        type: Array,
        required: true,
    },
});

// Tabs
const tabs = [
    { key: "results", label: "results", icon: DocumentTextIcon },
    { key: "weights", label: "weights", icon: PresentationChartBarIcon },
    { key: "grades", label: "Grades", icon: CheckBadgeIcon },
];

const selectedTab = ref("results");

const changeTab = (tabKey) => {
    selectedTab.value = tabKey;
    route().params.assessmentTab = tabKey;
}
</script>

<template>
    <nav
        class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700"
    >
        <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="changeTab(tab.key)"
            :class="[
                'flex items-center px-4 py-2 space-x-2 text-sm font-medium transition',
                selectedTab === tab.key
                    ? 'border-b-2 border-indigo-500 text-indigo-600'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
            ]"
        >
            <component :is="tab.icon" class="w-5 h-5" />
            <span>{{ tab.label }}</span>
        </button>
    </nav>
    <transition
        mode="out-in"
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 scale-75"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-75"
    >
        <div
            :key="selectedTab"
            class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
        >
            <!-- Results Panel -->
            <ShowResults
                v-if="selectedTab == 'results'"
                :section="section"
                :course="course"
                :semester="semester"
                :instructor="instructor"
                :weights="weights"
                :studentsList="students"
            />

            <!-- Weights Panel -->
            <ShowWeights
                v-else-if="selectedTab == 'weights'"
                :weights="weights"
                :section="section"
                :course="course"
                :semester="semester"
                :instructor="instructor"
            />

            <!-- Grades Panel -->
            <ShowGrades
                v-else-if="selectedTab == 'grades'"
                :section="section"
                :weights="weights"
                :course="course"
                :semester="semester"
                :instructor="instructor"
                :grades="grades"
                :studentsList="students"
            />
        </div>
    </transition>
</template>
