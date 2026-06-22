<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";

const form = ref({
    full_name: "",
    email: "",
    phone: "",
    program: "",
    semester: "",
});

const successMessage = ref("");

const submit = () => {
    console.log("Form submitted:", form.value);
    successMessage.value = "Registration submitted successfully!";

    // Clear form
    form.value = {
        full_name: "",
        email: "",
        phone: "",
        program: "",
        semester: "",
    };
};
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-2xl mx-auto px-4 md:px-6 pt-4 space-y-6">
            
            <!-- Header Banner -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 p-6 text-white shadow-lg">
                <!-- Decorative overlays -->
                <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/10 pointer-events-none"></div>
                <div class="absolute bottom-0 right-12 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>

                <h1 class="text-xl font-bold mb-1">Apply for Registration</h1>
                <p class="text-indigo-200 text-xs font-medium">
                    Submit your application details below to request enrollment for the next semester.
                </p>
            </div>

            <!-- Form Sheet Card -->
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-6 md:p-8 shadow-sm space-y-6">
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                            Full Name
                        </label>
                        <input
                            type="text"
                            v-model="form.full_name"
                            required
                            placeholder="Enter your full name"
                            class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 dark:bg-gray-850 dark:text-white transition"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                                Email Address
                            </label>
                            <input
                                type="email"
                                v-model="form.email"
                                required
                                placeholder="name@example.com"
                                class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 dark:bg-gray-850 dark:text-white transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                                Phone Number
                            </label>
                            <input
                                type="tel"
                                v-model="form.phone"
                                required
                                placeholder="+251-xxx-xxxx"
                                class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 dark:bg-gray-850 dark:text-white transition"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                                Program of Interest
                            </label>
                            <select
                                v-model="form.program"
                                required
                                class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 dark:bg-gray-850 dark:text-white transition"
                            >
                                <option value="" disabled>Select a program</option>
                                <option value="BSc in Computer Science">BSc in Computer Science</option>
                                <option value="BA in Business Administration">BA in Business Administration</option>
                                <option value="BSc in Civil Engineering">BSc in Civil Engineering</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                                Target Semester
                            </label>
                            <select
                                v-model="form.semester"
                                required
                                class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 dark:bg-gray-850 dark:text-white transition"
                            >
                                <option value="" disabled>Select target semester</option>
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-gray-700/60 flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-6 py-2.5 text-xs font-bold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl shadow-md transition duration-300 active:scale-95 cursor-pointer"
                        >
                            Submit Application
                        </button>
                    </div>
                </form>

                <!-- Success Alert -->
                <transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-2"
                >
                    <div
                        v-if="successMessage"
                        class="flex items-start gap-3 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900 rounded-xl p-4"
                    >
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                                Application Submitted
                            </p>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">
                                {{ successMessage }}
                            </p>
                        </div>
                    </div>
                </transition>
            </div>

        </div>
    </AppLayout>
</template>
