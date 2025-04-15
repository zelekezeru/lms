<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    ArrowPathIcon,
} from "@heroicons/vue/24/solid";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    students: {
        type: Object,
        required: true,
    },
    sortInfo: {
        type: Object,
    },
    
});

const search = ref(usePage().props.search || "");
const refreshing = ref(false);

const refreshData = () => {
    refreshing.value = true;
    router.flush("/students", { method: "get" });

    router.visit(route("students.index"), {
        only: ["students"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

const searchStudents = () => {
    router.get(
        route("students.index"),
        { search: search.value },
        { preserveState: true }
    );
};

const deleteStudent = (id) => {
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
            router.delete(route("students.destroy", id), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The student has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Students" />
    <AppLayout>
        <!-- Page Title -->
        <div class="my-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Students
            </h1>
        </div>

        <div class="flex justify-between items-center mb-3">
            <!-- Search Bar with Icon -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg
                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-4.35-4.35M9 17A8 8 0 109 1a8 8 0 000 16z"
                        />
                    </svg>
                </span>
                <input
                    type="text"
                    v-model="search"
                    placeholder="Search..."
                    class="pl-10 p-2 border rounded-lg text-gray-900 dark:text-white dark:bg-gray-700"
                    @input="searchStudents"
                />
            </div>

            <div class="flex space-x-2">
                <!-- Add Student Button -->
                <Link
                    v-if="userCan('create-students')"
                    :href="route('students.create')"
                    class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    + Add Student
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
        </div>

        <!-- Table or No Results Message -->
        <div
            v-if="students.data.length > 0"
            class="overflow-x-auto shadow-md sm:rounded-lg"
        >
            <Table>
                <TableHeader>
                    <tr>
                        <th scope="col" class="px-6 py-3">Student ID</th>
                        <th scope="col" class="px-6 py-3">Full Name</th>
                        <th scope="col" class="px-6 py-3">Program</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows
                        v-for="student in students.data"
                        :key="student.id"
                    >
                        <td class="px-6 py-4">{{ student.id_no }}</td>
                        <td class="px-6 py-4">
                            {{ student.student_name }}
                            {{ student.father_name }}
                            {{ student.grand_father_name }}
                        </td>
                        <td class="px-6 py-4">{{ student.program.name }}</td>
                        <td class="px-6 py-4 flex space-x-2">
                            <Link
                                :href="route('students.show', student.id)"
                                class="text-blue-500 hover:text-blue-700"
                                title="View"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>

                            <Link
                                v-if="userCan('update-students')"
                                :href="route('students.edit', student.id)"
                                class="text-green-500 hover:text-green-700"
                                title="Edit"
                            >
                                <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                            </Link>

                            <button
                                v-if="userCan('delete-students')"
                                @click="deleteStudent(student.id)"
                                class="text-red-500 hover:text-red-700"
                                title="Delete"
                            >
                                <TrashIcon class="w-5 h-5" />
                            <span>Delete</span>
                            </button>
                        </td>
                    </TableZebraRows>
                </tbody>
            </Table>
        </div>

        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
            <p>No search results found.</p>
        </div>

        <!-- Pagination Links -->
        <div class="mt-3 flex justify-center space-x-2">
            <Link
                v-for="link in students.links"
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
