<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({ form: Object });
const emit = defineEmits(['submit']);

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
  <form @submit.prevent="emit('submit')" class="p-4 shadow rounded">
    <!-- Personal Informations Section -->
    <div class="mb-4">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Personal Informations</h2>
      <hr class="my-4" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <InputLabel for="student_name" value="Student Name" />
        <TextInput
          id="student_name"
          type="text"
          v-model="form.student_name"
          class="w-full"
          placeholder="Full Name"
        />
        <InputError :message="form.errors?.student_name" class="mt-2" />
      </div>

      <div>
        <InputLabel for="father_name" value="Father's Name" />
        <TextInput
          id="father_name"
          type="text"
          v-model="form.father_name"
          class="w-full"
          placeholder="Father's Name"
        />
        <InputError :message="form.errors?.father_name" class="mt-2" />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div>
        <InputLabel for="grand_father_name" value="Grandfather's Name" />
        <TextInput
          id="grand_father_name"
          type="text"
          v-model="form.grand_father_name"
          class="w-full"
          placeholder="Grandfather's Name"
        />
        <InputError :message="form.errors?.grand_father_name" class="mt-2" />
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
          <option value="Choose" disabled>Select Marital Status</option>
          <option value="single">Single</option>
          <option value="married">Married</option>
          <option value="divorced">Divorced</option>
          <option value="widowed">Widowed</option>
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
        <div class="flex mb-4">
            <div class="w-1/2 pr-2">
                <label class="block text-gray-700">Church Name</label>
                <input v-model="form.church_name" class="w-full border rounded p-2" placeholder="e.g., Kalehiwot" />
            </div>
            <div class="w-1/2 pl-2">
                <label class="block text-gray-700">Church Address</label>
                <input v-model="form.church_address" class="w-full border rounded p-2" placeholder="e.g., Hawassa, Sidama" />
            </div>
        </div>
        <div class="flex mb-4">
            <div class="w-1/2 pr-2">
                <label class="block text-gray-700">Pastor's Name</label>
                <input v-model="form.pastor_name" class="w-full border rounded p-2" placeholder="Pastor's Full Name" />
            </div>
            <div class="w-1/2 pl-2">
                <label class="block text-gray-700">Pastor's Phone</label>
                <input v-model="form.pastor_phone" class="w-full border rounded p-2" placeholder="Pastor Phone Number" />
            </div>
        </div>
        <div class="flex mb-4">
            <div class="w-1/2 pr-2">
                <label class="block text-gray-700">Position/Denomination</label>
                <input v-model="form.position_denomination" class="w-full border rounded p-2" placeholder="e.g., Title/Denomination" />
            </div>
            <div class="w-1/2 pl-2">
                <label class="block text-gray-700">Office Use Notes</label>
                <textarea v-model="form.office_use_notes" class="w-full border rounded p-2" placeholder="Internal Notes"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</template>
