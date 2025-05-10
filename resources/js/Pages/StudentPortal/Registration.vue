<script setup>
import StudentLayout from "@/Layouts/StudentLayout.vue";
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
    <StudentLayout>
        <div class="max-w-3xl mx-auto mt-10 bg-white dark:bg-gray-800 p-8 rounded-xl shadow">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100">
                Apply for Registration
            </h2>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Full Name
                    </label>
                    <input
                        type="text"
                        v-model="form.full_name"
                        required
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email Address
                    </label>
                    <input
                        type="email"
                        v-model="form.email"
                        required
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Phone Number
                    </label>
                    <input
                        type="tel"
                        v-model="form.phone"
                        required
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Program
                    </label>
                    <select
                        v-model="form.program"
                        required
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    >
                        <option value="" disabled>Select a program</option>
                        <option value="BSc in Computer Science">BSc in Computer Science</option>
                        <option value="BA in Business Administration">BA in Business Administration</option>
                        <option value="BSc in Civil Engineering">BSc in Civil Engineering</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Semester
                    </label>
                    <select
                        v-model="form.semester"
                        required
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    >
                        <option value="" disabled>Select semester</option>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                    </select>
                </div>

                <div class="pt-4">
                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
                    >
                        Submit Application
                    </button>
                </div>
            </form>

            <div
                v-if="successMessage"
                class="mt-6 p-4 bg-green-100 text-green-800 rounded-lg text-sm"
            >
                {{ successMessage }}
            </div>
        </div>
    </StudentLayout>
</template>

