<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ArrowRightIcon, ArrowLeftIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    departments: {
        type: Array,
        required: true,
    },
    programs: {
        type: Array,
        required: true,
    },
});

// Emits
const emit = defineEmits(['next', 'previous']);

// Helper function to generate years
const getYears = () => {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let year = 2000; year <= currentYear; year++) {
        years.push(year);
    }
    return years;
};
</script>

<template>
    <div>
        <h2 class="text-lg font-bold mb-4">Academic Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Academic Year Dropdown -->
            <div>
                <InputLabel for="year_id" value="Academic Year" />
                <select
                    id="year_id"
                    v-model="form.year_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                >
                    <option value="" disabled>Select Year</option>
                    <option
                        v-for="year in getYears()"
                        :key="year"
                        :value="year"
                    >
                        {{ year }}
                    </option>
                </select>
                <InputError :message="form.errors?.year_id" class="mt-2" />
            </div>

            <!-- Program Dropdown -->
            <div>
                <InputLabel for="program_id" value="Select Program" />
                <select
                    id="program_id"
                    v-model="form.program_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                >
                    <option value="">Select Program</option>
                    <option
                        v-for="program in programs"
                        :key="program.id"
                        :value="program.id"
                    >
                        {{ program.name }}
                    </option>
                </select>
                <InputError :message="form.errors?.program_id" class="mt-2" />
            </div>
        </div>

        <!-- Department Input -->
         
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>

            <div>
                <InputLabel for="department_id" value="Department" />
                <select
                    id="department_id"
                    v-model="form.department_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                >
                    <option value="" disabled>Select Department</option>
                    <option
                        v-for="department in departments"
                        :key="department.id"
                        :value="department.id"
                    >
                        {{ department.name }}
                    </option>
                </select>
                <InputError :message="form.errors?.department_id" class="mt-2" />
            </div>

            <div>
                
            </div>
        </div>
        
        <div class="flex justify-end mt-4">
            <button
                @click="$emit('previous')"
                class="inline-flex items-center rounded-md bg-blue-600 mx-2 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-blue-700"
            >
                <ArrowLeftIcon class="w-5 h-5 mr-2" /> Previous
            </button>

            <button
                @click="$emit('next')"
                class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-green-700"
            >
                <ArrowRightIcon class="w-5 h-5 mr-2" /> Next
            </button>
        </div>
    </div>
</template>