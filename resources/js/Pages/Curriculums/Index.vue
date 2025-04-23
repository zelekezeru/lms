<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import {
    EyeIcon,
    PencilIcon,
    TrashIcon,
    PlusCircleIcon,
} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";

const sections = ref([
    // Mocked data – replace with props or fetch
    { id: 1, name: "Section A", code: "SEC101", year: "1st Year" },
    { id: 2, name: "Section B", code: "SEC102", year: "2nd Year" },
]);

const showCreateModal = ref(false);
const selectedTab = ref("all");

const tabs = [
    { key: "all", label: "All Sections", icon: EyeIcon },
    { key: "create", label: "Create New", icon: PlusCircleIcon },
];

const deletesection = (id) => {
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
            router.delete(route("sections.destroy", { section: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "Section removed successfully.", "success");
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-6xl mx-auto p-6">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800 dark:text-white">
                Section Management
            </h1>

            <!-- Tabs -->
            <nav class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    class="flex items-center px-4 py-2 space-x-2 text-sm font-medium transition"
                    :class="selectedTab === tab.key
                        ? 'border-b-2 border-indigo-500 text-indigo-600'
                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200'"
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            <!-- Tab Contents -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700">
                <transition
                    mode="out-in"
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-90"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-90"
                >
                    <div :key="selectedTab">
                        <!-- All Sections List -->
                        <div v-if="selectedTab === 'all'">
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-auto border border-gray-300 dark:border-gray-600">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-700">
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">#</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Name</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Code</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Year</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(section, index) in sections"
                                            :key="section.id"
                                            :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'"
                                            class="border-b border-gray-300 dark:border-gray-600"
                                        >
                                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ index + 1 }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ section.name }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ section.code }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ section.year }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 space-x-2">
                                                <button class="text-blue-600 hover:underline">
                                                    <PencilIcon class="w-5 h-5 inline" />
                                                </button>
                                                <button
                                                    @click="deletesection(section.id)"
                                                    class="text-red-600 hover:underline"
                                                >
                                                    <TrashIcon class="w-5 h-5 inline" />
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Create Section Modal -->
                        <div v-else-if="selectedTab === 'create'">
                            <p class="text-gray-600 dark:text-gray-300">This section will include a form to create new sections. Coming soon...</p>
                        </div>
                    </div>
                </transition>
            </div>
        </div>

        <!-- Modal Example -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Create Section</h2>
                <!-- Modal form content here -->
                <button @click="showCreateModal = false" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Close
                </button>
            </div>
        </Modal>
    </AppLayout>
</template>