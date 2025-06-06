<script setup>
import {
    ChevronDownIcon,
    ChevronUpIcon,
    UsersIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    icon: {
        type: Function,
        required: true,
    },
    sidebarVisible: {
        type: Boolean,
        required: true,
    },
});

const isOpen = ref(false);

// Custom Transition Hooks for Smooth Height Animation
const beforeEnter = (el) => {
    el.style.height = "0";
    el.style.opacity = "0";
};

const enter = (el) => {
    el.style.transition = "all 0.1s ease";
    el.style.height = el.scrollHeight + "px";
    el.style.opacity = "1";
};

const afterEnter = (el) => {
    el.style.height = "auto";
};

const beforeLeave = (el) => {
    el.style.height = el.scrollHeight + "px";
    el.style.opacity = "1";
};

const leave = (el) => {
    el.style.transition = "all 0.1s ease";
    // Force reflow to ensure the transition occurs
    void el.offsetHeight;
    el.style.height = "0";
    el.style.opacity = "0";
};

const afterLeave = (el) => {
    el.style.height = "";
};
</script>

<template>
    <div>
        <button
            @click="isOpen = !isOpen"
            :class="{
                'border-l-4 border-blue-700 box-border bg-gray-100 dark:bg-gray-600':
                    isOpen,
            }"
            class="w-full flex items-center rounded-md justify-between px-4 py-1 hover:bg-gray-200 dark:hover:bg-gray-700"
        >
            <div class="flex items-center">
                <component :is="icon" class="w-7 p-1" />
                <transition name="fade">
                    <span
                        v-if="sidebarVisible || sidebarHovered"
                        class="text-sm"
                    >
                        {{ label }}
                    </span>
                </transition>
            </div>
            <component
                v-if="sidebarVisible || sidebarHovered"
                :is="isOpen ? ChevronUpIcon : ChevronDownIcon"
                class="w-5 h-5 text-gray-600 dark:text-gray-200"
            />
        </button>
        <transition
            @before-enter="beforeEnter"
            @enter="enter"
            @after-enter="afterEnter"
            @before-leave="beforeLeave"
            @leave="leave"
            @after-leave="afterLeave"
        >
            <div
                v-if="isOpen && (sidebarVisible || sidebarHovered)"
                class="space-y-2 rounded-md p-2"
            >
                <slot />
            </div>
        </transition>
    </div>
</template>
