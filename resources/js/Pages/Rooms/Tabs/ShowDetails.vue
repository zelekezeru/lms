<script setup>
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/outline";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { defineProps } from "vue";

const props = defineProps({
    room: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deleteRoom = (id) => {
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
            router.delete(route("rooms.destroy", { room: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The room has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <div>
        <h1
            class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
        >
            Room Details
        </h1>

        <div
            class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700"
        >
            <div class="grid grid-cols-2 gap-4">
                <!-- room name -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >name</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ room.name }}</span
                    >
                </div>

                <!-- room Capacity -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Capacity</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ room.capacity || "N/A" }}</span
                    >
                </div>

                <!-- room Location -->
                <div class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Location</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >{{ room.location || "N/A" }}</span
                    >
                </div>

            </div>

            <!-- Edit and Delete Buttons -->
            <div class="flex justify-end mt-6 space-x-4">
                <!-- Edit Button, only show if user has permission -->
                <div v-if="userCan('update-rooms')">
                    <Link
                        :href="
                            route('rooms.edit', {
                                room: room.id,
                            })
                        "
                        class="flex items-center space-x-1 text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                        <span>Edit</span>
                    </Link>
                </div>
                <!-- Delete Button, only show if user has permission -->
                <div v-if="userCan('delete-rooms')">
                    <button
                        @click="deleteRoom(room.id)"
                        class="flex items-center space-x-1 text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
