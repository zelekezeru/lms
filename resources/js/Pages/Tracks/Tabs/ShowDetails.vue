<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, MultiSelect } from "primevue";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    PencilSquareIcon,
    PlusCircleIcon,
    BookOpenIcon,
    HomeModernIcon,
} from "@heroicons/vue/24/solid";
// Define the props for the track
const props = defineProps({
    track: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deleteTrack = (id) => {
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
            router.delete(route("tracks.destroy", { track: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The track has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <div
        class="p-6 bg-white border border-gray-200 shadow-lg dark:bg-gray-800 rounded-xl dark:border-gray-700"
    >
        <div class="grid gap-4 sm:grid-cols-2">
            <!-- Track Code -->
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400"
                    >{{ $t('tracks.code') }}</span
                >
                <span
                    class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    {{ track.code }}
                </span>
            </div>

            <!-- Track Program -->
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400"
                    >{{ $t('tracks.program') }}</span
                >
                <span
                    class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                ><Link
                    :href="
                        route('programs.show', { program: track.program.id })
                    "
                >
                    {{ track.program.name }}
                </Link>
                </span>
            </div>

            <!-- Description -->
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400"
                    >{{ $t('tracks.description') }}</span
                >
                <span
                    class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    {{ track.description }}
                </span>
            </div>

            <!-- Duration -->
            <div>
                <span class="block text-sm text-gray-500 dark:text-gray-400"
                    >{{ $t('tracks.duration') }}</span
                >
                <span
                    class="block text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    {{ track.duration }} Years
                </span>
            </div>
        </div>

        <!-- Edit and Delete Buttons -->
        <div class="flex justify-end col-span-2 mt-4">
            <Link
                v-if="userCan('update-tracks')"
                :href="
                    route('tracks.edit', {
                        track: track.id,
                    })
                "
                class="inline-flex items-center space-x-2 text-blue-500 hover:text-blue-700"
            >
                <PencilIcon class="w-5 h-5" />
                <span>{{ $t('actions.edit') }}</span>
            </Link>
            <button
                v-if="userCan('delete-tracks')"
                @click="deleteTrack(program.id)"
                class="inline-flex items-center ml-4 space-x-2 text-red-500 hover:text-red-700"
            >
                <TrashIcon class="w-5 h-5" />
                <span>{{ $t('actions.delete') }}</span>
            </button>
        </div>
    </div>
</template>
