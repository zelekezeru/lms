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
    <div class="space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700/60">
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">
                    Church Connection
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Congregation details and pastoral references.
                </p>
            </div>
        </div>

        <!-- Make sure Church is not null -->
        <div v-if="!church" class="bg-gray-50 dark:bg-gray-700/20 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-8 text-center">
            <p class="text-sm text-gray-400 dark:text-gray-500 italic">
                No church connection details recorded.
            </p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Church Card -->
            <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-5 flex gap-4 shadow-sm hover:shadow-md transition duration-300">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                    <HomeModernIcon class="w-6 h-6" />
                </div>
                <div class="space-y-3 min-w-0 flex-1">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Church Name</p>
                        <p class="text-base font-extrabold text-gray-950 dark:text-gray-50 mt-1 truncate">
                            {{ church.church_name }}
                        </p>
                    </div>
                    <div v-if="church.church_address">
                        <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Address</p>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mt-1">
                            {{ church.church_address }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pastor Card -->
            <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-5 flex gap-4 shadow-sm hover:shadow-md transition duration-300">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400 shrink-0">
                    <UsersIcon class="w-6 h-6" />
                </div>
                <div class="space-y-3 min-w-0 flex-1">
                    <div v-if="church.pastor_name">
                        <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Pastor's Name</p>
                        <p class="text-base font-extrabold text-gray-950 dark:text-gray-50 mt-1 truncate">
                            {{ church.pastor_name }}
                        </p>
                    </div>
                    <div v-if="church.pastor_phone">
                        <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Pastor's Phone</p>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mt-1">
                            {{ church.pastor_phone }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>