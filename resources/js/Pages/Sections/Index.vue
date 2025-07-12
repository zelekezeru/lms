<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { EyeIcon, TrashIcon, ArrowPathIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon ,AcademicCapIcon, ViewColumnsIcon, CalendarIcon, FingerPrintIcon} from "@heroicons/vue/24/outline";
import { ref } from "vue";

defineProps({
    sections: {
        type: Object,
        required: true,
    },
});

const refreshing = ref(false);

const refreshData = () => {
    refreshing.value = true;
    router.flush("/sections", { method: "get" });

    router.visit(route("sections.index"), {
        only: ["sections"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Delete function with SweetAlert confirmation
const deleteSection = (id) => {
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
            router.delete(route("sections.destroy", { section: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The section has been deleted.",
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
        <!-- Page Title -->
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Sections
            </h1>
        </div>

        <!-- Header Toolbar -->
        <div class="flex justify-between items-center mb-3">
            <!-- Add Section Button -->
            <Link
                v-if="userCan('create-sections')"
                :href="route('sections.create')"
                class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
            >
                + Add Section
            </Link>

            <!-- Refresh Data Button -->
            <button
                @click="refreshData"
                class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                title="Refresh Data"
            >
                <ArrowPathIcon
                    class="w-5 h-5 mr-2"
                    :class="{ 'animate-spin': refreshing }"
                />
                Refresh Data
            </button>
        </div>

        <!-- Sections Table -->
        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    v-for="section in sections"
                    :key="section.id"
                    class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4 border dark:border-gray-700"
                >
                    <Link
                        :href="route('sections.show', { section: section.id })" class="ml-1 text-blue-600" >
                        <div
                            class="mb-3 flex items-center text-gray-700 dark:text-gray-300"
                        >
                            <AcademicCapIcon class="w-5 h-5 mr-2 text-indigo-500" />
                                {{ section.program.name }}
                        </div>

                        <div
                            class="mb-3 flex items-center text-gray-700 dark:text-gray-300"
                        >
                            <ViewColumnsIcon class="w-5 h-5 mr-2 text-teal-500" />
                            <span class="font-semibold">Section:</span>
                            <span class="ml-1">{{ section.name }}</span>
                        </div>

                        <div
                            class="mb-3 flex items-center text-gray-700 dark:text-gray-300"
                        >
                            <CalendarIcon class="w-5 h-5 mr-2 text-orange-500" />
                            <span class="font-semibold">Year:</span>
                            <span class="ml-1">{{ section.year.name }}</span>
                        </div>

                        <div class="flex justify-end space-x-3 mt-4">
                            <Link
                                :href="
                                    route('sections.show', { section: section.id })
                                "
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                            <Link
                                v-if="userCan('update-sections')"
                                :href="
                                    route('sections.edit', { section: section.id })
                                "
                                class="text-green-500 hover:text-green-700"
                            >
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
