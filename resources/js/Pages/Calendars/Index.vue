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
    router.visit(route("calendars.index"), {
        only: ["semesters"],
        onFinish: () => (refreshing.value = false),
    });
};

const searchSemesters = () => {
    router.get(
        route("semesters.index"),
        { search: search.value },
        { preserveState: true }
    );
};
</script>

<template>
    <AppLayout>
        <h1
            class="mb-6 text-3xl font-bold text-center text-gray-900 dark:text-white"
        >
            {{ $t("semester.title") }}
        </h1>

        <div class="flex items-center justify-between mb-4">
            <input
                type="text"
                v-model="search"
                :placeholder="$t('semester.search')"
                class="p-2 pl-3 text-gray-900 bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                @input="searchSemesters"
            />

            <div class="flex gap-3">
                <Link
                    :href="route('semesters.closeForm')"
                    class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700"
                >
                    {{ $t("semester.close_current") }}
                </Link>

                <button
                    @click="refreshData"
                    class="flex items-center px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
                >
                    <ArrowPathIcon
                        class="w-5 h-5 mr-1"
                        :class="{ 'animate-spin': refreshing }"
                    />
                    {{ $t("semester.refresh") }}
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table
                class="min-w-full bg-white border border-gray-200 dark:bg-gray-800 dark:border-gray-700"
            >
                <thead>
                    <tr class="text-left bg-gray-100 dark:bg-gray-700">
                        <th class="p-3 text-gray-900 dark:text-white">
                            {{ $t("semester.name") }}
                        </th>
                        <th class="p-3 text-gray-900 dark:text-white">
                            {{ $t("semester.start_date") }}
                        </th>
                        <th class="p-3 text-gray-900 dark:text-white">
                            {{ $t("semester.end_date") }}
                        </th>
                        <th class="p-3 text-gray-900 dark:text-white">
                            {{ $t("semester.status") }}
                        </th>
                        <th class="p-3 text-gray-900 dark:text-white">
                            {{ $t("semester.action") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="semester in semesters.data"
                        :key="semester.id"
                        class="border-t border-gray-200 dark:border-gray-700"
                    >
                        <td class="p-3 text-gray-900 dark:text-white">
                            {{ semester.name }}
                        </td>
                        <td class="p-3 text-gray-900 dark:text-white">
                            {{ semester.start_date }}
                        </td>
                        <td class="p-3 text-gray-900 dark:text-white">
                            {{ semester.end_date }}
                        </td>
                        <td class="p-3">
                            <span
                                :class="
                                    semester.status === 'Active'
                                        ? 'text-green-600 dark:text-green-400'
                                        : 'text-gray-500 dark:text-gray-400'
                                "
                            >
                                {{
                                    semester.status === "Active"
                                        ? $t("semester.active")
                                        : $t("semester.inactive")
                                }}
                            </span>
                        </td>
                        <td class="p-3">
                            <Link
                                :href="
                                    route('semesters.show', {
                                        semester: semester.id,
                                    })
                                "
                                class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
