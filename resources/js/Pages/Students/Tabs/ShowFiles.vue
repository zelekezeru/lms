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
    <Head title="Student Documents" />
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700/60">
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">
                    Verification Documents
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Manage uploaded forms, ID scans, and certifications.
                </p>
            </div>
            
            <Link
                v-if="student && student.user_id"
                :href="route('userDocuments.newDocument', { user_id: student.user_id })"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition active:scale-95"
            >
                + Add Document
            </Link>
        </div>

        <!-- Files Card Grid -->
        <div v-if="documents && documents.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="document in documents"
                :key="document.id"
                class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md transition duration-300 relative group"
            >
                <div class="space-y-4">
                    <!-- File Preview Icon or Image -->
                    <div class="aspect-video w-full bg-gray-50 dark:bg-gray-900/40 rounded-xl overflow-hidden flex items-center justify-center border border-gray-105 dark:border-gray-750">
                        <img
                            v-if="document.file === null && document.image"
                            :src="document.image"
                            alt="Preview"
                            class="w-full h-full object-cover group-hover:scale-[1.02] transition duration-300 animate-fade-in"
                            @load="handleImageLoad"
                        />
                        <div v-else class="flex flex-col items-center gap-2 text-gray-405 dark:text-gray-500">
                            <ClipboardDocumentListIcon class="w-12 h-12" />
                            <span class="text-[10px] font-semibold uppercase tracking-wider">File Attachment</span>
                        </div>
                    </div>

                    <!-- File Details -->
                    <div class="space-y-1">
                        <h3 class="font-bold text-gray-950 dark:text-gray-50 text-sm truncate">
                            {{ document.title }}
                        </h3>
                        <p class="text-xs text-gray-555 dark:text-gray-400 line-clamp-2">
                            {{ document.description || 'No description provided.' }}
                        </p>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-5 pt-3 border-t border-gray-50 dark:border-gray-700/60 flex items-center justify-between">
                    <Link
                        :href="route('userDocuments.show', { userDocument: document.id })"
                        class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800"
                    >
                        <EyeIcon class="w-4 h-4" />
                        <span>View Details</span>
                    </Link>
                    
                    <button
                        v-if="userCan('delete-documents')"
                        @click="deleteDocument(document.id)"
                        class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-450 hover:text-rose-800"
                    >
                        <TrashIcon class="w-4 h-4" />
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-gray-50 dark:bg-gray-700/20 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-12 text-center">
            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-850 flex items-center justify-center mx-auto text-gray-400 mb-3">
                <ClipboardDocumentListIcon class="w-6 h-6" />
            </div>
            <p class="text-sm text-gray-400 dark:text-gray-550 font-medium">No documents uploaded yet.</p>
        </div>
    </div>
</template>