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
            <span class="text-sm text-gray-500 dark:text-gray-400">{{$t('programs.details.code')}}</span>
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >{{ program.code }}</span
            >
        </div>
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">{{$t('programs.details.name')}}</span>
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >{{ program.name }}</span
            >
        </div>
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >{{$t('programs.details.language')}}</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >{{ program.language }}</span
            >
        </div>
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >{{$t('programs.details.description')}}</span
            >
            <span
                class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >{{ program.description }}</span
            >
        </div>
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400"
                >{{$t('programs.details.program_director')}}</span
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
                <span>{{$t('programs.details.edit')}}</span>
            </Link>
            <button
                v-if="userCan('delete-programs')"
                @click="deleteProgram(program.id)"
                class="inline-flex items-center ml-4 space-x-2 text-red-500 hover:text-red-700"
            >
                <TrashIcon class="w-5 h-5" />
                <span>{{$t('programs.details.delete')}}</span>
            </button>
        </div>
    </div>
</template>
