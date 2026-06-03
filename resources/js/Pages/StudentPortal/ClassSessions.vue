<script setup>
import { ref, computed } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    PlayCircleIcon,
    ClockIcon,
    UserIcon,
    MapPinIcon,
    CalendarIcon,
    ChevronRightIcon,
    XMarkIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
} from "@heroicons/vue/24/outline";
import { PlayCircleIcon as PlaySolid } from "@heroicons/vue/24/solid";

const props = defineProps({
    classSessions: Array,
});

const search = ref("");
const selectedSession = ref(null);

const statusStyle = (status) => {
    const s = status?.toLowerCase();
    if (s === "completed" || s === "done")
        return { badge: "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400", icon: CheckCircleIcon };
    if (s === "cancelled" || s === "canceled")
        return { badge: "bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400", icon: XMarkIcon };
    if (s === "upcoming" || s === "scheduled")
        return { badge: "bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400", icon: CalendarIcon };
    return { badge: "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400", icon: ExclamationCircleIcon };
};

const formatTime = (t) => {
    if (!t) return "—";
    const [h, m] = t.split(":");
    const hr = parseInt(h);
    const ampm = hr >= 12 ? "PM" : "AM";
    const hr12 = hr % 12 || 12;
    return `${hr12}:${m} ${ampm}`;
};

const filteredSessions = computed(() => {
    if (!search.value.trim()) return props.classSessions ?? [];
    const q = search.value.toLowerCase();
    return (props.classSessions ?? []).filter(
        (s) =>
            s.course?.name?.toLowerCase().includes(q) ||
            s.instructor?.name?.toLowerCase().includes(q) ||
            s.status?.toLowerCase().includes(q)
    );
});

const openSession = (session) => { selectedSession.value = session; };
const closeSession = () => { selectedSession.value = null; };
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-3xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Header -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                    <PlayCircleIcon class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Class Sessions</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ (classSessions ?? []).length }} total sessions</p>
                </div>
            </div>

            <!-- Search -->
            <div class="relative">
                <PlaySolid class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search by course or instructor…"
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                />
            </div>

            <!-- Session cards -->
            <div v-if="filteredSessions.length" class="space-y-3">
                <button
                    v-for="session in filteredSessions"
                    :key="session.id"
                    @click="openSession(session)"
                    class="w-full text-left bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition group overflow-hidden"
                >
                    <div class="h-1 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                    <div class="p-4 flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center shrink-0 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/40 transition">
                            <PlayCircleIcon class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1">
                                    {{ session.course?.name ?? 'Unknown course' }}
                                </h3>
                                <span :class="['text-xs font-semibold rounded-full px-2 py-0.5 shrink-0', statusStyle(session.status).badge]">
                                    {{ session.status }}
                                </span>
                            </div>
                            <div class="mt-1.5 flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <CalendarIcon class="w-3.5 h-3.5" />
                                    {{ session.date ?? '—' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <ClockIcon class="w-3.5 h-3.5" />
                                    {{ formatTime(session.startTime) }}
                                </span>
                                <span v-if="session.instructor" class="flex items-center gap-1">
                                    <UserIcon class="w-3.5 h-3.5" />
                                    {{ session.instructor.name }}
                                </span>
                            </div>
                        </div>
                        <ChevronRightIcon class="w-4 h-4 text-gray-300 dark:text-gray-600 shrink-0 group-hover:text-indigo-500 transition" />
                    </div>
                </button>
            </div>

            <!-- Empty state -->
            <div v-else class="text-center py-14">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                    <PlayCircleIcon class="w-7 h-7 text-gray-400" />
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">No sessions found</p>
            </div>
        </div>

        <!-- ── Session detail bottom sheet (mobile) / modal ── -->
        <Teleport to="body">
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="selectedSession" class="fixed inset-0 z-50 flex items-end md:items-center justify-center">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeSession" />

                    <!-- Sheet / Modal -->
                    <transition
                        enter-active-class="transition duration-300 ease-out"
                        enter-from-class="translate-y-full md:translate-y-0 md:scale-95 opacity-0"
                        enter-to-class="translate-y-0 md:scale-100 opacity-100"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="translate-y-0 md:scale-100 opacity-100"
                        leave-to-class="translate-y-full md:translate-y-0 md:scale-95 opacity-0"
                    >
                        <div class="relative z-10 w-full md:max-w-lg bg-white dark:bg-gray-900 rounded-t-3xl md:rounded-2xl shadow-2xl overflow-hidden">
                            <!-- Handle (mobile) -->
                            <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mt-3 md:hidden"></div>

                            <!-- Header -->
                            <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-gray-100 dark:border-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                        <PlayCircleIcon class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-1">
                                            {{ selectedSession.course?.name }}
                                        </h3>
                                        <span :class="['text-xs font-medium rounded-full px-2 py-0.5', statusStyle(selectedSession.status).badge]">
                                            {{ selectedSession.status }}
                                        </span>
                                    </div>
                                </div>
                                <button @click="closeSession" class="p-1.5 rounded-xl text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                    <XMarkIcon class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Details -->
                            <div class="px-5 py-4 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Date</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <CalendarIcon class="w-4 h-4 text-gray-400" />
                                        {{ selectedSession.date ?? '—' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Time</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <ClockIcon class="w-4 h-4 text-gray-400" />
                                        {{ formatTime(selectedSession.startTime) }} — {{ formatTime(selectedSession.endTime) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Instructor</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <UserIcon class="w-4 h-4 text-gray-400" />
                                        {{ selectedSession.instructor?.name ?? 'TBA' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">Room</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <MapPinIcon class="w-4 h-4 text-gray-400" />
                                        {{ selectedSession.room?.name ?? 'TBD' }}
                                    </p>
                                </div>
                                <div v-if="selectedSession.classAbout" class="col-span-2">
                                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-0.5">About</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ selectedSession.classAbout }}</p>
                                </div>
                            </div>

                            <div class="px-5 pb-6">
                                <button
                                    @click="closeSession"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl py-3 text-sm font-semibold transition"
                                >
                                    Done
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </transition>
        </Teleport>
    </AppLayout>
</template>
