<script setup>
import { ref, computed } from "vue";
import { ListBulletIcon, Squares2X2Icon } from "@heroicons/vue/24/outline";

const props = defineProps({
    enrollment: { type: Object, required: true },
    student: { type: Object, required: true },
    weights: { type: Array, required: true },
});

const viewMode = ref("card");

const getPercent = (weight) => {
    if (!weight.results?.[0]) return 0;
    return Math.min(100, Math.round((weight.results[0].point * 100) / weight.point));
};

const getScore = (weight) => weight.results?.[0]?.point ?? null;

const progressColor = (pct) => {
    if (pct >= 85) return { bar: "bg-emerald-500", text: "text-emerald-600 dark:text-emerald-400", badge: "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400" };
    if (pct >= 65) return { bar: "bg-amber-400", text: "text-amber-600 dark:text-amber-400", badge: "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400" };
    return { bar: "bg-rose-500", text: "text-rose-600 dark:text-rose-400", badge: "bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400" };
};

const totalScore = computed(() => {
    return props.weights.reduce((acc, w) => {
        const s = getScore(w);
        return s !== null ? acc + s : acc;
    }, 0);
});
const totalPossible = computed(() => props.weights.reduce((acc, w) => acc + w.point, 0));
const overallPct = computed(() =>
    totalPossible.value > 0 ? Math.round((totalScore.value * 100) / totalPossible.value) : 0
);
</script>

<template>
    <div class="space-y-5">
        <!-- Overall summary card -->
        <div v-if="weights.length" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 flex items-center gap-5 shadow-sm">
            <!-- Circular progress -->
            <div class="relative w-20 h-20 shrink-0">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 40 40">
                    <circle cx="20" cy="20" r="16" fill="none" stroke-width="4" class="stroke-gray-200 dark:stroke-gray-700" />
                    <circle
                        cx="20" cy="20" r="16" fill="none" stroke-width="4"
                        stroke-linecap="round"
                        :stroke-dasharray="`${overallPct} ${100 - overallPct}`"
                        :class="overallPct >= 85 ? 'stroke-emerald-500' : overallPct >= 65 ? 'stroke-amber-400' : 'stroke-rose-500'"
                        style="stroke-dashoffset: 0; transition: stroke-dasharray 0.5s ease"
                    />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ overallPct }}%</span>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider mb-0.5">Overall Score</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ totalScore }} <span class="text-base font-normal text-gray-400">/ {{ totalPossible }}</span>
                </p>
                <p class="text-xs text-gray-500 mt-0.5">{{ weights.length }} assessment{{ weights.length !== 1 ? 's' : '' }}</p>
            </div>
        </div>

        <!-- View toggle + title -->
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Assessments</h2>
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 rounded-xl p-1">
                <button
                    @click="viewMode = 'card'"
                    :class="['p-1.5 rounded-lg transition', viewMode === 'card' ? 'bg-white dark:bg-gray-700 shadow-sm text-indigo-600 dark:text-indigo-400' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300']"
                >
                    <Squares2X2Icon class="w-4 h-4" />
                </button>
                <button
                    @click="viewMode = 'list'"
                    :class="['p-1.5 rounded-lg transition', viewMode === 'list' ? 'bg-white dark:bg-gray-700 shadow-sm text-indigo-600 dark:text-indigo-400' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300']"
                >
                    <ListBulletIcon class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- No data -->
        <div v-if="!weights.length" class="text-center py-12 text-gray-400 dark:text-gray-500">
            <Squares2X2Icon class="w-10 h-10 mx-auto mb-3 opacity-40" />
            <p class="text-sm">No assessments recorded yet</p>
        </div>

        <!-- Card View -->
        <transition mode="out-in" enter-active-class="transition duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100">
            <div :key="viewMode">
                <div v-if="viewMode === 'card'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div
                        v-for="weight in weights"
                        :key="weight.id"
                        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm space-y-3"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ weight.name }}</h3>
                                <p v-if="weight.description" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ weight.description }}</p>
                            </div>
                            <span
                                v-if="getScore(weight) !== null"
                                :class="['text-xs font-bold rounded-xl px-2.5 py-1 shrink-0', progressColor(getPercent(weight)).badge]"
                            >
                                {{ getScore(weight) }}/{{ weight.point }}
                            </span>
                            <span v-else class="text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-xl px-2.5 py-1 shrink-0">
                                Pending
                            </span>
                        </div>

                        <!-- Progress bar -->
                        <div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                <span>Progress</span>
                                <span :class="progressColor(getPercent(weight)).text">{{ getPercent(weight) }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div
                                    :class="['h-full rounded-full transition-all duration-700', progressColor(getPercent(weight)).bar]"
                                    :style="{ width: getPercent(weight) + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List View -->
                <div v-else class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                <tr>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assessment</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Score</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Max</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Progress</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="weight in weights" :key="weight.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ weight.name }}</p>
                                        <p v-if="weight.description" class="text-xs text-gray-400 mt-0.5 truncate max-w-[200px]">{{ weight.description }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span v-if="getScore(weight) !== null" :class="['font-bold', progressColor(getPercent(weight)).text]">
                                            {{ getScore(weight) }}
                                        </span>
                                        <span v-else class="text-gray-400 text-xs">—</span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400 font-medium">{{ weight.point }}</td>
                                    <td class="px-4 py-3 w-32">
                                        <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div
                                                :class="['h-full rounded-full', progressColor(getPercent(weight)).bar]"
                                                :style="{ width: getPercent(weight) + '%' }"
                                            ></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
