<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, MultiSelect } from "primevue";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    PencilSquareIcon, 
    PlusCircleIcon,
    BookOpenIcon,
    HomeModernIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    church: {
        type: Object,
        required: true,
    },
});

</script>

<template>
    <div class="flex items-center justify-between mb-4">
        <h2
            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
        >
            Church Information
        </h2>
    </div>

    <!-- Make sure Church is not null -->
    <div v-if="!church" class="text-center">
        <p class="text-gray-500 dark:text-gray-400">
            No church information available.
        </p>
    </div>
    <div v-else-if="church" class="overflow-x-auto">
        <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
            <div class="grid grid-cols-2 gap-4">
                <div v-if="church.church_name" class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Church</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        {{ church.church_name }}
                    </span>
                </div>
                <div v-if="church.church_address" class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Address</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        {{ church.church_address }}
                    </span>
                </div> <!-- Closing the div for Church Address -->

                <div v-if="church.pastor_name" class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Pastor's Name</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        {{ church.pastor_name }}
                    </span>
                </div> <!-- Closing the div for Pastor's Name -->
                <div v-if="church.pastor_phone" class="flex flex-col">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Pastor's Phone</span
                    >
                    <span
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        {{ church.pastor_phone }}
                    </span>
                </div> <!-- Closing the div for Pastor's Phone -->
            </div>
        </div>
    </div>
</template>