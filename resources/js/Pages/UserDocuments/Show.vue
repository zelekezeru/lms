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
    user: {
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
            router.delete(
                route("userDocuments.destroy", { userDocument: id }),
                {
                    onSuccess: () => {
                        Swal.fire(
                            "Deleted!",
                            "The document has been deleted.",
                            "success"
                        );
                    },
                }
            );
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-6xl mx-auto p-6">
            <h1
                class="text-4xl font-semibold mb-8 text-gray-900 dark:text-white text-center"
            >
                📄 Document Details
            </h1>

            <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-8 border border-gray-200 dark:border-gray-700">
                <div class="grid sm:grid-cols-2 gap-6">

                    <!-- Owner -->
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Document Owner</span>
                        <p class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                            {{ user.name }}
                        </p>
                    </div>

                    <!-- Title -->
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Title</span>
                        <p class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                            {{ userDocument.title }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Description</span>
                        <p class="text-lg text-gray-700 dark:text-gray-300 mt-1">
                            {{ userDocument.description }}
                        </p>
                    </div>
                </div>

                <!-- Uploaded File -->
                <div v-if="userDocument.file">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Uploaded File</span>
                    <div>
                        <a
                            :href="userDocument.file"
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline mt-1 block"
                        >
                            📎 View File
                        </a>
                    </div>
                </div>
                <!-- Uploaded Image -->                    
                <div v-else-if="userDocument.image">
                    
                    <span class="text-sm text-gray-500 dark:text-gray-400 mb-2 block">Uploaded Image</span>
                    <div class="flex justify-center items-center min-h-[220px] bg-gradient-to-br from-gray-100 via-white to-gray-200 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-xl shadow-inner overflow-hidden relative group">
                        <transition name="fade" mode="out-in">
                            <div v-if="!imageLoaded" key="loader" class="absolute inset-0 flex items-center justify-center bg-gray-200 dark:bg-gray-700 animate-pulse z-10">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </div>
                            <img
                                v-else
                                key="image"
                                :src="userDocument.image"
                                :alt="`Image of ` + userDocument.title"
                                @load="handleImageLoad"
                                class="rounded-xl shadow-lg object-contain transition-transform duration-300 group-hover:scale-105 group-hover:shadow-2xl"
                                style="width: 595px; height: 842px; max-width: 100%; max-height: 80vh;"
                            />
                        </transition>
                        <div class="absolute bottom-2 right-2 bg-white/80 dark:bg-gray-900/70 px-3 py-1 rounded-full text-xs text-gray-700 dark:text-gray-200 shadow backdrop-blur-sm">
                            {{ userDocument.title }}
                        </div>
                    </div>
                </div>

                <!-- No File or Image -->
                <div v-else>
                    <span class="text-sm text-gray-500 dark:text-gray-400">No file or image uploaded</span>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mt-1">
                        This document does not have any associated files or images.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <!-- Edit Button -->
                    <div v-if="userCan('update-userDocuments')">
                        <Link
                            :href="
                                route('userDocuments.edit', {
                                    userDocument: userDocument.id,
                                })
                            "
                            class="flex items-center space-x-1 text-blue-500 hover:text-blue-700"
                        >
                            <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                        </Link>
                    </div>
                    <!-- Delete Button -->
                    <div v-if="userCan('delete-userDocuments')">
                        <button
                            @click="deleteuserDocument(userDocument.id)"
                            class="flex items-center space-x-1 text-red-500 hover:text-red-700"
                        >
                            <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

