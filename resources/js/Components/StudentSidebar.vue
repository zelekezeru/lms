<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    HomeIcon,
    BookOpenIcon,
    ClipboardDocumentListIcon,
    CalendarDaysIcon,
    PlayCircleIcon,
    ChartBarIcon,
    UserCircleIcon,
    QuestionMarkCircleIcon,
    AcademicCapIcon,
} from "@heroicons/vue/24/outline";
import {
    HomeIcon as HomeIconSolid,
    BookOpenIcon as BookOpenIconSolid,
    ClipboardDocumentListIcon as ClipboardSolid,
    CalendarDaysIcon as CalendarSolid,
    PlayCircleIcon as PlaySolid,
    ChartBarIcon as ChartSolid,
    UserCircleIcon as UserSolid,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    isOpen: Boolean,
    isMobile: Boolean,
});

const currentRoute = computed(() => usePage().url);

const navItems = [
    {
        label: "Home",
        href: route("student.dashboard"),
        icon: HomeIcon,
        iconActive: HomeIconSolid,
        match: "student.dashboard",
    },
    {
        label: "Courses",
        href: route("student.enrollments"),
        icon: BookOpenIcon,
        iconActive: BookOpenIconSolid,
        match: "student.enrollments",
    },
    {
        label: "Grades",
        href: route("student.grades"),
        icon: ChartBarIcon,
        iconActive: ChartSolid,
        match: "student.grades",
    },
    {
        label: "Transcript",
        href: route("student.transcripts"),
        icon: ClipboardDocumentListIcon,
        iconActive: ClipboardSolid,
        match: "student.transcripts",
    },
    {
        label: "Schedule",
        href: route("student.classSchedules"),
        icon: CalendarDaysIcon,
        iconActive: CalendarSolid,
        match: "student.classSchedules",
    },
    {
        label: "Sessions",
        href: route("student.classSessions"),
        icon: PlayCircleIcon,
        iconActive: PlaySolid,
        match: "student.classSessions",
    },
];

const isActive = (item) => route().current(item.match) || route().current(item.match + ".*");

const desktopClasses = computed(() => [
    "hidden md:flex flex-col bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 shadow-sm transition-all duration-300 h-full flex-shrink-0",
    props.isOpen ? "w-60" : "w-16",
]);

const mobileDrawerClasses = computed(() => [
    "md:hidden fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-900 shadow-2xl transition-transform duration-300 w-72",
    props.isOpen ? "translate-x-0" : "-translate-x-full",
]);
</script>

<template>
    <!-- ═══════════════════════════════════════
         DESKTOP SIDEBAR
    ═══════════════════════════════════════ -->
    <aside :class="desktopClasses">
        <!-- Brand -->
        <Link :href="route('student.dashboard')" class="flex items-center gap-3 px-4 h-16 border-b border-gray-100 dark:border-gray-800 shrink-0">
            <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center shrink-0">
                <AcademicCapIcon class="w-5 h-5 text-white" />
            </div>
            <span v-if="isOpen" class="font-bold text-gray-900 dark:text-white text-sm tracking-wide truncate">
                Student Portal
            </span>
        </Link>

        <!-- Nav -->
        <nav class="flex-1 py-4 overflow-y-auto">
            <ul class="space-y-1 px-2">
                <li v-for="item in navItems" :key="item.label">
                    <Link
                        :href="item.href"
                        :class="[
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all duration-150 group',
                            isActive(item)
                                ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                        ]"
                    >
                        <component
                            :is="isActive(item) ? item.iconActive : item.icon"
                            :class="['w-5 h-5 shrink-0', isActive(item) ? 'text-indigo-600 dark:text-indigo-400' : '']"
                        />
                        <span v-if="isOpen" class="text-sm font-medium truncate">{{ item.label }}</span>
                        <!-- Active indicator dot when collapsed -->
                        <span v-if="!isOpen && isActive(item)" class="absolute left-12 w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                    </Link>
                </li>
            </ul>
        </nav>

        <!-- Bottom -->
        <div class="px-2 pb-4 border-t border-gray-100 dark:border-gray-800 pt-3">
            <Link
                :href="route('student.profile')"
                :class="[
                    'flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all duration-150',
                    route().current('student.profile')
                        ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800',
                ]"
            >
                <UserCircleIcon class="w-5 h-5 shrink-0" />
                <span v-if="isOpen" class="text-sm font-medium">Profile</span>
            </Link>
        </div>
    </aside>

    <!-- ═══════════════════════════════════════
         MOBILE DRAWER OVERLAY
    ═══════════════════════════════════════ -->
    <div class="md:hidden">
        <!-- Backdrop -->
        <div
            v-if="isOpen"
            class="fixed inset-0 bg-black/40 z-40 backdrop-blur-sm"
            @click="$emit('close')"
        />

        <!-- Drawer -->
        <aside :class="mobileDrawerClasses">
            <div class="flex items-center justify-between h-16 px-5 border-b border-gray-100 dark:border-gray-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center">
                        <AcademicCapIcon class="w-5 h-5 text-white" />
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">Student Portal</span>
                </div>
                <button @click="$emit('close')" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-3">
                    <li v-for="item in navItems" :key="item.label">
                        <Link
                            :href="item.href"
                            @click="$emit('close')"
                            :class="[
                                'flex items-center gap-4 rounded-xl px-4 py-3 transition-all duration-150',
                                isActive(item)
                                    ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800',
                            ]"
                        >
                            <component
                                :is="isActive(item) ? item.iconActive : item.icon"
                                class="w-5 h-5 shrink-0"
                            />
                            <span class="font-medium">{{ item.label }}</span>
                            <span v-if="isActive(item)" class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                        </Link>
                    </li>
                </ul>
            </nav>

            <div class="px-3 pb-6 border-t border-gray-100 dark:border-gray-800 pt-3">
                <Link
                    :href="route('student.profile')"
                    @click="$emit('close')"
                    class="flex items-center gap-4 rounded-xl px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                >
                    <UserCircleIcon class="w-5 h-5" />
                    <span class="font-medium">Profile</span>
                </Link>
            </div>
        </aside>
    </div>

    <!-- ═══════════════════════════════════════
         MOBILE BOTTOM NAV BAR
    ═══════════════════════════════════════ -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-30 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 safe-area-bottom">
        <div class="grid grid-cols-5 h-16">
            <Link
                v-for="item in navItems.slice(0, 5)"
                :key="item.label"
                :href="item.href"
                :class="[
                    'flex flex-col items-center justify-center gap-0.5 transition-colors duration-150',
                    isActive(item)
                        ? 'text-indigo-600 dark:text-indigo-400'
                        : 'text-gray-500 dark:text-gray-500',
                ]"
            >
                <component
                    :is="isActive(item) ? item.iconActive : item.icon"
                    class="w-5 h-5"
                />
                <span class="text-[10px] font-medium">{{ item.label }}</span>
                <span v-if="isActive(item)" class="w-1 h-1 rounded-full bg-indigo-600 mt-0.5"></span>
            </Link>
        </div>
    </nav>
</template>
