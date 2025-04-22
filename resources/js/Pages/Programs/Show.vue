<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, EyeIcon } from "@heroicons/vue/24/solid";
import { CogIcon } from "@heroicons/vue/24/outline";
import ShowDetails from "./Tabs/ShowDetails.vue";
import ShowDepartments from "./Tabs/ShowDepartments.vue";
import ShowStudyModes from "./Tabs/ShowStudyModes.vue";

const props = defineProps({
    program: { type: Object, required: true },
    users: { type: Array, required: false },
});

const tabs = [
    { key: "details", label: "Details", icon: CogIcon },
    { key: "departments", label: "Departments", icon: PencilIcon },
    { key: "modes", label: "Study Modes", icon: EyeIcon },
];
const selectedTab = ref("details");

const deleteProgram = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone.",
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
                        "Program has been deleted.",
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
                {{ program.name }} Program
            </h1>

            <nav
                class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    :class="[
                        'flex items-center px-4 py-2 space-x-2 text-sm font-medium transition',
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
                    ]"
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            <div
                class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
            >
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
                        <ShowDetails v-show="selectedTab == 'details'" :program="program"/>

                        <!-- Departments Panel -->
                        <ShowDepartments v-show="selectedTab == 'departments'" :program="program" :users="users"/>

                        <ShowStudyModes v-show="selectedTab == 'modes'" :program="program" />
                    </div>
                </transition>
            </div>
        </div>
    </AppLayout>

</template>
