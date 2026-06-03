<script setup>
import { ref } from "vue";
import { 
    ListBulletIcon, 
    ViewColumnsIcon,
    ClockIcon,
    MapPinIcon,
    UserIcon,
    CalendarDaysIcon,
    InformationCircleIcon,
    AcademicCapIcon,
    ArrowLeftIcon,
    TagIcon
} from "@heroicons/vue/24/outline";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    classSessions: {
        type: Array,
        required: true,
    },
    student: {
        type: Object,
        required: false,
    }
});

const viewMode = ref("card");
const showDetail = ref(false);
const selectedSession = ref(null);

function viewDetails(session) {
    selectedSession.value = session;
    showDetail.value = true;
}

function backToList() {
    selectedSession.value = null;
    showDetail.value = false;
}

function toggleViewMode() {
    showDetail.value = false;
    viewMode.value = viewMode.value === "card" ? "table" : "card";
}

const getStatusStyles = (status) => {
    const s = status?.toLowerCase() || "";
    if (s.includes("completed") || s.includes("done") || s.includes("present")) {
        return "bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-300 border-green-100/50 dark:border-green-900/30";
    }
    if (s.includes("cancel")) {
        return "bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300 border-red-100/50 dark:border-red-900/30";
    }
    if (s.includes("progress") || s.includes("ongoing") || s.includes("active")) {
        return "bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-amber-100/50 dark:border-amber-900/30";
    }
    return "bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border-indigo-100/50 dark:border-indigo-900/30";
};

const getStatusDotColor = (status) => {
    const s = status?.toLowerCase() || "";
    if (s.includes("completed") || s.includes("done") || s.includes("present")) return "bg-green-500";
    if (s.includes("cancel")) return "bg-red-500";
    if (s.includes("progress") || s.includes("ongoing") || s.includes("active")) return "bg-amber-500";
    return "bg-indigo-500";
};
</script>

