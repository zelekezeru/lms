<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";

defineProps({
    programs: {
        type: Object,
        required: true,
    },
});

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
        <!-- Add New program Button -->
        <Link
            :href="route('programs.create')"
            class="inline-flex items-center rounded-md border border-transparent bg-gray-800 dark:bg-gray-200 dark:text-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 mb-3"
        >
            Add New program
        </Link>

        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3">program Name</th>
                        <th scope="col" class="px-6 py-3">Language</th>
                        <th scope="col" class="px-6 py-3">Study</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="program in programs.data"
                        :key="program.id"
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                        >
                            <Link
                                :href="
                                    route('programs.show', {
                                        program: program.id,
                                    })
                                "
                                >{{ program.name }}</Link
                            >
                        </th>
                        <td class="px-6 py-4">{{ program.language }}</td>
                        <td class="px-6 py-4">{{ program.study }}</td>
                        <td class="px-6 py-4 flex space-x-2">
                            <Link
                                :href="
                                    route('programs.show', {
                                        program: program.id,
                                    })
                                "
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                            <Link
                                :href="
                                    route('programs.edit', {
                                        program: program.id,
                                    })
                                "
                                class="text-green-500 hover:text-green-700"
                            >
                                <PencilIcon class="w-5 h-5" />
                            </Link>
                            <button
                                @click="deleteprogram(program.id)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-3 flex justify-center space-x-2">
            <Link
                v-for="link in programs.meta.links"
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
