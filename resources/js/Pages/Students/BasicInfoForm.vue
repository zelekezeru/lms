<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ArrowRightIcon, ArrowLeftIcon } from "@heroicons/vue/24/solid";


const props = defineProps({ form: Object });

// const props = defineProps(['form']);
const emit = defineEmits(['next']);
</script>

<template>
    <div>
        <h2 class="text-lg font-bold mb-4">Basic Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <InputLabel for="first_name" value="Student Name" />
            <TextInput
            id="first_name"
            type="text"
            v-model="form.first_name"
            class="w-full"
            placeholder="Full Name"
            />
            <InputError :message="form.errors?.first_name" class="mt-2" />
        </div>

        <div>
            <InputLabel for="middle_name" value="Father's Name" />
            <TextInput
            id="middle_name"
            type="text"
            v-model="form.middle_name"
            class="w-full"
            placeholder="Father's Name"
            />
            <InputError :message="form.errors?.middle_name" class="mt-2" />
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <InputLabel for="last_name" value="Grandfather's Name" />
            <TextInput
            id="last_name"
            type="text"
            v-model="form.last_name"
            class="w-full"
            placeholder="Grandfather's Name"
            />
            <InputError :message="form.errors?.last_name" class="mt-2" />
        </div>

        <div>
            <InputLabel for="email" value="Email" />
            <TextInput
            id="email"
            type="email"
            v-model="form.email"
            class="w-full"
            placeholder="Email address"
            />
            <InputError :message="form.errors?.email" class="mt-2" />
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <InputLabel for="mobile_phone" value="Mobile Phone" />
            <TextInput
            id="mobile_phone"
            type="text"
            v-model="form.mobile_phone"
            class="w-full"
            placeholder="Mobile Phone Number"
            />
            <InputError :message="form.errors?.mobile_phone" class="mt-2" />
        </div>

        <div>
            <InputLabel for="office_phone" value="Office Phone" />
            <TextInput
            id="office_phone"
            type="text"
            v-model="form.office_phone"
            class="w-full"
            placeholder="Office Phone Number"
            />
            <InputError :message="form.errors?.office_phone" class="mt-2" />
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <InputLabel for="marital_status" value="Marital Status" />
            <select
            id="marital_status"
            v-model="form.marital_status"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
            >
            <option value="" disabled>Select Marital Status</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Divorced">Divorced</option>
            <option value="Widowed">Widowed</option>
            </select>
            <InputError :message="form.errors?.marital_status" class="mt-2" />
        </div>

        <div>
            <InputLabel for="sex" value="Sex" />
            <select
            id="sex"
            v-model="form.sex"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
            >
            <option value="" disabled>Select Sex</option>
            <option value="M">Male</option>
            <option value="F">Female</option>
            </select>
            <InputError :message="form.errors?.sex" class="mt-2" />
        </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <InputLabel for="date_of_birth" value="Date of Birth" />
            <TextInput
            id="date_of_birth"
            type="date"
            v-model="form.date_of_birth"
            class="w-full"
            />
            <InputError :message="form.errors?.date_of_birth" class="mt-2" />
        </div>

        <div>
            <InputLabel for="address" value="Address" />
            <TextInput
            id="address"
            type="text"
            v-model="form.address"
            class="w-full"
            placeholder="e.g., Hawassa, Sidama, Ethiopia"
            />
            <InputError :message="form.errors?.address" class="mt-2" />
        </div>
        </div>
        
        <div class="flex justify-end mt-4">
            <!-- Add other basic fields here -->
            <button
                @click="$emit('next')"
                class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-green-700 mt-4"
            >
                <ArrowRightIcon class="w-5 h-5 mr-2" /> Next
            </button>
        </div>
    </div>
</template>