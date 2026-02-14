<script setup>
import { defineProps } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { EyeIcon } from "@heroicons/vue/24/outline";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/solid";

defineProps({
    sections: {
        type: Object,
        required: true,
    },
    tracks: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="overflow-x-auto">

        <!-- Card for each section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">
            <div
            v-for="track in tracks"
            :key="track.id"
            class="bg-gradient-to-br from-white via-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-700 shadow-lg rounded-xl p-6 transition-transform hover:scale-105 hover:shadow-2xl"
            >
            <!-- Track Name -->
            <div class="flex items-center justify-between">
                <Link
                    :href="route('tracks.show', { track: track.id })"
                    class="text-l font-bold text-gray-900 dark:text-gray-100 inline-flex items-center px-2 py-1 font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:underline"
                >
                    {{ track.name }} Sections
                </Link>
            </div>

            <!-- Sections List -->
            <ul class="space-y-2">
                <li>
                    <details class="group w-full rounded-md bg-gray-50 dark:bg-gray-900 p-2">
                        <summary class="flex items-center justify-between cursor-pointer list-none select-none">
                            <div class="flex items-center space-x-3">
                                <span class="text-sm font-medium text-gray-800 dark:text-yellow-200">Click here for Sections list</span>
                                <span class="text-md mr-1">{{ track.sections?.length ?? 0 }}</span>
                                
                            </div>

                            <!-- Chevron -->
                            <svg class="w-4 h-4 text-red-500 transition-transform duration-200 group-open:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>

                        <!-- Expanded sections -->
                        <ul class="mt-3 space-y-1">
                            <li
                                v-for="section in track.sections"
                                :key="section.id"
                                class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-md px-3 py-2 hover:bg-blue-50 dark:hover:bg-blue-950 transition"
                            >
                                <Link
                                    v-if="userCan('view-sections')"
                                    :href="route('sections.show', { section: section.id })"
                                    class="flex items-center justify-between w-full text-blue-500 hover:text-blue-700"
                                >
                                    <span class="font-medium text-gray-800 dark:text-gray-200 w-4/5">{{ section.name }}</span>
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 w-1/5">
                                       👥 {{ section.students?.length ?? 0 }}
                                    </span>
                                </Link>
                            </li>
                            <li v-if="!track.sections?.length" class="text-gray-400 italic">No sections available.</li>
                        </ul>
                    </details>
                </li>
                <li v-if="!track.sections?.length" class="text-gray-400 italic">No sections available.</li>
            </ul>
        </div>
        </div>
    </div>

</template>