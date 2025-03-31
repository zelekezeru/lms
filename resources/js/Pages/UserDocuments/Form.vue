<script setup>
import { PhotoIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";

const props = defineProps({
    form: { type: Object, required: true },
});

// Reactive preview states
const imagePreview = ref(null);
const filePreview = ref(null);

// Handle file changes for image and document uploads
const handleFileChange = (e, type) => {
    const file = e.target.files[0];
    props.form[type] = file;

    if (type === "image" && file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result; // Update image preview
        };
        reader.readAsDataURL(file);
    }
};
</script>

<template>
  <form @submit.prevent="submit" enctype="multipart/form-data">
    <!-- Title and Description -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Title</label>
        <input
          id="title"
          type="text"
          v-model="form.title"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <div v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</div>
      </div>

      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
        <textarea
          id="description"
          v-model="form.description"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        ></textarea>
        <div v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</div>
      </div>
    </div>

    <!-- Image Upload & Preview -->
    <div class="mt-4">
      <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Image</label>
      <div class="flex items-center gap-4">
        <label
          for="image"
          class="cursor-pointer px-4 py-2 text-white flex items-center gap-2 rounded-md shadow transition bg-indigo-600 hover:bg-indigo-700"
        >
          <PhotoIcon class="w-5 h-5" />
          Upload Image
        </label>
        <input
          id="image"
          type="file"
          accept="image/*"
          class="hidden"
          @change="(e) => handleFileChange(e, 'image')"
        />
        <div v-if="imagePreview" class="w-16 h-16 rounded-full border shadow overflow-hidden">
          <img :src="imagePreview" alt="Image Preview" class="object-cover w-full h-full" />
        </div>
      </div>
      <div v-if="form.errors.image" class="text-red-500 text-sm">{{ form.errors.image }}</div>
    </div>

    <!-- File Upload -->
    <div class="mt-4">
      <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload File (Optional)</label>
      <div class="flex items-center gap-4">
        <label
          for="file"
          class="cursor-pointer px-4 py-2 text-white flex items-center gap-2 rounded-md shadow transition bg-indigo-600 hover:bg-indigo-700"
        >
          <PhotoIcon class="w-5 h-5" />
          Upload File
        </label>
        <input
          id="file"
          type="file"
          accept=".pdf,.doc,.docx"
          class="hidden"
          @change="(e) => handleFileChange(e, 'file')"
        />
      </div>
      <div v-if="form.errors.file" class="text-red-500 text-sm">{{ form.errors.file }}</div>
    </div>

    <!-- Submit Button -->
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

