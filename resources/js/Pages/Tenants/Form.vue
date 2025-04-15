<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { PhotoIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  form: { type: Object, required: true },
});

// Handle logo selection and preview
const handleFileChange = (e) => {
    const file = e.target.files[0];
    props.form.logo = file;

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            props.form.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        props.form.imagePreview = null;
    }
};
</script>

<template>
  <form @submit.prevent="submit" enctype="multipart/form-data">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Institution Name</label>
        <input
          id="name"
          type="text"
          v-model="form.name"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
        <input
          id="email"
          type="email"
          v-model="form.email"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <div v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Institution Phone</label>
        <input
          id="phone"
          type="text"
          v-model="form.phone"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <div v-if="form.errors.phone" class="text-red-500 text-sm">{{ form.errors.phone }}</div>
      </div>

      <!-- logo Upload & Preview -->
      <div>
        <InputLabel for="profile_img" value="logo" />
        <div class="flex items-center gap-4">
            <label for="profile_img" class="cursor-pointer px-4 py-2 text-white flex items-center gap-2 rounded-md shadow transition bg-black hover:bg-blue-700">
                <PhotoIcon class="w-5 h-5" /> Upload Logo
            </label>
            <input id="profile_img" type="file" accept="image/*" class="hidden" @change="handleFileChange" />
            <div v-if="props.form.imagePreview" class="w-16 h-16 rounded-full border shadow overflow-hidden">
                <img :src="props.form.imagePreview" alt="Profile Preview" class="object-cover w-full h-full" />
            </div>
        </div>
        <InputError :message="props.form.errors.profile_img" />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div>
        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Institution Address</label>
        <input
          id="address"
          type="text"
          v-model="form.address"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <div v-if="form.errors.address" class="text-red-500 text-sm">{{ form.errors.address }}</div>
      </div>

      <div>
        <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Representative's Name</label>
        <input
          id="contact_person"
          type="text"
          v-model="form.contact_person"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <div v-if="form.errors.contact_person" class="text-red-500 text-sm">{{ form.errors.contact_person }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div>
        <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Representative's Phone</label>
        <input
          id="contact_phone"
          type="text"
          v-model="form.contact_phone"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <div v-if="form.errors.contact_phone" class="text-red-500 text-sm">{{ form.errors.contact_phone }}</div>
      </div>

      <div>
        <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Representative's Email</label>
        <input
          id="contact_email"
          type="text"
          v-model="form.contact_email"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <div v-if="form.errors.contact_email" class="text-red-500 text-sm">{{ form.errors.contact_email }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
        <select
          id="status"
          v-model="form.status"
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option value="">Select Status(Default Inactive)</option>
          <option value="0">Inactive</option>
          <option value="1">Active</option>
        </select>
        <div v-if="form.errors.status" class="text-red-500 text-sm">{{ form.errors.status }}</div>
      </div>

      <div>
        <label for="paid" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Paid Status</label>
        <select
          id="paid"
          v-model="form.paid"
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option value="">Select Paid(default Not Paid)</option>
          <option value="0">Not Paid</option>
          <option value="1">Paid</option>
        </select>
        <div v-if="form.errors.paid" class="text-red-500 text-sm">{{ form.errors.paid }}</div>
      </div>
    </div>

    <div class="mt-6 flex justify-center">
      <button
        type="submit"
        :disabled="form.processing"
        class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <span v-if="!form.processing">Submit</span>
        <span v-else>Submitting...</span>
      </button>
    </div>
  </form>
</template>
