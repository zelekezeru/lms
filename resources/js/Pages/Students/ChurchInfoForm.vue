<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ArrowRightIcon, ArrowLeftIcon, ArrowDownTrayIcon } from "@heroicons/vue/24/solid";

const props = defineProps({ form: Object });

const getYears = () => {
  const currentYear = new Date().getFullYear();
  const years = [];
  for (let year = 2000; year <= currentYear; year++) {
    years.push(year);
  }
  return years;
};

// const props = defineProps(['form']);
const emit = defineEmits(['next']);
</script>

<template>
    <div>
        <h2 class="text-lg font-bold mb-4">Basic Information</h2>     

        <div class="mt-6 flex justify-end">
            <button
                @click="$emit('previous')"
                class="inline-flex items-center rounded-md bg-blue-600 text-white mx-2 px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-gray-700"
            >
                <ArrowLeftIcon class="w-5 h-5 mr-2" /> Previous
            </button>

            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
            <ArrowDownTrayIcon class="w-5 h-5 mr-2" /> 
                <span v-if="!form.processing">Submit</span>
                <span v-else>Submitting...</span>
            </button>
        </div>   

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <InputLabel for="church_name" value="Church Name" />
            <TextInput
            id="church_name"
            type="text"
            v-model="form.church_name"
            class="w-full"
            placeholder="e.g., Kalehiwot"
            />
            <InputError :message="form.errors?.church_name" class="mt-2" />
        </div>

        <div>
            <InputLabel for="church_address" value="Church Address" />
            <TextInput
            id="church_address"
            type="text"
            v-model="form.church_address"
            class="w-full"
            placeholder="e.g., Hawassa, Sidama"
            />
            <InputError :message="form.errors?.church_address" class="mt-2" />
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <InputLabel for="pastor_name" value="Pastor's Name" />
            <TextInput
            id="pastor_name"
            type="text"
            v-model="form.pastor_name"
            class="w-full"
            placeholder="Pastor's Full Name"
            />
            <InputError :message="form.errors?.pastor_name" class="mt-2" />
        </div>

        <div>
            <InputLabel for="pastor_phone" value="Pastor's Phone" />
            <TextInput
            id="pastor_phone"
            type="text"
            v-model="form.pastor_phone"
            class="w-full"
            placeholder="Pastor Phone Number"
            />
            <InputError :message="form.errors?.pastor_phone" class="mt-2" />
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <InputLabel for="position_denomination" value="Position/Denomination" />
            <TextInput
            id="position_denomination"
            type="text"
            v-model="form.position_denomination"
            class="w-full"
            placeholder="e.g., Title/Denomination"
            />
            <InputError :message="form.errors?.position_denomination" class="mt-2" />
        </div>

        <div>
            <InputLabel for="office_use_notes" value="Office Use Notes" />
            <textarea
            id="office_use_notes"
            v-model="form.office_use_notes"
            class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
            placeholder="Internal Notes"
            ></textarea>
            <InputError :message="form.errors?.office_use_notes" class="mt-2" />
        </div>
    </div>  

    </div>
</template>