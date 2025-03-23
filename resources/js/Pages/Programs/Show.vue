<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";

// Define the props for the program
const props = defineProps({
    program: {
        type: Object,
        required: true,
    },
});

const createMode = ref(false);

const modeForm = useForm({
    'program_id': props.program.id,
    'mode': '',
    'duration': '',
    'fees': '', 
    'schedule': '', 
})

// Delete function with SweetAlert confirmation
const deleteprogram = (id) => {
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
            router.delete(route("programs.destroy", { program: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The program has been deleted.",
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
                {{ program.name }} Program
            </h1>

            <div
                class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
            >
                <div class="grid grid-cols-2 gap-4">
                    <!-- Program ID -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >ID</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.code }}
                        </span>
                    </div>

                    <!-- Program Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.name }}
                        </span>
                    </div>

                    <!-- Program Language -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Language</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.language }}
                        </span>
                    </div>

                    <!-- Description -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Description</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.description }}
                        </span>
                    </div>

                    <!-- Program Director -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Program Director</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.user.name }}
                        </span>
                    </div>
                </div>

                <!-- Departments -->
                <div class="mt-10">
                    <div class="text-center">
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >Departments</span
                        >
                    </div>
                    <table class="mt-2 w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b dark:border-gray-700 p-2">
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Code
                                </th>
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Department Name
                                </th>
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Duration
                                </th>
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="department in program.departments"
                                :key="department.id"
                                class="border-b dark:border-gray-700 p-2 cursor-pointer"
                            >
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ department.code }}
                                </td>
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ department.name }}
                                </td>
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ department.duration }}
                                </td>
                                <td>
                                    <div v-if="userCan('view-departments')">
                                        <Link
                                            prefetch="hover"
                                            cache-for="3"
                                            :href="
                                                route('departments.show', {
                                                    department: department.id,
                                                })
                                            "
                                            class="text-blue-500 hover:text-blue-700"
                                        >
                                            <EyeIcon class="w-5 h-5" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Study Modes -->
                <div class="mt-6">
                    <div class="text-center">
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                            >Study Modes</span
                        >
                        <PlusCircleIcon
                            class="w-8"
                            @click="createMode = !createMode"
                        />
                    </div>
                    <table class="mt-2 w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b dark:border-gray-700 p-2">
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Mode
                                </th>
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Duration
                                </th>
                                <th
                                    class="text-md font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Fees
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="mode in program.studyModes"
                                :key="mode.id"
                                class="border-b dark:border-gray-700 p-2"
                            >
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ mode.mode }}
                                </td>
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ mode.duration }}
                                </td>
                                <td
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ mode.fees }}
                                </td>
                            </tr>
                            <transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="opacity-0 -translate-y-2"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition duration-200 ease-in"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 -translate-y-2"
                            >
                                <tr
                                    v-if="createMode"
                                    class="border-b dark:border-gray-700"
                                >
                                    <td>
                                        <input
                                            v-model="modeForm.mode"
                                            type="text"
                                            placeholder="Mode"
                                            class="w-full px-2 py-1 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                        />
                                    </td>
                                    <td>
                                        <input
                                            v-model="modeForm.duration"
                                            type="text"
                                            placeholder="Duration"
                                            class="w-full px-2 py-1 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                        />
                                    </td>
                                    <td>
                                        <input
                                            v-model="modeForm.fees"
                                            type="number"
                                            placeholder="Fees"
                                            class="w-full px-2 py-1 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                        />
                                    </td>
                                    <td>
                                        <button
                                            class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600"
                                            @click="addMode"
                                        >
                                            Save
                                        </button>
                                    </td>
                                </tr>
                            </transition>
                        </tbody>
                    </table>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-2">
                    <Link
                        v-if="userCan('update-programs')"
                        :href="route('programs.edit', { program: program.id })"
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                    </Link>
                    <button
                        v-if="userCan('delete-programs')"
                        @click="deleteprogram(program.id)"
                        class="text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
