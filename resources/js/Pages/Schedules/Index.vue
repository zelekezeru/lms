<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { ArrowPathIcon } from "@heroicons/vue/24/solid";
import Swal from "sweetalert2";
import { EyeIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    semesters: Object,
    search: String,
});

const refreshing = ref(false);
const search = ref(props.search || "");

const refreshData = () => {
    refreshing.value = true;
    router.visit(route("schedules.index"), {
        only: ["semesters"],
        onFinish: () => (refreshing.value = false),
    });
};

const searchSemesters = () => {
    router.get(route("semesters.index"), { search: search.value }, { preserveState: true });
};
</script>

<template>
    <AppLayout>
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-900 dark:text-white">Semester Management</h1>

        <div class="flex justify-between items-center mb-4">
            <input
                type="text"
                v-model="search"
                placeholder="Search semesters..."
                class="pl-3 p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                @input="searchSemesters"
            />

            <div class="flex gap-3">
                <Link
                    :href="route('semesters.closeForm')"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
                >
                    Close Current Semester
                </Link>

                <button
                    @click="refreshData"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center"
                >
                    <ArrowPathIcon class="w-5 h-5 mr-1" :class="{ 'animate-spin': refreshing }" />
                    Refresh
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-left">
                        <th class="p-3 text-gray-900 dark:text-white">Name</th>
                        <th class="p-3 text-gray-900 dark:text-white">Start Date</th>
                        <th class="p-3 text-gray-900 dark:text-white">End Date</th>
                        <th class="p-3 text-gray-900 dark:text-white">Status</th>
                        <th class="p-3 text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="semester in semesters.data" :key="semester.id" class="border-t border-gray-200 dark:border-gray-700">
                        <td class="p-3 text-gray-900 dark:text-white">{{ semester.name }}</td>
                        <td class="p-3 text-gray-900 dark:text-white">{{ semester.start_date }}</td>
                        <td class="p-3 text-gray-900 dark:text-white">{{ semester.end_date }}</td>
                        <td class="p-3">
                            <span :class="semester.status === 'Active' ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
                                {{ semester.status }}
                            </span>
                        </td>
                        <td class="p-3">
                            <Link :href="route('semesters.show', { semester: semester.id })" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
