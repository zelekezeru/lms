<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ref } from "vue";
import { PencilSquareIcon, TrashIcon, ArrowPathIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    documents: {
        type: Object,
        required: true,
        default: () => ({ data: [], meta: { links: [] } }), // Ensure pagination links are always available
    },
});

// State to track loading status
const refreshing = ref(false);

// Refresh data
const refreshData = () => {
    refreshing.value = true;
    router.get(
        route("academic-documents.index"),
        {},
        {
            only: ["documents"],
            preserveScroll: true,
            onFinish: () => {
                refreshing.value = false;
            },
        }
    );
};

// Delete with confirmation
const deleteDocument = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("academic-documents.destroy", { document: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "The document has been removed.", "success");
                },
                onError: (errors) => {
                    Swal.fire("Error!", "Something went wrong!", "error");
                    console.error(errors);
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout title="Academic Documents">
        <!-- Page Title -->
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Academic Documents
            </h1>
        </div>

        <!-- Header Toolbar -->
        <div class="flex justify-between items-center mb-3">
            <Link :href="route('academic-documents.create')" class="inline-flex items-center rounded-md bg-gray-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest hover:bg-gray-700 focus:ring-2 focus:ring-indigo-500">
                Add New Document
            </Link>
            <button @click="refreshData" class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest hover:bg-blue-700 focus:ring-2 focus:ring-indigo-500" title="Refresh Data">
                <ArrowPathIcon class="w-5 h-5 mr-2" :class="{ 'animate-spin': refreshing }" />
                Refresh Data
            </button>
        </div>

        <!-- Documents Table -->
        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Title</th>
                        <th scope="col" class="px-6 py-3">Description</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="document in documents.data" :key="document.id" class="odd:bg-white even:bg-gray-50 border-b dark:border-gray-700">
                        <td class="px-6 py-4">{{ document.title }}</td>
                        <td class="px-6 py-4">{{ document.description }}</td>
                        <td class="px-6 py-4 flex space-x-3">
                            <Link :href="route('academic-documents.edit', { document: document.id })" class="text-green-500 hover:text-green-700">
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                            <button @click="deleteDocument(document.id)" class="text-red-500 hover:text-red-700">
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
