<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";

// Define the props for the role
defineProps({
    role: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deleteRole = (id) => {
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
            router.delete(route("roles.destroy", { role: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The role has been deleted.",
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
        <div class="p-6 mx-auto max-w-8xl">
            <h1
                class="mb-6 text-3xl font-semibold text-center text-gray-900 dark:text-gray-100"
            >
                {{ role.name }} {{ $t('role.role') }}
            </h1>

            <div
                class="p-6 border shadow-lg dark:bg-gray-800 rounded-xl dark:border-gray-700"
            >
                <h1 class="my-4 text-xl font-bold text-center text-gray-900 dark:text-gray-100">
                    {{ $t('role.view_permissions') }}
                </h1>

                <div class="grid grid-cols-3 gap-4 ml-16">
                    <span
                        v-for="permission in role.permissions"
                        class="text-gray-800 dark:text-gray-100"
                        :key="permission.id"
                    >
                        {{ permission.name }}
                    </span>
                </div>

                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <Link
                        :href="route('roles.edit', { role: role.id })"
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                        <span>{{ $t('role.edit') }}</span>
                    </Link>
                    <button
                        @click="deleteRole(role.id)"
                        class="text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                        <span>{{ $t('role.delete') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

