<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    PencilSquareIcon,
    PlusCircleIcon,
} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import { formToJSON } from "axios";
import { Listbox, MultiSelect } from "primevue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import ShowDetails from "./Tabs/ShowDetails.vue";
import ShowCourses from "./Tabs/ShowCourses.vue";
import ShowStudents from "./Tabs/ShowStudents.vue";

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    courses: {
        type: Object,
        required: false,
    },
    instructors: {
        type: Object,
        required: false,
    },
});

const selectedTab = ref("details");

const tabs = [
    { key: "details", label: "Details", icon: CogIcon },
    { key: "courses", label: "Courses", icon: AcademicCapIcon },
    { key: "students", label: "Students", icon: UsersIcon },
];

const students = ref({
    data: [],
    meta: {
        current_page: 1,
        per_page: 50,
    },
});

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
                    Swal.fire(
                        "Deleted!",
                        "The section entry has been successfully deleted.",
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
        <div class="max-w-5xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ section.name }} Section
            </h1>

            <nav
                class="flex justify-center space-x-4 overflow-x-auto pb-2 mb-6 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    class="flex-shrink-0 flex items-center px-4 py-2 space-x-2 text-sm font-medium transition whitespace-nowrap"
                    :class="
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200'
                    "
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            <div
                class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
            >
                <!-- Details Panel -->
                <transition
                    mode="out-in"
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-75"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-75"
                >
                    <div :key="selectedTab">
                        <ShowDetails
                            v-if="selectedTab === 'details'"
                            :courses="courses"
                            :section="section"
                            :instructors="instructors"
                        />

                        <!-- Courses Panel -->
                        <ShowCourses
                            v-else-if="selectedTab === 'courses'"
                            :courses="courses"
                            :section="section"
                            :instructors="instructors"
                        />

                        <!-- Students Panel -->
                        <ShowStudents
                            v-else-if="selectedTab === 'students'"
                            :courses="courses"
                            :section="section"
                            :instructors="instructors"
                        />
                    </div>
                </transition>
            </div>
        </div>
    </AppLayout>
</template>
