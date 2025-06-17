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
            {{ $t("semester.calender") }}
        </h1>


        <div class="overflow-x-auto">
                <p>{{ semesters.ststus === 'Active' }}</p>
            <div
                v-if="semesters && semesters.data && semesters.data.length"
                class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded shadow p-6"
            >
                <div v-for="semester in semesters.data" :key="semester.id">
                    <div
                        v-if="semester.is_active"
                        class="border-l-4 border-blue-600 pl-4 py-2 mb-4 bg-blue-50 dark:bg-blue-900"
                    >
                        <h2 class="text-xl font-semibold text-blue-700 dark:text-blue-300 mb-2">
                            {{ $t('semester.current_active') }}
                        </h2>
                        <div class="text-gray-900 dark:text-gray-100">
                            <div class="mb-1">
                                <span class="font-medium">{{ $t('semester.name') }}:</span>
                                {{ semester.name }}
                            </div>
                            <div class="mb-1">
                                <span class="font-medium">{{ $t('semester.start_date') }}:</span>
                                {{ semester.start_date }}
                            </div>
                            <div class="mb-1">
                                <span class="font-medium">{{ $t('semester.end_date') }}:</span>
                                {{ semester.end_date }}
                            </div>
                            <div class="mb-1">
                                <span class="font-medium">{{ $t('semester.status') }}:</span>
                                <span class="text-green-600 dark:text-green-400">{{ $t('semester.active') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!semesters.data.some(s => s.is_active)" class="text-center text-gray-500 dark:text-gray-400">
                    {{ $t('semester.no_active') }}
                </div>
            </div>
            <div v-else class="text-center text-gray-500 dark:text-gray-400">
                {{ $t('semester.no_data') }}
            </div>
        </div>
    </AppLayout>
</template>
