<script setup>
import { ref, computed } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import {
    Square2StackIcon,
    AcademicCapIcon,
    ClockIcon,
    ArrowRightIcon,
    MagnifyingGlassIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/outline";
import { CheckCircleIcon as CheckSolid } from "@heroicons/vue/24/solid";

const props = defineProps({
    instructor: { type: Object, required: true },
});

const search = ref("");

const cardAccents = [
    "from-violet-500 to-purple-600",
    "from-sky-500 to-blue-600",
    "from-emerald-500 to-teal-600",
    "from-rose-500 to-pink-600",
    "from-amber-500 to-orange-500",
    "from-indigo-500 to-violet-600",
];

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return props.instructor.sections ?? [];
    return (props.instructor.sections ?? []).filter(
        (s) =>
            s.name.toLowerCase().includes(q) ||
            (s.code ?? "").toLowerCase().includes(q) ||
            (s.program?.name ?? "").toLowerCase().includes(q)
    );
});
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-5xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Header -->
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">My Sections</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ (instructor.sections ?? []).length }} active section{{ (instructor.sections ?? []).length !== 1 ? 's' : '' }}
                </p>
            </div>

            <!-- Search -->
            <div class="relative">
                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search sections, programs…"
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition"
                />
            </div>

            <!-- Section grid -->
            <div v-if="filtered.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <Link
                    v-for="(section, idx) in filtered"
                    :key="section.id"
                    :href="route('instructor.sections.detail', { section: section.id })"
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-md transition group"
                >
                    <div :class="['h-1.5 bg-gradient-to-r', cardAccents[idx % cardAccents.length]]"></div>
                    <div class="p-5">
                        <!-- Title row -->
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="flex-1 min-w-0">
                                <h2 class="font-semibold text-sm text-gray-900 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-400 transition line-clamp-1">
                                    {{ section.name }}
                                </h2>
                                <p class="text-xs text-gray-400 mt-0.5">{{ section.code }}</p>
                            </div>
                            <span
                                :class="[
                                    'shrink-0 text-xs font-semibold rounded-full px-2.5 py-1',
                                    section.isCompleted
                                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                ]"
                            >
                                {{ section.isCompleted ? 'Completed' : 'In Progress' }}
                            </span>
                        </div>

                        <!-- Meta -->
                        <div class="space-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-1.5">
                                <AcademicCapIcon class="w-3.5 h-3.5 shrink-0" />
                                {{ section.program?.name ?? '—' }}
                                <span v-if="section.program?.code" class="text-gray-400">({{ section.program.code }})</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <ClockIcon class="w-3.5 h-3.5 shrink-0" />
                                {{ section.track?.name ?? '—' }}
                            </div>
                        </div>

                        <!-- Footer chips -->
                        <div class="mt-3 flex items-center justify-between">
                            <div class="flex gap-1.5">
                                <span v-if="section.yearLevel" class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg px-2 py-0.5 font-medium">
                                    Yr {{ section.yearLevel }}
                                </span>
                                <span v-if="section.semester?.level" class="text-xs bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 rounded-lg px-2 py-0.5 font-medium">
                                    Sem {{ section.semester.level }}
                                </span>
                            </div>
                            <span class="text-xs text-violet-600 dark:text-violet-400 font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                                Details <ArrowRightIcon class="w-3 h-3" />
                            </span>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Empty state -->
            <div v-else class="text-center py-16">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                    <Square2StackIcon class="w-8 h-8 text-gray-400" />
                </div>
                <p class="text-gray-600 dark:text-gray-400 font-medium">No sections found</p>
            </div>

        </div>
    </AppLayout>
</template>
