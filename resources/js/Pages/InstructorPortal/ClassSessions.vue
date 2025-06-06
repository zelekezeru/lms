<script setup>
import { ref } from "vue";
import { ListBulletIcon, ViewColumnsIcon } from "@heroicons/vue/24/outline";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    instructor: Object,
    activeSemester: Object,
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
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                    Your Class Sessions - {{ activeSemester.year.name }}
                    {{ activeSemester.level }}
                </h1>
                <button
                    @click="toggleViewMode"
                    class="flex items-center px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-sm text-gray-900 dark:text-white"
                >
                    <component
                        :is="
                            viewMode === 'card'
                                ? ListBulletIcon
                                : ViewColumnsIcon
                        "
                        class="w-4 h-4 mr-2"
                    />
                    {{ viewMode === "card" ? "List View" : "Card View" }}
                </button>
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
                    <!-- Details Panel -->
                    <div
                        v-if="showDetail"
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow"
                    >
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
                        >
                            Session Details
                        </h2>
                        <div
                            class="grid sm:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300"
                        >
                            <p>
                                <strong>Course:</strong>
                                {{ selectedSession.course.name }}
                            </p>
                            <p>
                                <strong>Status:</strong>
                                {{ selectedSession.status }}
                            </p>
                            <p>
                                <strong>Date:</strong>
                                {{ selectedSession.date }}
                            </p>
                            <p>
                                <strong>Time:</strong>
                                {{ selectedSession.startTime }} -
                                {{ selectedSession.endTime }}
                            </p>
                            <p>
                                <strong>Instructor:</strong>
                                {{ selectedSession.instructor?.name || "TBA" }}
                            </p>
                            <p>
                                <strong>Room:</strong>
                                {{ selectedSession.room?.name || "TBD" }}
                            </p>
                            <p>
                                <strong>About:</strong>
                                {{ selectedSession.classAbout || "N/A" }}
                            </p>
                        </div>
                        <div class="mt-6">
                            <PrimaryButton @click="backToList"
                                >Back</PrimaryButton
                            >
                        </div>
                    </div>

                    <!-- List/Table/Card View -->
                    <div v-else>
                        <!-- Table View -->
                        <div
                            v-if="viewMode === 'table'"
                            class="overflow-x-auto rounded-lg shadow border border-gray-200 dark:border-gray-600"
                        >
                            <table class="w-full text-sm text-left">
                                <thead
                                    class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200"
                                >
                                    <tr>
                                        <th class="px-4 py-2">Course</th>
                                        <th class="px-4 py-2">Status</th>
                                        <th class="px-4 py-2">Date @ Time</th>
                                        <th class="px-4 py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="session in instructor.classSessions"
                                        :key="session.id"
                                        class="border-b dark:border-gray-700"
                                    >
                                        <td class="px-4 py-3">
                                            {{ session.course.name }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 rounded-full bg-yellow-400 text-gray-700 text-xs font-medium"
                                            >
                                                {{ session.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ session.date }} @{{
                                                session.startTime
                                            }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <PrimaryButton
                                                @click="viewDetails(session)"
                                                >Details</PrimaryButton
                                            >
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Card View -->
                        <div
                            v-else
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            <div
                                v-for="session in instructor.classSessions"
                                :key="session.id"
                                class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow border border-gray-200 dark:border-gray-600"
                            >
                                <h3
                                    class="text-lg font-semibold text-gray-800 dark:text-white mb-1"
                                >
                                    {{ session.course.name }}
                                </h3>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-300"
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
                                    <strong>Date:</strong>
                                    {{ session.date }} @{{ session.startTime }}
                                </p>
                                <PrimaryButton
                                    class="w-full"
                                    @click="viewDetails(session)"
                                >
                                    View Details
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
