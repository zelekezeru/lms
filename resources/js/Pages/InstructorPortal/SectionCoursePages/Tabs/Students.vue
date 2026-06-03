<script setup>
import { ref, computed } from "vue";
import {
    UserGroupIcon,
    MagnifyingGlassIcon,
    XMarkIcon,
    IdentificationIcon,
    EnvelopeIcon,
    PhoneIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    course: { type: Object, required: true },
    section: { type: Object, required: true },
    students: { type: Array, required: true },
});

const search = ref("");
const selectedStudent = ref(null);

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return props.students;
    return props.students.filter(
        (s) =>
            `${s.firstName} ${s.middleName} ${s.lastName}`.toLowerCase().includes(q) ||
            (s.idNo ?? "").toLowerCase().includes(q)
    );
});

const initials = (s) =>
    ((s.firstName?.[0] ?? "") + (s.lastName?.[0] ?? "")).toUpperCase() || "ST";

const avatarColors = [
    "bg-violet-500", "bg-sky-500", "bg-emerald-500",
    "bg-rose-500", "bg-amber-500", "bg-indigo-500",
];
</script>

<template>
    <div class="space-y-4">
        <!-- Header row -->
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <UserGroupIcon class="w-5 h-5 text-violet-500" />
                Students
                <span class="text-sm font-normal text-gray-400">({{ students.length }})</span>
            </h2>
        </div>

        <!-- Search -->
        <div class="relative">
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
            <input
                v-model="search"
                type="text"
                placeholder="Search by name or ID…"
                class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition"
            />
        </div>

        <!-- Student list -->
        <div v-if="filtered.length" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                <li
                    v-for="(student, idx) in filtered"
                    :key="student.id"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                >
                    <!-- Avatar -->
                    <div :class="['w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0', avatarColors[idx % avatarColors.length]]">
                        {{ initials(student) }}
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">ID: {{ student.idNo }}</p>
                    </div>

                    <!-- Detail button -->
                    <button
                        @click="selectedStudent = student"
                        class="shrink-0 text-xs px-3 py-1.5 bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400 rounded-xl font-semibold hover:bg-violet-100 dark:hover:bg-violet-900/40 transition"
                    >
                        View
                    </button>
                </li>
            </ul>
        </div>

        <!-- Empty state -->
        <div v-else class="text-center py-10">
            <UserGroupIcon class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
            <p class="text-sm text-gray-400">No students found</p>
        </div>
    </div>

    <!-- ── Student detail bottom sheet ── -->
    <Teleport to="body">
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="selectedStudent" class="fixed inset-0 z-50 flex items-end md:items-center justify-center">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="selectedStudent = null" />
                <transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="translate-y-full md:translate-y-0 md:scale-95 opacity-0"
                    enter-to-class="translate-y-0 md:scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="translate-y-0 md:scale-100 opacity-100"
                    leave-to-class="translate-y-full md:scale-95 opacity-0"
                >
                    <div class="relative z-10 w-full md:max-w-md bg-white dark:bg-gray-900 rounded-t-3xl md:rounded-2xl shadow-2xl overflow-hidden">
                        <!-- Handle -->
                        <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full mx-auto mt-3 md:hidden"></div>

                        <!-- Header -->
                        <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center font-bold text-violet-600 dark:text-violet-400">
                                    {{ initials(selectedStudent) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ selectedStudent.firstName }} {{ selectedStudent.middleName }} {{ selectedStudent.lastName }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ selectedStudent.idNo }}</p>
                                </div>
                            </div>
                            <button @click="selectedStudent = null" class="p-1.5 rounded-xl text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                <XMarkIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Details -->
                        <div class="px-5 py-4 space-y-3">
                            <div v-if="selectedStudent.email" class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                                <EnvelopeIcon class="w-4 h-4 text-gray-400 shrink-0" />
                                {{ selectedStudent.email }}
                            </div>
                            <div v-if="selectedStudent.phone" class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                                <PhoneIcon class="w-4 h-4 text-gray-400 shrink-0" />
                                {{ selectedStudent.phone }}
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                                <IdentificationIcon class="w-4 h-4 text-gray-400 shrink-0" />
                                Student ID: {{ selectedStudent.idNo }}
                            </div>
                        </div>

                        <div class="px-5 pb-6">
                            <button
                                @click="selectedStudent = null"
                                class="w-full bg-violet-600 hover:bg-violet-700 text-white rounded-xl py-3 text-sm font-semibold transition"
                            >
                                Done
                            </button>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </Teleport>
</template>
