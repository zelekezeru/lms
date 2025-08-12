<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { useForm } from "@inertiajs/vue3";
import {
    ArrowPathIcon,
    TrashIcon,
    PlusIcon,
    PencilIcon,
} from "@heroicons/vue/24/solid";
import ShowSemesters from "./ShowSemesters.vue";
import Form from "../Semesters/Form.vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
    year: {
        type: Object,
        required: true,
    },
    studyModes: Array,
});

const { year } = props;

const showSemesterForm = ref(false);

const selectedSemester = ref(null);

// Refresh
const refreshing = ref(false);
const refreshData = () => {
    refreshing.value = true;
    router.visit(route("years.show", year.id), {
        only: ["year"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};
// Toggle create form
const toggleSemesterForm = () => {
    showSemesterForm.value = !showSemesterForm.value;

    form.reset();
};

const form = useForm({
    name: "",
    year_id: year.id,
    level: "",
    start_date: "",
    end_date: "",
    study_modes: {},
});

const submit = () => {
    form.post(route("semesters.store"), {
        onSuccess: () => {
            Swal.fire("Success!", "The semester has been created.", "success");
        },
    });
};

// Delete year
const deleteYear = (id) => {
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
            router.delete(route("years.destroy", { year: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The Year has been deleted.",
                        "success"
                    );
                },
                onError: () => {
                    Swal.fire(
                        "Error!",
                        "There was a problem deleting the Year.",
                        "error"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-semibold mb-6 text-center">
                {{ year.name }} -
                {{ $t("year.details_title", { year: year.name }) }}
            </h1>

            <!-- Year Details -->
            <div
                class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border dark:border-gray-700"
            >
                <div
                    class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 mb-6 border border-gray-200 dark:border-gray-700"
                >
                    <h2
                        class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center space-x-2"
                    >
                        <CalendarIcon class="w-5 h-5 text-blue-500" />
                        <span>{{
                            $t("year.details_title", { year: year.name })
                        }}</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-300"
                                >{{ $t("year.name") }}</span
                            >
                            <div
                                class="text-lg font-semibold text-gray-800 dark:text-white"
                            >
                                {{ year.name }}
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-300"
                                >{{ $t("year.approval") }}</span
                            >
                            <div
                                :class="
                                    year.is_approved
                                        ? 'text-green-600 dark:text-green-400'
                                        : 'text-red-600 dark:text-red-400'
                                "
                                class="text-lg font-semibold"
                            >
                                {{
                                    year.is_approved
                                        ? $t("year.approved")
                                        : $t("year.not_approved")
                                }}
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <span
                                class="text-sm text-gray-500 dark:text-gray-300"
                                >{{ $t("year.status") }}</span
                            >
                            <div
                                :class="
                                    year.status === 'Active'
                                        ? 'text-green-600 dark:text-green-400'
                                        : 'text-red-600 dark:text-red-400'
                                "
                                class="text-lg font-semibold"
                            >
                                <span
                                    v-if="year.status === 'Active'"
                                    class="text-green-600 dark:text-green-400"
                                >
                                    {{ $t("status.active") }}
                                </span>
                                <span
                                    v-else
                                    class="text-red-600 dark:text-red-400"
                                >
                                    {{ $t("status.inactive", "Inactive") }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Edit/Delete Year Buttons -->
                    <div class="flex justify-end mt-6 space-x-4">
                        <div v-if="userCan('update-years')">
                            <Link
                                :href="route('years.edit', { year: year.id })"
                                class="inline-flex items-center space-x-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline"
                            >
                                <PencilIcon class="w-5 h-5" />
                                <span>{{ $t("common.edit") }}</span>
                            </Link>
                        </div>
                        <div v-if="userCan('delete-years')">
                            <button
                                @click="deleteYear(year.id)"
                                class="inline-flex items-center space-x-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:underline"
                            >
                                <TrashIcon class="w-5 h-5" />
                                <span>{{ $t("common.delete") }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Toggle & Refresh -->
                <div class="mt-6 flex space-x-6">
                    <button
                        @click="refreshData"
                        class="bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase hover:bg-blue-700 flex items-center"
                    >
                        <ArrowPathIcon
                            class="w-5 h-5 mr-2"
                            :class="{ 'animate-spin': refreshing }"
                        />
                        {{ $t("common.refresh") }}
                    </button>
                </div>

                <!-- Semester Form -->

                <ShowSemesters class="mb-5" :year="year" />

                <button
                    @click="toggleSemesterForm"
                    class="bg-green-600 rounded mb-5 text-white px-4 py-2 text-xs font-semibold uppercase hover:bg-green-700 flex items-center"
                >
                    <component
                        :is="showSemesterForm ? XMarkIcon : PlusIcon"
                        class="w-5 h-5 mr-2"
                    />
                    {{
                        showSemesterForm
                            ? $t("common.cancel")
                            : $t("semester.add", "Add Semester")
                    }}
                </button>
                <transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="-translate-y-2 opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="translate-y-0 opacity-100"
                    leave-to-class="-translate-y-2 opacity-0"
                >
                    <div
                        class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
                        v-if="showSemesterForm"
                    >
                        <h1 class="font-bold text-3xl mb-5">
                            Add new Semester
                        </h1>
                        <Form
                            :form="form"
                            @submit="submit"
                            :hideYears="true"
                            :study-modes="studyModes"
                        />
                    </div>
                </transition>
            </div>
        </div>
    </AppLayout>
</template>
