<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    ArrowPathIcon,
} from "@heroicons/vue/24/solid";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";

defineProps({
    courses: {
        type: Object,
        required: true,
    },
});

const refreshing = ref(false);

const refreshData = () => {
    refreshing.value = true;
    router.flush("/courses", { method: "get" });

    router.visit(route("courses.index"), {
        only: ["courses"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Delete function with SweetAlert confirmation
const deleteCourse = (id) => {
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
            router.delete(route("courses.destroy", { course: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The course has been deleted.",
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
        <h1
            class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
        >
            Courses
        </h1>

        <!-- Add New course Button -->
        <div class="flex justify-between items-center mb-3">
            <Link
                v-if="userCan('create-courses')"
                :href="route('courses.create')"
                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 text-white dark:bg-gray-700 dark:text-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Add New course
            </Link>

            <button
                @click="refreshData"
                class="inline-flex items-center rounded-md border border-transparent bg-blue-800 text-white dark:bg-blue-700 dark:text-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                title="Refresh Data"
            >
                <ArrowPathIcon
                    class="w-5 h-5 mr-2"
                    :class="{ 'animate-spin': refreshing }"
                />
                Refresh Data
            </button>
        </div>

        <div class="overflow-x-auto shadow-md sm:rounded-lg mt-3">
            <Table>
                <TableHeader>
                    <tr>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Code</th>
                        <th scope="col" class="px-6 py-3">Department</th>
                        <th scope="col" class="px-6 py-3">Duration</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows
                        v-for="course in courses.data"
                        :key="course.id"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                        >
                            <Link
                                :href="
                                    route('courses.show', {
                                        course: course.id,
                                    })
                                "
                                >{{ course.name }}</Link
                            >
                        </th>
                        <td class="px-6 py-4">{{ course.code }}</td>
                        <td class="px-6 py-4">{{ course.department.name }}</td>
                        <td class="px-6 py-4">{{ course.duration }}</td>
                        <td class="px-6 py-4 flex space-x-2">
                            <!-- View -->
                            <div v-if="userCan('view-courses')">
                                <Link
                                    prefetch="hover"
                                    cache-for="3"
                                    :href="
                                        route('courses.show', {
                                            course: course.id,
                                        })
                                    "
                                    class="text-blue-500 hover:text-blue-700"
                                >
                                    <EyeIcon class="w-5 h-5" />
                                </Link>
                            </div>
                            <!-- Edit -->
                            <div v-if="userCan('update-courses')">
                                <Link
                                    prefetch="hover"
                                    cache-for="3"
                                    :href="
                                        route('courses.edit', {
                                            course: course.id,
                                        })
                                    "
                                    class="text-green-500 hover:text-green-700"
                                >
                                    <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                                </Link>
                            </div>
                            <!-- Delete -->
                            <div v-if="userCan('delete-courses')">
                                <button
                                    @click="deleteCourse(course.id)"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
                                </button>
                            </div>
                        </td>
                    </TableZebraRows>
                </tbody>
            </Table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-3 flex justify-center space-x-2">
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
