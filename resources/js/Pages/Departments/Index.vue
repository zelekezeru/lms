<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { PencilIcon, EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";

defineProps({
    departments: {
        type: Object,
        required: true,
    },
});

// Delete function with SweetAlert confirmation
const deleteDepartment = (id) => {
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
            router.delete(route("departments.destroy", { department: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "The department has been deleted.", "success");
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <!-- Add New Department Button -->
        <Link
            :href="route('departments.create')"
            class="inline-flex items-center rounded-md border border-transparent bg-gray-800 dark:bg-gray-200 dark:text-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 mb-3"
        >
            Add New Department
        </Link>

        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Department Name</th>
                        <th scope="col" class="px-6 py-3">Code</th>
                        <th scope="col" class="px-6 py-3">Description</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="department in departments.data"
                        :key="department.id"
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200"
                    >
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <Link :href="route('departments.show', { department: department.id })">{{ department.name }}</Link>
                        </th>
                        <td class="px-6 py-4">{{ department.code }}</td>
                        <td class="px-6 py-4">{{ department.description }}</td>
                        <td class="px-6 py-4 flex space-x-2">
                            <Link prefetch="hover" cache-for="3"  :href="route('departments.show', { department: department.id })" class="text-blue-500 hover:text-blue-700">
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                            <Link prefetch="hover" cache-for="3" :href="route('departments.edit', { department: department.id })" class="text-green-500 hover:text-green-700">
                                <PencilIcon class="w-5 h-5" />
                            </Link>
                            <button @click="deleteDepartment(department.id)" class="text-red-500 hover:text-red-700">
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
                v-for="link in departments.meta.links"
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
