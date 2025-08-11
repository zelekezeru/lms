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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div
            v-for="track in tracks"
            :key="track.id"
            class="bg-gradient-to-br from-white via-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-700 shadow-lg rounded-xl p-6 transition-transform hover:scale-105 hover:shadow-2xl"
            >
            <!-- Track Name -->
            <div class="flex items-center justify-between mb-2">
                <Link
                    :href="route('tracks.show', { track: track.id })"
                    class="text-l font-bold text-gray-900 dark:text-gray-100 inline-flex items-center px-2 py-1 font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:underline"
                >
                    {{ track.name }}
                </Link>
            </div>

            <!-- Track Students Count -->
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">Sections List</h4>
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mb-2">
                    👥 {{ track.students?.length ?? 0 }} students
                </span>
            </div>
            <!-- Sections List -->
            <ul class="space-y-2">
                <li

                    v-for="section in track.sections"
                    :key="section.id"
                    class="flex items-center justify-between bg-gray-50 dark:bg-gray-900 rounded-md px-3 py-2 hover:bg-blue-50 dark:hover:bg-blue-950 transition"
                >
                    <Link
                        v-if="userCan('view-sections')"
                        :href="route('sections.show', { section: section.id })"
                        class="text-blue-500 hover:text-blue-700">

                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ section.name }}</span>
                    <!-- Section students count -->
                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        👥 {{ section.students?.length ?? 0 }} students
                    </span>
                </Link>
                </li>
                <li v-if="!track.sections?.length" class="text-gray-400 italic">No sections available.</li>
            </ul>
        </div>
        </div>
    </div>

</template>