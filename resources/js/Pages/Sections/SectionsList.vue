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
});
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 shadow-lg rounded-lg">
            <thead class="bg-gradient-to-r from-blue-100 to-blue-300 dark:from-gray-800 dark:to-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Section Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Year</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Language</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(section, index) in Object.values(sections).flat()"
                    :key="section.id"
                    :class="[
                        'hover:bg-blue-50 dark:hover:bg-gray-800 transition-colors duration-200 cursor-pointer',
                        index % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800'
                    ]"
                    @click="$router ? $router.push({ name: 'sections.show', params: { section: section.id } }) : $inertia.visit(route('sections.show', { section: section.id }))"
                >
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ index + 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        {{ section.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700 dark:text-blue-300 font-mono">
                        <span class="bg-blue-100 dark:bg-gray-700 rounded px-2 py-1">{{ section.code }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                        {{ section.year?.name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                        {{ section.program?.language ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-3">
                        <Link :href="route('sections.show', { section: section.id })" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 flex items-center gap-1" @click.stop>
                            <EyeIcon class="w-4 h-4" />
                            View
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