<template>
    <AppLayout>
        <div class="relative min-h-screen max-w-7xl mx-auto py-10 px-4 space-y-8">
            <!-- Decorative Glow Background Effects -->
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-indigo-200/40 dark:bg-indigo-950/20 rounded-full blur-3xl -z-10"></div>
            <div class="absolute bottom-10 left-1/4 w-96 h-96 bg-purple-200/30 dark:bg-purple-950/15 rounded-full blur-3xl -z-10"></div>

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-150/40 dark:border-gray-700/30 pb-6">
                <div class="space-y-1">
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        <CalendarDaysIcon class="w-8 h-8 text-indigo-500" />
                        Class Sessions Log
                    </h1>
                    <p class="text-sm text-gray-400 font-light">
                        Track your academic session history, timings, and class notes
                    </p>
                </div>

                <!-- Custom View Mode Switcher -->
                <button
                    v-if="!showDetail"
                    @click="toggleViewMode"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/70 dark:bg-gray-800/70 backdrop-blur-md border border-white/20 dark:border-gray-700/50 shadow-sm text-sm text-gray-700 dark:text-gray-250 hover:bg-gray-55 dark:hover:bg-gray-750 transition duration-200 font-bold"
                >
                    <component
                        :is="viewMode === 'card' ? ListBulletIcon : ViewColumnsIcon"
                        class="w-4 h-4 text-indigo-500"
                    />
                    {{ viewMode === "card" ? "Timeline View" : "Card View" }}
                </button>
            </div>

            <!-- Main Transition Body -->
            <transition
                mode="out-in"
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-4"
            >
                <div :key="`${viewMode}-${showDetail}`">
                    <!-- 1. SESSION DETAILS VIEW -->
                    <div
                        v-if="showDetail && selectedSession"
                        class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 sm:p-8 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-md space-y-6"
                    >
                        <div class="flex items-center gap-4">
                            <button
                                @click="backToList"
                                class="p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-300 rounded-xl transition duration-200 border border-gray-200/50 dark:border-gray-700/40"
                            >
                                <ArrowLeftIcon class="w-5 h-5" />
                            </button>
                            <div>
                                <h2 class="text-xl font-black text-gray-900 dark:text-white">
                                    {{ selectedSession.course.name }}
                                </h2>
                                <p class="text-xs text-gray-400 font-light mt-0.5">Session Overview & Materials</p>
                            </div>
                        </div>

                        <!-- Split Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                            <!-- Main Info Cards (Left) -->
                            <div class="md:col-span-2 space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="p-4 bg-gray-50/50 dark:bg-gray-900/40 rounded-2xl border border-gray-100/50 dark:border-gray-800/30 space-y-2">
                                        <div class="flex items-center gap-2 text-xs text-gray-400 font-bold uppercase tracking-wider">
                                            <ClockIcon class="w-4 h-4 text-indigo-500" />
                                            Schedule
                                        </div>
                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                            {{ selectedSession.date }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ selectedSession.startTime }} to {{ selectedSession.endTime }}
                                        </div>
                                    </div>

                                    <div class="p-4 bg-gray-50/50 dark:bg-gray-900/40 rounded-2xl border border-gray-100/50 dark:border-gray-800/30 space-y-2">
                                        <div class="flex items-center gap-2 text-xs text-gray-400 font-bold uppercase tracking-wider">
                                            <MapPinIcon class="w-4 h-4 text-purple-500" />
                                            Room / Location
                                        </div>
                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                            {{ selectedSession.room?.name || "TBD" }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            Capacity: {{ selectedSession.room?.capacity || "N/A" }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Class Notes / About -->
                                <div class="p-6 bg-indigo-50/20 dark:bg-indigo-950/10 rounded-2xl border border-indigo-100/35 dark:border-indigo-900/20 space-y-3">
                                    <div class="flex items-center gap-2 text-xs text-gray-400 font-bold uppercase tracking-wider">
                                        <InformationCircleIcon class="w-4 h-4 text-indigo-500" />
                                        Session Agenda & Details
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-normal whitespace-pre-line">
                                        {{ selectedSession.classAbout || "No specific agenda set for this class session. Please check with your instructor." }}
                                    </p>
                                </div>
                            </div>

                            <!-- Meta Details (Right Panel) -->
                            <div class="space-y-4 bg-gray-50/55 dark:bg-gray-900/20 p-5 rounded-2xl border border-gray-100 dark:border-gray-800/30 flex flex-col justify-between">
                                <div class="space-y-4">
                                    <div class="space-y-1">
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Status</span>
                                        <span :class="[getStatusStyles(selectedSession.status), 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border']">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotColor(selectedSession.status)"></span>
                                            {{ selectedSession.status }}
                                        </span>
                                    </div>

                                    <div class="space-y-2 pt-3 border-t border-gray-250/30 dark:border-gray-700/30">
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Instructor</span>
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-xl">
                                                <UserIcon class="w-5 h-5" />
                                            </div>
                                            <div>
                                                <span class="text-sm font-bold text-gray-800 dark:text-gray-250 block">
                                                    {{ selectedSession.instructor?.name || "TBA" }}
                                                </span>
                                                <span class="text-[10px] text-gray-400 font-light block">Instructor in Charge</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="selectedSession.type" class="space-y-1 pt-3 border-t border-gray-250/30 dark:border-gray-700/30">
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Session Type</span>
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-gray-700 dark:text-gray-300">
                                            <TagIcon class="w-3.5 h-3.5 text-indigo-500" />
                                            {{ selectedSession.type }}
                                        </span>
                                    </div>
                                </div>

                                <div class="pt-6">
                                    <PrimaryButton @click="backToList" class="w-full justify-center py-2.5 rounded-xl font-bold">
                                        Back to Sessions List
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. SESSIONS LIST/GRID VIEW -->
                    <div v-else>
                        <!-- A. TIMELINE LIST VIEW (Styled as modern timeline cards) -->
                        <div v-if="viewMode === 'table'" class="space-y-4">
                            <div v-if="classSessions.length > 0" class="relative pl-6 border-l-2 border-indigo-100 dark:border-indigo-900/65 space-y-6">
                                <div
                                    v-for="session in classSessions"
                                    :key="session.id"
                                    class="relative bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-md transition duration-200"
                                >
                                    <!-- Vertical timeline node dot -->
                                    <div class="absolute left-[-32px] top-[26px] w-4 h-4 rounded-full border-4 border-white dark:border-gray-900 shadow" :class="getStatusDotColor(session.status)"></div>

                                    <!-- Left: Date & Time block -->
                                    <div class="flex items-center gap-3 min-w-[190px]">
                                        <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 rounded-xl">
                                            <ClockIcon class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white block">{{ session.date }}</span>
                                            <span class="text-xs text-gray-400 block mt-0.5">{{ session.startTime }} - {{ session.endTime }}</span>
                                        </div>
                                    </div>

                                    <!-- Middle: Course name & details tags -->
                                    <div class="flex-1 space-y-2">
                                        <h3 class="text-base font-black text-gray-900 dark:text-white leading-tight">
                                            {{ session.course.name }}
                                        </h3>
                                        <div class="flex flex-wrap gap-2">
                                            <span :class="[getStatusStyles(session.status), 'inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full border']">
                                                {{ session.status }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 px-2.5 py-0.5 rounded-full border border-purple-100/50 dark:border-purple-900/30">
                                                <MapPinIcon class="w-3 h-3" />
                                                Room: {{ session.room?.name || 'TBD' }}
                                            </span>
                                            <span v-if="session.type" class="inline-flex items-center gap-1 text-[10px] font-bold bg-gray-50 text-gray-700 dark:bg-gray-950/40 dark:text-gray-300 px-2.5 py-0.5 rounded-full border border-gray-100/50 dark:border-gray-900/30">
                                                {{ session.type }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Right: Action Button -->
                                    <div class="flex items-center justify-end min-w-[120px]">
                                        <PrimaryButton
                                            @click="viewDetails(session)"
                                            class="w-full md:w-auto px-4 py-2 rounded-xl text-xs font-bold shadow-sm"
                                        >
                                            View Details
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-else class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-12 text-center rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm text-gray-400">
                                📅 No class sessions recorded.
                            </div>
                        </div>

                        <!-- B. CARD GRID VIEW (Standard cards with premium decorations) -->
                        <div v-else>
                            <div v-if="classSessions.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div
                                    v-for="session in classSessions"
                                    :key="session.id"
                                    class="group relative bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm hover:shadow-md transition duration-250 flex flex-col justify-between"
                                >
                                    <!-- Tiny subtle top-glow accent matching status -->
                                    <div class="absolute top-0 left-6 right-6 h-0.5 rounded-b-lg opacity-60 group-hover:opacity-100 transition duration-200" :class="getStatusDotColor(session.status)"></div>

                                    <div class="space-y-4">
                                        <!-- Header Block -->
                                        <div class="flex justify-between items-start gap-2">
                                            <h3 class="text-base font-black text-gray-900 dark:text-white leading-snug group-hover:text-indigo-550 dark:group-hover:text-indigo-400 transition">
                                                {{ session.course.name }}
                                            </h3>
                                            <span :class="[getStatusStyles(session.status), 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap']">
                                                {{ session.status }}
                                            </span>
                                        </div>

                                        <!-- Details list -->
                                        <div class="space-y-2 text-xs text-gray-650 dark:text-gray-350">
                                            <div class="flex items-center gap-2">
                                                <ClockIcon class="w-4 h-4 text-indigo-500" />
                                                <span>{{ session.date }} @ {{ session.startTime }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <MapPinIcon class="w-4 h-4 text-purple-500" />
                                                <span>Room: {{ session.room?.name || "TBD" }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <UserIcon class="w-4 h-4 text-indigo-500" />
                                                <span class="truncate">Instructor: {{ session.instructor?.name || "TBA" }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action -->
                                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700/40">
                                        <PrimaryButton
                                            class="w-full justify-center py-2.5 rounded-xl font-bold text-xs"
                                            @click="viewDetails(session)"
                                        >
                                            View Full details
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-12 text-center rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm text-gray-400">
                                📅 No class sessions recorded.
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
