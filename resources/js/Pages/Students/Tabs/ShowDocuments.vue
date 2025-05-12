<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import { ClipboardDocumentListIcon, EyeIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    documents: {
        type: Array,
        required: true,
    },
    student: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="p-6 bg-white dark:bg-gray-800 shadow rounded-lg border dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
            Student Documents
        </h2>
        <Link
            v-if="student && student.user_id"
            :href="route('userDocuments.newDocument', { user_id: student.user_id })"
            class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 my-4 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        >
            + Add Document
        </Link>
        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
            <p>Unable to add documents. User ID is missing.</p>
        </div>
        <div v-if="documents" class="overflow-x-auto">
            <table class="min-w-full table-auto border border-gray-300 dark:border-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                            Document Name
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                            Description
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                            Tumbnail
                        </th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(document, index) in documents"
                        :key="index"
                        :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'"
                        class="border-b border-gray-300 dark:border-gray-600"
                    >
                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                            {{ document.title }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                            {{ document.description }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                            <div v-if="document.file === null" class="flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <img
                                :src="document.image"
                                alt="Document Image Preview"
                                class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700"
                                />
                            </div>

                            <div v-else-if="document.file !==null " class="flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <ClipboardDocumentListIcon class="w-8 h-8 text-gray-500 dark:text-gray-400" />
                            </div>

                            <div v-else class="text-xs text-gray-400 italic">No file</div>
                        </td>

                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300">
                            <Link :href="route('userDocuments.show', { userDocument: document.id })" class="text-blue-500 hover:text-blue-700">
                                <span class="inline-flex items-center gap-1">
                                    <EyeIcon class="w-5 h-5" />
                                    View
                                </span>
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
            <p>No documents available.</p>
        </div>
    </div>
</template>
