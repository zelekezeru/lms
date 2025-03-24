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
    studyModes: {
        type: Object,
        required: true,
    },
});

const refreshing = ref(false);

const refreshData = () => {
    refreshing.value = true;
    router.flush("/studyModes", { method: "get" });

    router.visit(route("studyModes.index"), {
        only: ["studyModes"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Delete function with SweetAlert confirmation
const deletestudyMode = (id) => {
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
            router.delete(route("studyModes.destroy", { studyMode: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The studyMode has been deleted.",
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
            Study Modes
        </h1>

        <!-- Add New studyMode Button -->
        <div class="flex justify-between items-center mb-3">
            <Link
                v-if="userCan('create-studyModes')"
                :href="route('studyModes.create')"
                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 text-white dark:bg-gray-700 dark:text-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Add New studyMode
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
                        <th scope="col" class="px-6 py-3">Department(Mode)</th>
                        <th scope="col" class="px-6 py-3">Duration</th>
                        <th scope="col" class="px-6 py-3">Fees</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows
                        v-for="studyMode in studyModes.data"
                        :key="studyMode.id"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                        >
                            <Link
                                :href="
                                    route('studyModes.show', {
                                        studyMode: studyMode.id,
                                    })
                                "
                                >{{ studyMode.department.name }} ({{ studyMode.mode }})</Link
                            >
                        </th>
                        <td class="px-6 py-4">{{ studyMode.duration }}</td>
                        <td class="px-6 py-4">{{ studyMode.fees }}</td>
                        <!--
                            <td class="px-1 w-14 py-4">
                        <span
                            v-for="studyMode in program.studyModes"
                            :key="studyMode.id"
                            class="bg-yellow-700 rounded-md px-2 py-1 ml-1 text-gray-100 cursor-help"
                            :title="`Mode: ${studyMode.mode}\nProgram: ${program.name}\nDuration: ${studyMode.duration}\nFees: ${studyMode.fees}`"
                        >
                            {{ studyMode.mode }}
                        </span>
                    </td> -->
                        <td class="px-6 py-4 flex space-x-2">
                            <!-- View -->
                            <div v-if="userCan('view-studyModes')">
                                <Link
                                    prefetch="hover"
                                    cache-for="3"
                                    :href="
                                        route('studyModes.show', {
                                            studyMode: studyMode.id,
                                        })
                                    "
                                    class="text-blue-500 hover:text-blue-700"
                                >
                                    <EyeIcon class="w-5 h-5" />
                                </Link>
                            </div>
                            <!-- Edit -->
                            <div v-if="userCan('update-studyModes')">
                                <Link
                                    prefetch="hover"
                                    cache-for="3"
                                    :href="
                                        route('studyModes.edit', {
                                            studyMode: studyMode.id,
                                        })
                                    "
                                    class="text-green-500 hover:text-green-700"
                                >
                                    <PencilIcon class="w-5 h-5" />
                                </Link>
                            </div>
                            <!-- Delete -->
                            <div v-if="userCan('delete-studyModes')">
                                <button
                                    @click="deletestudyMode(studyMode.id)"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <TrashIcon class="w-5 h-5" />
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
                v-for="link in studyModes.meta.links"
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
