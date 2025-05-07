<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    courses: {
        type: Object,
        required: false,
    },
    instructors: {
        type: Object,
        required: false,
    },
});
</script>

<template>
    <div class="grid grid-cols-2 gap-4">
        <!-- Section Code -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Code</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ section.code }}
            </span>
        </div>

        <!-- Section Name -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ section.name }}
            </span>
        </div>

        <!-- Section Director -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Representative</span
            >
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ section.user?.name }}
            </span>
        </div>

        <!-- Program name -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Program name</span
            >
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ section.program.name }}
            </span>
        </div>

        <!-- Track  -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Track</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ section.track.name }}
            </span>
        </div>

        <!-- Year name -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Academic Year</span
            >
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ section.year.name }}
            </span>
        </div>

        <!-- Semester name -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Semester</span
            >
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ section.semester.name }}
            </span>
        </div>

        <!-- Edit and Delete Buttons -->

        <div class="flex justify-end col-span-2 mt-4">
            <Link
                v-if="userCan('update-sections')"
                :href="route('sections.edit', { section: section.id })"
                class="inline-flex items-center space-x-2 text-blue-500 hover:text-blue-700"
            >
                <PencilIcon class="w-5 h-5" />
                <span>Edit</span>
            </Link>
            <button
                v-if="userCan('delete-sections')"
                @click="deletesection(section.id)"
                class="ml-4 inline-flex items-center space-x-2 text-red-500 hover:text-red-700"
            >
                <TrashIcon class="w-5 h-5" />
                <span>Delete</span>
            </button>
        </div>

        <!-- Assign Courses to This section -->
    </div>
</template>
