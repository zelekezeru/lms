<script setup>
import { ref, computed } from "vue";
import { Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    BookOpenIcon,
    CheckCircleIcon,
    XCircleIcon,
    ExclamationCircleIcon,
    AcademicCapIcon,
    ClockIcon,
    ArrowRightIcon,
    MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";
import { CheckCircleIcon as CheckSolid } from "@heroicons/vue/24/solid";

const props = defineProps({
    student: { type: Object, required: true },
});

const search = ref("");

const tabs = [
    { key: "enrolled", label: "Active", icon: BookOpenIcon, color: "indigo" },
    { key: "completed", label: "Done", icon: CheckCircleIcon, color: "emerald" },
    { key: "dropped", label: "Dropped", icon: XCircleIcon, color: "rose" },
    { key: "failed", label: "Failed", icon: ExclamationCircleIcon, color: "amber" },
];

const activeTab = ref("enrolled");

const filtered = computed(() => {
    const byStatus = (props.student.enrollments || []).filter(
        (e) => e.status === activeTab.value
    );
    const q = search.value.trim().toLowerCase();
    if (!q) return byStatus;
    return byStatus.filter(
        (e) =>
            e.course.name.toLowerCase().includes(q) ||
            e.course.code.toLowerCase().includes(q) ||
            (e.instructor?.name ?? "").toLowerCase().includes(q)
    );
});

const countFor = (key) =>
    (props.student.enrollments || []).filter((e) => e.status === key).length;

const cardAccents = [
    "from-indigo-500 to-purple-600",
    "from-sky-500 to-blue-600",
    "from-emerald-500 to-teal-600",
    "from-rose-500 to-pink-600",
    "from-amber-500 to-orange-600",
    "from-violet-500 to-indigo-600",
];

const tabActiveClasses = {
    indigo: "bg-indigo-600 text-white shadow-indigo-200 dark:shadow-indigo-900/30 shadow-md",
    emerald: "bg-emerald-600 text-white shadow-emerald-200 dark:shadow-emerald-900/30 shadow-md",
    rose: "bg-rose-600 text-white shadow-rose-200 dark:shadow-rose-900/30 shadow-md",
    amber: "bg-amber-500 text-white shadow-amber-200 dark:shadow-amber-900/30 shadow-md",
};

const tabInactiveClasses = "bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600";

const statusBadge = {
    enrolled: "bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400",
    completed: "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400",
    dropped: "bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400",
    failed: "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400",
};
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-5xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">My Courses</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ (student.enrollments || []).length }} total enrollments
                    </p>
                </div>
            </div>

            <!-- Search bar -->
            <div class="relative">
                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search courses, instructors…"
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                />
            </div>

            <!-- Tab pills — horizontally scrollable on mobile -->
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="activeTab = tab.key"
                    :class="[
                        'flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-semibold whitespace-nowrap transition-all duration-200 shrink-0',
                        activeTab === tab.key
                            ? tabActiveClasses[tab.color]
                            : tabInactiveClasses,
                    ]"
                >
                    <component :is="tab.icon" class="w-4 h-4" />
                    {{ tab.label }}
                    <span
                        :class="[
                            'ml-0.5 rounded-full text-xs px-1.5 py-0.5 font-bold',
                            activeTab === tab.key ? 'bg-white/25 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                        ]"
                    >
                        {{ countFor(tab.key) }}
                    </span>
                </button>
            </div>

            <!-- Course cards -->
            <div v-if="filtered.length" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Link
                    v-for="(enrollment, idx) in filtered"
                    :key="enrollment.id"
                    :href="enrollment.status === 'enrolled' ? route('student.enrollments.show', enrollment.id) : '#'"
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-md transition group"
                >
                    <div :class="['h-1.5 bg-gradient-to-r', cardAccents[idx % cardAccents.length]]"></div>
                    <div class="p-4">
                        <!-- Title row -->
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-sm text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition line-clamp-2 leading-snug">
                                    {{ enrollment.course.name }}
                                </h3>
                                <p class="text-xs text-gray-400 mt-0.5">{{ enrollment.course.code }}</p>
                            </div>
                            <span :class="['shrink-0 text-xs font-semibold rounded-full px-2 py-0.5', statusBadge[enrollment.status]]">
                                {{ enrollment.status }}
                            </span>
                        </div>

                        <!-- Meta info -->
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <ClockIcon class="w-3.5 h-3.5 shrink-0" />
                                <span>{{ enrollment.instructor?.name ?? 'TBA' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <AcademicCapIcon class="w-3.5 h-3.5 shrink-0" />
                                <span>
                                    {{ enrollment.section?.name ?? '' }}
                                    <template v-if="enrollment.section?.track"> · {{ enrollment.section.track.name }}</template>
                                </span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg px-2 py-1">
                                {{ enrollment.course.credit_hours }} credits
                            </span>
                            <span v-if="enrollment.status === 'enrolled'" class="text-xs text-indigo-600 dark:text-indigo-400 font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                                Open <ArrowRightIcon class="w-3 h-3" />
                            </span>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Empty state -->
            <div v-else class="text-center py-16">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                    <BookOpenIcon class="w-8 h-8 text-gray-400" />
                </div>
                <p class="text-gray-600 dark:text-gray-400 font-medium">No {{ activeTab }} courses found</p>
                <p v-if="search" class="text-sm text-gray-400 mt-1">Try a different search term</p>
            </div>

        </div>
    </AppLayout>
</template>
