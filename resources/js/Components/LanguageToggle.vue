<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useI18n } from "vue-i18n";
import {
    GlobeAltIcon,
    ChevronDownIcon,
    LanguageIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    collapsed: {
        type: Boolean,
        default: false,
        required: false,
    },
});
// Refs for state and element tracking
const dropdownOpen = ref(false);
const dropdownRef = ref(null);

const { locale } = useI18n({ useScope: "global" });
const selectedLocale = ref(localStorage.getItem("locale") || locale.value);

const changeLocale = (lang) => {
    locale.value = lang;
    selectedLocale.value = lang;
    localStorage.setItem("locale", lang);
    dropdownOpen.value = false;
};

const languages = {
    en: "English",
    am: "አማርኛ",
    zh: "中文",
    ur: "Urdu - اردو",
    sw: "Kiswahili",
    ar: "Arabic - العربية",
    es: "Español",
    fr: "Français",
    om: "Afaan Oromoo",
    ti: "ትግርኛ",
};

const languageName = (lang) => {
    return languages[lang] || "Unknown";
};

// Handle outside click to close dropdown
const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        dropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});
onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
    <div class="relative inline-block text-left" ref="dropdownRef">
        <button
            @click="dropdownOpen = !dropdownOpen"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md shadow bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600 focus:outline-none"
        >
            <GlobeAltIcon class="w-5 h-5 mr-2" />
            <span v-if="!collapsed">Language</span>
            <ChevronDownIcon
                class="w-4 h-4 ml-2 transition-transform duration-200"
                :class="{ 'rotate-180': dropdownOpen }"
            />
        </button>

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="scale-95 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
        >
            <div
                v-if="dropdownOpen"
                class="absolute right-0 z-20 w-40 mt-2 origin-top-right bg-white border border-gray-200 divide-y divide-gray-100 rounded-md shadow-lg dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700"
            >
                <div class="py-1">
                    <button
                        v-for="(value, key) in languages"
                        :key="key"
                        @click="changeLocale(key)"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <LanguageIcon class="w-4 h-4 mr-2" /> {{ value }}
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>
