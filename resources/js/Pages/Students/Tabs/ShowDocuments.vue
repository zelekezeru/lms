<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import { ClipboardDocumentListIcon, FolderOpenIcon, EyeIcon } from "@heroicons/vue/24/outline";
import Modal from "@/Components/Modal.vue";
import ShowFiles from "./ShowFiles.vue";
import ShowTranscript from "./ShowTranscript.vue";


const props = defineProps({
    documents: {
        type: Array,
        required: true,
    },
    student: {
        type: Object,
        required: true,
    },
    semesters: {
        type: Object,
        required: true,
    },
});


// Multi nav header options
const selectedTab = ref('documents');

const tabs = [
    { key: 'documents', label: 'Documents', icon: ClipboardDocumentListIcon },
    { key: 'transcript', label: 'Transcript', icon: FolderOpenIcon },
];

</script>

<template>
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
    <div
        :key="selectedTab"
        class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
    >

        <!-- Documents Panel -->
        <ShowFiles
            v-if="selectedTab === 'documents'"
            :student="student"
            :documents="documents"
        />

        <!-- Church Panel -->
        <ShowTranscript
            v-else-if="selectedTab === 'transcript'"
            :student="student"
            :semesters="semesters"
        />

        </div>
    </transition>
    
    
</template>
