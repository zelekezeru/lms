<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";
import Swal from "sweetalert2";

const props = defineProps({
    program: { type: Object, required: true },
});

const deleteProgram = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone.",
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
                        "Program has been deleted.",
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
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">CODE</span>
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >{{ program.code }}</span
            >
        </div>
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >{{ program.name }}</span
            >
        </div>
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Language</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >{{ program.language }}</span
            >
        </div>
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Description</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >{{ program.description }}</span
            >
        </div>
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >Program Director</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >{{ program.director?.name || "N/A" }}</span
            >
        </div>
        <div class="flex justify-end col-span-2 mt-4">
            <Link
                v-if="userCan('update-programs')"
                :href="
                    route('programs.edit', {
                        program: program.id,
                    })
                "
                class="inline-flex items-center space-x-2 text-blue-500 hover:text-blue-700"
            >
                <PencilIcon class="w-5 h-5" />
                <span>Edit</span>
            </Link>
            <button
                v-if="userCan('delete-programs')"
                @click="deleteProgram(program.id)"
                class="ml-4 inline-flex items-center space-x-2 text-red-500 hover:text-red-700"
            >
                <TrashIcon class="w-5 h-5" />
                <span>Delete</span>
            </button>
        </div>
    </div>
</template>
