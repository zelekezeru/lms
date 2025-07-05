<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import {
    ArrowPathIcon,
    PresentationChartBarIcon,
} from "@heroicons/vue/24/solid";
import Swal from "sweetalert2";
import {
    ArrowDownOnSquareIcon,
    EyeIcon,
    InformationCircleIcon,
    LockClosedIcon,
} from "@heroicons/vue/24/outline";
import ShowDetails from "./Tabs/ShowDetails.vue";
import ShowSections from "./Tabs/ShowSections.vue";
import ShowSemesters from "./Tabs/ShowSemesters.vue";
import ShowCloseForm from "./Tabs/ShowCloseForm.vue";

const props = defineProps({
    oldSemesters: Object,
    activeSemester: Object,
    sections: Array,
    search: String,
    gradesPercentage: Object,
    studyModes: {
        required: true,
        type: Array,
    },
});

const refreshing = ref(false);
const search = ref(props.search || "");

const refreshData = () => {
    refreshing.value = true;
    router.visit(route("calendars.index"), {
        only: ["semesters"],
        onFinish: () => (refreshing.value = false),
    });
};

const searchSemesters = () => {
    router.get(
        route("semesters.index"),
        { search: search.value },
        { preserveState: true }
    );
};

const selectedTab = ref("status");

const tabs = [
    { key: "status", label: "Active Semester", icon: InformationCircleIcon },
    {
        key: "closeForm",
        label: "Close Semesters",
        icon: ArrowDownOnSquareIcon,
    },
    {
        key: "sections",
        label: "Active Sections",
        icon: PresentationChartBarIcon,
    },
    { key: "semesters", label: "Older Semesters", icon: LockClosedIcon },
];

const setSemesterOf = ref(null);

const showCloseForm = (studyModeId) => {
    (setSemesterOf.value = studyModeId), (selectedTab.value = "closeForm");
};

const backToStatus = () => {
    selectedTab.value = "status";
};
</script>

<template>
    <AppLayout>
        <h1
            class="mb-6 text-3xl font-bold text-center text-gray-900 dark:text-white"
        >
            {{ $t("semester.calender") }}
        </h1>
        <nav
            class="flex justify-center space-x-4 overflow-x-auto pb-2 mb-6 border-b border-gray-200 dark:border-gray-700"
        >
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="selectedTab = tab.key"
                class="flex-shrink-0 flex items-center px-4 py-2 space-x-2 text-sm font-medium transition whitespace-nowrap"
                :class="
                    selectedTab === tab.key
                        ? 'border-b-2 border-indigo-500 text-indigo-600'
                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200'
                "
            >
                <component :is="tab.icon" class="w-5 h-5" />
                <span>{{ tab.label }}</span>
            </button>
        </nav>
        <!-- Details Panel -->
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
                <ShowDetails
                    v-if="selectedTab === 'status'"
                    :studyModes="studyModes"
                    :active-semester="props.activeSemester"
                    :show-close-semester="showCloseForm"
                />

                <!-- Courses Panel -->
                <ShowCloseForm
                    v-else-if="selectedTab === 'closeForm'"
                    :studyModes="studyModes"
                    :setSemesterOf="setSemesterOf"
                    :back-to-status="backToStatus"
                />

                <!-- Courses Panel -->
                <ShowSections
                    v-else-if="selectedTab === 'sections'"
                    :sections="props.sections"
                    :gradesPercentage="props.gradesPercentage"
                />

                <!-- Semesters Panel -->
                <ShowSemesters
                    v-else-if="selectedTab === 'semesters'"
                    :oldSemesters="props.oldSemesters"
                    :active-semester="props.activeSemester"
                    :search="search"
                    @search="searchSemesters"
                />
            </div>
        </transition>
    </AppLayout>
</template>
