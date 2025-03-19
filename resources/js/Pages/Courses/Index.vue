<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ref } from "vue";
import {
    EyeIcon,
    TrashIcon,
    ArrowPathIcon,
    PencilSquareIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    courses: {
        type: Object,
        required: true,
        default: () => ({ data: [], meta: { links: [] } }), // Ensure meta.links is always an array
    },
});

// State to track loading status
const refreshing = ref(false);

// Refresh data function
const refreshData = () => {
    refreshing.value = true;
    router.get(
        route("courses.index"),
        {},
        {
            only: ["courses"],
            preserveScroll: true,
            onFinish: () => {
                refreshing.value = false;
            },
        }
    );
};

// Delete function with SweetAlert confirmation
const deletecourse = (id) => {
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
            router.delete(route("courses.destroy", { course: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The course has been removed.",
                        "success"
                    );
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
    <AppLayout title="courses">
        <!-- Page Title -->
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Courses
            </h1>
        </div>

        <!-- Header Toolbar -->
        <div class="flex justify-between items-center mb-3">
            <Link
                :href="route('courses.create')"
                class="inline-flex items-center rounded-md bg-gray-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest hover:bg-gray-700 focus:ring-2 focus:ring-indigo-500"
                v-if="userCan('create-courses')"
            >
                Add New course
            </Link>
            <button
                @click="refreshData"
                class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest hover:bg-blue-700 focus:ring-2 focus:ring-indigo-500"
                title="Refresh Data"
            >
                <ArrowPathIcon
                    class="w-5 h-5 mr-2"
                    :class="{ 'animate-spin': refreshing }"
                />
                Refresh Data
            </button>
        </div>

        <!-- courses Table -->
        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Specialization</th>
                        <th scope="col" class="px-6 py-3">Employment Type</th>
                        <th scope="col" class="px-6 py-3">Hire Date</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="course in courses.data"
                        :key="course.id"
                        class="odd:bg-white even:bg-gray-50 border-b dark:border-gray-700"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                        >
                            {{ course.user?.name || "N/A" }}
                        </th>
                        <td class="px-6 py-4">
                            {{ course.specialization }}
                        </td>
                        <td class="px-6 py-4">
                            {{ course.employment_type }}
                        </td>
                        <td class="px-6 py-4">{{ course.hire_date }}</td>
                        <td class="px-6 py-4">{{ course.status }}</td>
                        <td class="px-6 py-4 flex space-x-3">
                            <Link
                                v-if="userCan('update-courses')"
                                :href="
                                    route('courses.edit', {
                                        course: course.id,
                                    })
                                "
                                class="text-green-500 hover:text-green-700"
                            >
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                            <button
                                v-if="userCan('delete-courses')"
                                @click="deletecourse(course.id)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="courses.meta && courses.meta.links"
            class="mt-3 flex justify-center space-x-2"
        >
            <Link
                v-for="link in courses.meta.links"
                :key="link.label"
                :href="link.url || '#'"
                class="p-2 px-4 text-sm font-medium rounded-lg transition-colors"
                :class="{
                    'text-gray-700 dark:text-gray-400': true,
                    'cursor-not-allowed opacity-50': !link.url,
                    '!bg-gray-100 !dark:bg-gray-800': link.active,
                }"
                v-html="link.label"
            />
        </div>
    </AppLayout>
</template>