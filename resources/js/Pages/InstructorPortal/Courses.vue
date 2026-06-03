<script setup>
import { ref, computed } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import {
    BookOpenIcon,
    UserGroupIcon,
    ClockIcon,
    ArrowRightIcon,
    MagnifyingGlassIcon,
    Square2StackIcon,
} from "@heroicons/vue/24/outline";

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
    if (!q) return props.instructor.courses ?? [];
    return (props.instructor.courses ?? []).filter(
        (c) =>
            c.name.toLowerCase().includes(q) ||
            (c.code ?? "").toLowerCase().includes(q)
    );
});
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-5xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Header -->
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">My Courses</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ (instructor.courses ?? []).length }} course{{ (instructor.courses ?? []).length !== 1 ? 's' : '' }} this semester
                </p>
            </div>

            <!-- Search -->
            <div class="relative">
                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search courses…"
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition"
                />
            </div>

            <!-- Course grid -->
            <div v-if="filtered.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <Link
                    v-for="(course, idx) in filtered"
                    :key="course.id"
                    :href="route('instructor.courses.detail', { course: course.id })"
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm hover:shadow-md transition group"
                >
                    <div :class="['h-1.5 bg-gradient-to-r', cardAccents[idx % cardAccents.length]]"></div>
                    <div class="p-5">
                        <!-- Icon + code -->
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white text-sm font-bold bg-gradient-to-br', cardAccents[idx % cardAccents.length]]">
                                {{ (course.code ?? course.name).slice(0, 2).toUpperCase() }}
                            </div>
                            <span class="text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full px-2.5 py-1">
                                {{ course.code ?? '—' }}
                            </span>
                        </div>

                        <!-- Name -->
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-400 transition line-clamp-2 mb-3">
                            {{ course.name }}
                        </h2>

                        <!-- Meta -->
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <Square2StackIcon class="w-3.5 h-3.5 shrink-0" />
                                {{ course.sectionsCount ?? 0 }} section{{ (course.sectionsCount ?? 0) !== 1 ? 's' : '' }}
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <UserGroupIcon class="w-3.5 h-3.5 shrink-0" />
                                {{ course.enrolled ?? '—' }} students enrolled
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 flex items-center justify-end">
                            <span class="text-xs text-violet-600 dark:text-violet-400 font-medium flex items-center gap-1 group-hover:gap-2 transition-all">
                                View course <ArrowRightIcon class="w-3 h-3" />
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
                <p class="text-gray-600 dark:text-gray-400 font-medium">No courses found</p>
                <p v-if="search" class="text-sm text-gray-400 mt-1">Try a different search term</p>
            </div>

        </div>
    </AppLayout>
</template>
