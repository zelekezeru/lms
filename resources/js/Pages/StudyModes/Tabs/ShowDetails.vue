<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    studyMode: {
        type: Object,
        required: true,
    },
    courses: {
        type: Object,
        required: false,
    },
    instructors: {
        type: Object,
        required: false,
    },
});


const deletestudyMode = (id) => {
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
            router.delete(route("studyModes.destroy", { studyMode: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The studyMode entry has been successfully deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <div class="grid grid-cols-2 gap-4">
        <!-- StudyMode Code -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Id</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ studyMode.id }}
            </span>
        </div>

        <!-- Study Mode Name -->
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ studyMode.name }}
            </span>
        </div>

        <!-- Edit and Delete Buttons -->

        <div class="flex justify-end col-span-2 mt-4">
            <Link
                v-if="userCan('update-studyModes')"
                :href="route('studyModes.edit', { studyMode: studyMode.id })"
                class="inline-flex items-center space-x-2 text-blue-500 hover:text-blue-700"
            >
                <PencilIcon class="w-5 h-5" />
                <span>Edit</span>
            </Link>
            <button
                v-if="userCan('delete-studyModes')"
                @click="deletestudyMode(studyMode.id)"
                class="ml-4 inline-flex items-center space-x-2 text-red-500 hover:text-red-700"
            >
                <TrashIcon class="w-5 h-5" />
                <span>Delete</span>
            </button>
        </div>

        <!-- Assign Courses to This studyMode -->
    </div>
</template>
