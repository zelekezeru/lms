<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Calendar from "primevue/calendar";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ListBulletIcon, ViewColumnsIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    enrollment: Object,
    activeSemester: Object,
    classSessions: Array,
});

const showForm = ref(false);
const showDetail = ref(false);
const selectedSession = ref(null);
const viewMode = ref("card"); // ✅ Default to 'card'

function toggleForm() {
    showForm.value = !showForm.value;
}

function viewDetails(session) {
    selectedSession.value = session;
    showDetail.value = true;
}

function backToList() {
    selectedSession.value = null;
    showDetail.value = false;
}

// ✅ Ensure view mode toggling resets detail view
function toggleViewMode() {
    showDetail.value = false;
    viewMode.value = viewMode.value === "card" ? "table" : "card";
}
</script>

<template>
    <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
        <!-- Header and View Mode Toggle -->
        <div
            class="flex flex-col sm:flex-row justify-between items-center gap-4"
        >
            <h1
                class="text-2xl font-bold text-gray-800 dark:text-white text-center"
            >
                Your Class Sessions for {{ activeSemester.year.name }} Semester
                - {{ activeSemester.level }}
            </h1>
            <div class="flex gap-2">
                <!-- View Mode Toggle Button -->
                <button
                    @click="toggleViewMode"
                    class="inline-flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm text-gray-800 dark:text-white rounded-lg transition"
                >
                    <component
                        :is="
                            viewMode === 'card'
                                ? ListBulletIcon
                                : ViewColumnsIcon
                        "
                        class="w-5 h-5 mr-2"
                    />
                    {{ viewMode === "card" ? "List View" : "Card View" }}
                </button>
            </div>
        </div>

        <!-- Transition: Details View or List -->
        <transition
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-75"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-75"
        >
            <div :key="`${viewMode}-${showDetail}`">
                <!-- Details View -->
                <div
                    v-if="showDetail"
                    key="details"
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow"
                >
                    <h2
                        class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
                    >
                        Class Session Overview
                    </h2>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-300"
                    >
                        <p><strong>Date:</strong> {{ selectedSession.date }}</p>
                        <p>
                            <strong>Start Time:</strong>
                            {{ selectedSession.startTime }}
                        </p>
                        <p>
                            <strong>End Time:</strong>
                            {{ selectedSession.endTime }}
                        </p>
                        <p><strong>Type:</strong> {{ selectedSession.type }}</p>
                        <p>
                            <strong>Status:</strong>
                            {{ selectedSession.status }}
                        </p>
                        <p>
                            <strong>Room:</strong>
                            {{ selectedSession.room?.name || "N/A" }}
                        </p>
                        <p>
                            <strong>About:</strong>
                            {{ selectedSession.classAbout || "N/A" }}
                        </p>
                    </div>
                    <div class="mt-6">
                        <PrimaryButton @click="backToList">Back</PrimaryButton>
                    </div>
                </div>

                <!-- List Views (Table or Cards) -->
                <div v-else key="list">
                    <!-- Table View -->
                    <div v-if="viewMode === 'table'">
                        <div
                            class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-300 dark:border-gray-600"
                        >
                            <table class="w-full min-w-[500px] table-auto">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200"
                                        >
                                            Date @Start
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200"
                                        >
                                            Type
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200"
                                        >
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            session, index
                                        ) in classSessions"
                                        :key="session.id"
                                        :class="
                                            index % 2 === 0
                                                ? 'bg-white dark:bg-gray-800'
                                                : 'bg-gray-50 dark:bg-gray-700'
                                        "
                                    >
                                        <td
                                            class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                        >
                                            {{ session.date }} @{{
                                                session.startTime
                                            }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                        >
                                            {{ session.type }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                        >
                                            <span
                                                class="px-2 py-1 rounded-full bg-yellow-400 text-xs font-medium"
                                            >
                                                {{ session.status }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                        >
                                            <PrimaryButton
                                                @click="viewDetails(session)"
                                                >Details</PrimaryButton
                                            >
                                        </td>
                                    </tr>
                                    <tr v-if="classSessions.length === 0">
                                        <td
                                            colspan="4"
                                            class="text-center py-6 text-sm text-gray-500 dark:text-gray-300"
                                        >
                                            No sessions set.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Card View -->
                    <div
                        v-if="viewMode === 'card'"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <div
                            v-for="session in classSessions"
                            :key="session.id"
                            class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-600"
                        >
                            <h3
                                class="text-lg font-semibold text-gray-800 dark:text-white mb-2"
                            >
                                {{ session.date }} @{{ session.startTime }}
                            </h3>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-300 mb-1"
                            >
                                <strong>Type:</strong> {{ session.type }}
                            </p>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-300 mb-1"
                            >
                                <strong>Status:</strong>
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full bg-yellow-400 text-xs font-semibold"
                                >
                                    {{ session.status }}
                                </span>
                            </p>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-300 mb-3"
                            >
                                <strong>Room:</strong>
                                {{ session.room?.name || "N/A" }}
                            </p>
                            <PrimaryButton
                                class="w-full"
                                @click="viewDetails(session)"
                                >View Details</PrimaryButton
                            >
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
