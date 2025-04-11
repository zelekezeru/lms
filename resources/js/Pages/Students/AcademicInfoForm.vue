<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ArrowRightIcon, ArrowLeftIcon } from "@heroicons/vue/24/solid";

// const props = defineProps({ form: Object });

const getYears = () => {
  const currentYear = new Date().getFullYear();
  const years = [];
  for (let year = 2000; year <= currentYear; year++) {
    years.push(year);
  }
  return years;
};

const props = defineProps(['form']);
const emit = defineEmits(['next', 'previous']);
</script>

<template>
    <div>
        <h2 class="text-lg font-bold mb-4">Academic Information</h2>
        <!-- Add other basic fields here -->
        <div class="flex justify-end mt-4">
            <div class="mx-2">
                <button
                    @click="$emit('previous')"
                    class="inline-flex items-center rounded-md bg-blue-600 text-white mx-2 px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-gray-700"
                >
                    <ArrowLeftIcon class="w-5 h-5 mr-2" /> Previous
                </button>
                
                <!-- Add other basic fields here -->
                <button
                    @click="$emit('next')"
                    class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-green-700 mt-4"
                >
                    <ArrowRightIcon class="w-5 h-5 mr-2" /> Next
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <InputLabel for="academic_year" value="Academic Year" />
                <select
                    id="academic_year"
                    v-model="form.academic_year"
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
                <InputError :message="form.errors?.academic_year" class="mt-2" />
            </div>

            <div>
                <InputLabel for="program" value="Program" />
                <select
                    id="program"
                    v-model="form.program"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                >
                    <option value="" disabled>Select Program</option>
                    <option value="Regular">Regular</option>
                    <option value="Online">Online</option>
                    <option value="Distance">Distance</option>
                    <option value="Other">Other</option>
                </select>
                <InputError :message="form.errors?.program" class="mt-2" />
            </div>
        </div>
    </div>
</template>