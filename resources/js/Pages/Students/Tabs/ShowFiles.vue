<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ClipboardDocumentListIcon, EyeIcon, TrashIcon, PencilIcon } from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    documents: {
        type: Array,
        required: true,
    },
});

// Accessibility: image loading state
const imageLoaded = ref(false);
const handleImageLoad = () => {
    imageLoaded.value = true;
};

// Example permission check (replace with your actual logic)
function userCan(permission) {
    // ...implement permission logic...
    return true;
}

// Example delete handler (replace with your actual logic)
const deleteDocument = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("userDocuments.destroy", { userDocument: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "The document has been deleted.", "success");
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Student Document" />
        <h2 id="student-documents-heading" class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
            Student Documents
        </h2>

        <div class="mb-4">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-2">
                <Link
                    v-if="student && student.user_id"
                    :href="route('userDocuments.newDocument', { user_id: student.user_id })"
                    class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    + Add Document
                </Link>
                <div v-else class="text-center text-gray-500 dark:text-gray-400 py-2">
                    <p>Unable to add documents. User ID is missing.</p>
                </div>
                <Link
                    :href="route('students.transcript', { student: student.id })"
                    class="inline-flex items-center rounded-md bg-purple-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-yellow-700 focus:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                >
                    Show Transcript
                </Link>
            </div>
        </div>

        <div v-if="documents && documents.length" class="overflow-x-auto">
            <table class="min-w-full table-auto border border-gray-300 dark:border-gray-600" aria-label="Documents table">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                            Document Name
                        </th>
                        <th scope="col" class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                            Description
                        </th>
                        <th scope="col" class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                            Thumbnail
                        </th>
                        <th scope="col" class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(document, index) in documents"
                        :key="document.id"
                        :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'"
                        class="border-b border-gray-300 dark:border-gray-600"
                    >
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 border-r border-gray-300 dark:border-gray-600">
                            {{ document.title }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600">
                            {{ document.description }}
                        </td>
                        <td class="px-4 py-2 text-sm border-r border-gray-300 dark:border-gray-600">
                            <div v-if="document.file === null" class="flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <img
                                    :src="document.image"
                                    alt="Document Image Preview"
                                    class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700"
                                    @load="handleImageLoad"
                                />
                            </div>
                            <div v-else-if="document.file !== null" class="flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <ClipboardDocumentListIcon class="w-8 h-8 text-gray-500 dark:text-gray-400" aria-label="Document file icon" />
                            </div>
                            <div v-else class="text-xs text-gray-400 italic">No file</div>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 flex gap-2">
                            <Link
                                :href="route('userDocuments.show', { userDocument: document.id })"
                                class="text-blue-500 hover:text-blue-700 flex items-center gap-1"
                                aria-label="View document"
                            >
                                <EyeIcon class="w-5 h-5" />
                                <span>View</span>
                            </Link>
                            <button
                                v-if="userCan('delete-documents')"
                                @click="deleteDocument(document.id)"
                                class="text-red-500 hover:text-red-700 flex items-center gap-1"
                                aria-label="Delete document"
                            >
                                <TrashIcon class="w-5 h-5" />
                                <span>Delete</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
            <p>No documents available.</p>
        </div>
</template>