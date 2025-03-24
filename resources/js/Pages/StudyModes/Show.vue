<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Define the props for the studyMode
const props = defineProps({
    studyMode: {
        type: Object,
        required: true,
    },
});

const addMode = () => {
    modeForm.post(
        route("studyModes.store", {
            redirectTo: "studyModes.show",
            params: { studyMode: props.studyMode.id },
        }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Added!",
                    "The Study Mode you entered has been inserted succesfully.",
                    "success"
                );

                createMode.value = false;
                modeForm.mode = "";
                modeForm.duration = "";
                modeForm.fees = "";
            },
        }
    );
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
        <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
            <h1
                class="text-3xl sm:text-4xl font-bold mb-6 text-center text-gray-900 dark:text-gray-100"
            >
                {{ studyMode.department.name }} ({{ studyMode.mode }}) Study Mode
            </h1>

            <div
                class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border border-gray-200 dark:border-gray-700"
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- studyMode Mode -->
                    <div>
                        <span
                            class="block text-sm text-gray-500 dark:text-gray-400"
                            >Mode</span
                        >
                        <span
                            class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ studyMode.mode }}
                        </span>
                    </div>

                    <!-- studyMode Department -->
                    <div>
                        <span
                            class="block text-sm text-gray-500 dark:text-gray-400"
                            >Department</span
                        >
                        <span
                            class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ studyMode.department.name }}
                        </span>
                    </div>

                    <!-- Duration -->
                    <div>
                        <span
                            class="block text-sm text-gray-500 dark:text-gray-400"
                            >Duration</span
                        >
                        <span
                            class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ studyMode.duration }}
                        </span>
                    </div>

                    <!-- studyMode Fees -->
                    <div>
                        <span
                            class="block text-sm text-gray-500 dark:text-gray-400"
                            >Fees</span
                        >
                        <span
                            class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ studyMode.fees }}
                        </span>
                    </div>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-4">
                    <!-- Edit Button, only show if user has permission -->
                    <div v-if="userCan('update-studyModes')">
                        <Link
                            :href="
                                route('studyModes.edit', {
                                    studyMode: studyMode.id,
                                })
                            "
                            class="flex items-center space-x-1 text-blue-500 hover:text-blue-700"
                        >
                            <PencilIcon class="w-5 h-5" />
                            <span>Edit</span>
                        </Link>
                    </div>
                    <!-- Delete Button, only show if user has permission -->
                    <div v-if="userCan('delete-studyModes')">
                        <button
                            @click="confirmDelete(studyMode.id)"
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
