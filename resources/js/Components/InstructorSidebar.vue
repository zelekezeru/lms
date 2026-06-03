<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    HomeIcon,
    BookOpenIcon,
    Square2StackIcon,
    CalendarDaysIcon,
    PlayCircleIcon,
    UserCircleIcon,
    QuestionMarkCircleIcon,
    AcademicCapIcon,
} from "@heroicons/vue/24/outline";
import {
    HomeIcon as HomeIconSolid,
    BookOpenIcon as BookOpenIconSolid,
    Square2StackIcon as StackSolid,
    CalendarDaysIcon as CalendarSolid,
    PlayCircleIcon as PlaySolid,
    UserCircleIcon as UserSolid,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    isOpen: Boolean,
    isMobile: Boolean,
});

defineEmits(["close"]);

const navItems = [
    { label: "Home",     href: route("instructor.dashboard"),     icon: HomeIcon,         iconActive: HomeIconSolid,  match: "instructor.dashboard" },
    { label: "Courses",  href: route("instructor.courses"),       icon: BookOpenIcon,     iconActive: BookOpenIconSolid, match: "instructor.courses" },
    { label: "Sections", href: route("instructor.sections"),      icon: Square2StackIcon, iconActive: StackSolid,     match: "instructor.sections" },
    { label: "Schedule", href: route("instructor.classSchedules"), icon: CalendarDaysIcon, iconActive: CalendarSolid,  match: "instructor.classSchedules" },
    { label: "Sessions", href: route("instructor.classSessions"),  icon: PlayCircleIcon,  iconActive: PlaySolid,      match: "instructor.classSessions" },
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
    <!-- ── Desktop Sidebar ─────────────────────── -->
    <aside :class="desktopClasses">
        <Link :href="route('instructor.dashboard')" class="flex items-center gap-3 px-4 h-16 border-b border-gray-100 dark:border-gray-800 shrink-0">
            <div class="w-8 h-8 rounded-xl bg-violet-600 flex items-center justify-center shrink-0">
                <AcademicCapIcon class="w-5 h-5 text-white" />
            </div>
            <span v-if="isOpen" class="font-bold text-gray-900 dark:text-white text-sm tracking-wide truncate">
                Instructor Portal
            </span>
        </Link>

        <nav class="flex-1 py-4 overflow-y-auto">
            <ul class="space-y-1 px-2">
                <li v-for="item in navItems" :key="item.label">
                    <Link
                        :href="item.href"
                        :class="[
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all duration-150 group relative',
                            isActive(item)
                                ? 'bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white',
                        ]"
                    >
                        <component :is="isActive(item) ? item.iconActive : item.icon" class="w-5 h-5 shrink-0" />
                        <span v-if="isOpen" class="text-sm font-medium truncate">{{ item.label }}</span>
                    </Link>
                </li>
            </ul>
        </nav>

        <div class="px-2 pb-4 border-t border-gray-100 dark:border-gray-800 pt-3">
            <Link
                :href="route('profile.edit')"
                :class="[
                    'flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all duration-150',
                    route().current('profile.edit')
                        ? 'bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800',
                ]"
            >
                <UserCircleIcon class="w-5 h-5 shrink-0" />
                <span v-if="isOpen" class="text-sm font-medium">Profile</span>
            </Link>
        </div>
    </aside>

    <!-- ── Mobile Drawer ───────────────────────── -->
    <div class="md:hidden">
        <div v-if="isOpen" class="fixed inset-0 bg-black/40 z-40 backdrop-blur-sm" @click="$emit('close')" />
        <aside :class="mobileDrawerClasses">
            <div class="flex items-center justify-between h-16 px-5 border-b border-gray-100 dark:border-gray-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-violet-600 flex items-center justify-center">
                        <AcademicCapIcon class="w-5 h-5 text-white" />
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">Instructor Portal</span>
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
                                    ? 'bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800',
                            ]"
                        >
                            <component :is="isActive(item) ? item.iconActive : item.icon" class="w-5 h-5 shrink-0" />
                            <span class="font-medium">{{ item.label }}</span>
                            <span v-if="isActive(item)" class="ml-auto w-1.5 h-1.5 rounded-full bg-violet-600"></span>
                        </Link>
                    </li>
                </ul>
            </nav>

            <div class="px-3 pb-6 border-t border-gray-100 dark:border-gray-800 pt-3">
                <Link :href="route('profile.edit')" @click="$emit('close')" class="flex items-center gap-4 rounded-xl px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <UserCircleIcon class="w-5 h-5" />
                    <span class="font-medium">Profile</span>
                </Link>
            </div>
        </aside>
    </div>

    <!-- ── Mobile Bottom Nav ───────────────────── -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-30 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 safe-area-bottom">
        <div class="grid grid-cols-5 h-16">
            <Link
                v-for="item in navItems"
                :key="item.label"
                :href="item.href"
                :class="[
                    'flex flex-col items-center justify-center gap-0.5 transition-colors duration-150',
                    isActive(item) ? 'text-violet-600 dark:text-violet-400' : 'text-gray-500 dark:text-gray-500',
                ]"
            >
                <component :is="isActive(item) ? item.iconActive : item.icon" class="w-5 h-5" />
                <span class="text-[10px] font-medium">{{ item.label }}</span>
                <span v-if="isActive(item)" class="w-1 h-1 rounded-full bg-violet-600 mt-0.5"></span>
            </Link>
        </div>
    </nav>
</template>
