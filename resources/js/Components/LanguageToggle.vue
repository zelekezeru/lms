<template>
    <div class="relative inline-block text-left">
      <div>
        <button
          @click="dropdownOpen = !dropdownOpen"
          class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow hover:bg-blue-700 focus:outline-none"
        >
          <GlobeAltIcon class="w-5 h-5 mr-2" />
          {{ languageName(selectedLocale) }}
          <ChevronDownIcon class="w-4 h-4 ml-2 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" />
        </button>
      </div>
  
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
            <!-- English -->
            <button
              @click="changeLocale('en')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <LanguageIcon class="w-4 h-4 mr-2" /> English
            </button>
            <!-- Amharic -->
            <button
              @click="changeLocale('am')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <LanguageIcon class="w-4 h-4 mr-2" /> አማርኛ
            </button>
            <!-- Swahili -->
            <button
              @click="changeLocale('sw')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <LanguageIcon class="w-4 h-4 mr-2" /> Kiswahili
            </button>            
            <!-- Chinese -->
            <button
              @click="changeLocale('zh')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <LanguageIcon class="w-4 h-4 mr-2" /> 中文
            </button>
            <!-- Urdu -->
            <button
              @click="changeLocale('ur')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <LanguageIcon class="w-4 h-4 mr-2" /> Urdu - اردو
            </button>
            <!-- Arabic -->
            <button
              @click="changeLocale('ar')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <LanguageIcon class="w-4 h-4 mr-2" /> Arabic - العربية
            </button>
            <!-- Afan Oromo -->
            <button
              @click="changeLocale('om')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <LanguageIcon class="w-4 h-4 mr-2" /> Afaan Oromoo
            </button>
            <!-- Spanish -->
            <button
              @click="changeLocale('es')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <LanguageIcon class="w-4 h-4 mr-2" /> Español
            </button>
            <!-- French -->
            <button
              @click="changeLocale('fr')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <LanguageIcon class="w-4 h-4 mr-2" /> Français
            </button>
            <!-- Tigrigna -->
            <button
              @click="changeLocale('ti')"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <LanguageIcon class="w-4 h-4 mr-2" /> ትግርኛ
            </button>
          </div>
        </div>
      </transition>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { GlobeAltIcon, ChevronDownIcon, LanguageIcon } from '@heroicons/vue/24/outline'
  
  const dropdownOpen = ref(false)
  const { locale } = useI18n({ useScope: 'global' })
  const selectedLocale = ref(localStorage.getItem('locale') || locale.value)
  
  const changeLocale = (lang) => {
    locale.value = lang
    selectedLocale.value = lang
    localStorage.setItem('locale', lang)
    dropdownOpen.value = false
  }
  
  const languageName = (lang) => {
    return lang === 'en' ? 'English' : 'አማርኛ'
  }
  </script>
  