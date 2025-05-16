<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { EyeIcon, PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/outline";
import { Head, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

defineProps({
    weight: Object,
});

// Confirm delete method
const deleteWeight = (id) => {
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
            router.delete(route("weights.destroy", { weight: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "The weight has been deleted.", "success");
                    router.visit(route("weights.index")); // Redirect to index after deletion
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <Head :title="`Weight: ${weight.name}`" />

        <div class="max-w-5xl mx-auto p-6">
            <!-- Page Title -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Weight Details
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Below are the details of the selected weight.
                </p>
            </div>

            <!-- Weight Details Card -->
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Name
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.name }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Weight Point
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.point }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Description
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.description || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Assigned User
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.user?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Year
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.year?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Semester
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.semester?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            Course
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ weight.course?.name || "N/A" }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-end items-center space-x-4 mt-6">
                    <Link
                        :href="route('weights.edit', { weight: weight.id })"
                        class="inline-flex items-center text-green-500 hover:text-green-700"
                    >
                        <PencilSquareIcon class="w-5 h-5 mr-1" />
                        <span>Edit</span>
                    </Link>
                    <button
                        @click="deleteWeight(weight.id)"
                        class="inline-flex items-center text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5 mr-1" />
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
