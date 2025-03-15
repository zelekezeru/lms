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
        <InputLabel for="address_1" value="Address" />
        <TextInput
          id="address_1"
          type="text"
          v-model="form.address_1"
          class="w-full"
          placeholder="e.g., Hawassa, Sidama, Ethiopia"
        />
        <InputError :message="form.errors?.address_1" class="mt-2" />
      </div>
    </div>

    <!-- Academic Informations Section -->
    <div class="mb-4 mt-8">
      <h2 class="text-xl font-semibold text-gray-800">Academic Informations</h2>
      <hr class="my-4" />
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

    <!-- Church Informations Section -->
    <div class="mb-4 mt-8">
      <h2 class="text-xl font-semibold text-gray-800">Church Informations</h2>
      <hr class="my-4" />
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

    <div class="mt-6">
      <PrimaryButton type="submit">
        Save
      </PrimaryButton>
    </div>

  </form>
    
</template>
