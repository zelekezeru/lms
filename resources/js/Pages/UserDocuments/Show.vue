
<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

// Define props for the UserDocument
defineProps({
    userDocument: {
        type: Object,
        required: true,
    },
});

const imageLoaded = ref(false);

const handleImageLoad = () => {
    imageLoaded.value = true;
};

// Delete function with SweetAlert confirmation
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
                    Swal.fire(
                        "Deleted!",
                        "The document has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                Document Details
            </h1>

            <div
                class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
            >
                <div class="flex justify-center mb-8">
                    <div
                        v-if="!imageLoaded"
                        class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                    ></div>
                    
                    <img
                        v-show="imageLoaded"
                        class="rounded-full w-44 h-44 object-contain bg-gray-400"
                        :src="userDocument.image"
                        :alt="`Image of ` + userDocument.title"
                        @load="handleImageLoad"
                    />
                </div>
                <div class="grid sm:grid-cols-2 gap-4 lg:pl-36 sm:gap-2">
                    <!-- Document Title -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Title</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ userDocument.title }}</span
                        >
                    </div>

                    <!-- Document Description -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Description</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >{{ userDocument.description }}</span
                        >
                    </div>

                    <!-- Uploaded File -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Uploaded File</span
                        >
                        <a
                            v-if="userDocument.file"
                            :href="userDocument.file"
                            target="_blank"
                            class="text-blue-500 hover:text-blue-700"
                        >
                            View File
                        </a>
                        <span v-else class="text-gray-500">No file uploaded</span>
                    </div>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <Link
                        :href="
                            route('userDocuments.edit', { userDocument: userDocument.id })
                        "
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                    </Link>
                    <button
                        @click="deleteDocument(userDocument.id)"
                        class="text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
