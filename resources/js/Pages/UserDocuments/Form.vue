<script setup>
import { PhotoIcon } from "@heroicons/vue/24/outline";
import { ref, onMounted } from "vue";

const props = defineProps({
    form: { type: Object, required: true },
    user: { type: Object, required: true },
    userDocument: { type: Object, required: true },
});

const imagePreview = ref(null);

// Handle file changes for image and document uploads
const handleFileChange = (e, type) => {
    const file = e.target.files[0];

    if (type === "image") {
        if (file) {
            // Validate that the file is an image
            if (!file.type.startsWith("image/")) {
                alert("The selected file must be an image.");
                return;
            }

            props.form.image = file;

            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else if (props.userDocument.image) {
            // Retain existing image if no new file is selected
            props.form.image = props.userDocument.image;
        }
    } else if (type === "file") {
        if (file) {
            // Validate that the file is a valid document
            const allowedTypes = [
                "application/pdf",
                "application/msword",
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                "application/vnd.ms-excel",
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                "text/plain",
                "text/csv",
            ];
            if (!allowedTypes.includes(file.type)) {
                alert("The selected file must be a valid document (PDF, DOC, XLS, etc.).");
                return;
            }

            props.form.file = file;
        } else if (props.userDocument.file) {
            // Retain existing file if no new file is selected
            props.form.file = props.userDocument.file;
        }
    }
};

// Reset form fields (optional)
const resetForm = () => {
    props.form.title = "";
    props.form.description = "";
    props.form.image = "";
    props.form.file = "";
    imagePreview.value = null;
};

// Show existing image on edit
onMounted(() => {
    if (props.form.image && typeof props.form.image === "string") {
        imagePreview.value = props.form.image;
    }
});
</script>

<template>
    <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-8">
        <input type="hidden" name="user_id" :value="form.user_id" />

        <!-- Title & Description -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                <input
                    id="title"
                    type="text"
                    v-model="form.title"
                    required
                    class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
                />
                <p v-if="form.errors.title" class="text-sm text-red-500 mt-1">{{ form.errors.title }}</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    required
                    class="mt-1 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
                ></textarea>
                <p v-if="form.errors.description" class="text-sm text-red-500 mt-1">{{ form.errors.description }}</p>
            </div>

          <!-- Image Upload & Preview -->
          <div>
              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                  <label
                      for="image"
                      class="cursor-pointer inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition"
                      aria-label="Upload image"
                  >
                      <PhotoIcon class="w-5 h-5 mr-2" />
                      Upload Image
                  </label>
                  <input
                      id="image"
                      type="file"
                      accept="image/*"
                      class="hidden"
                      @change="(e) => handleFileChange(e, 'image')"
                  />

                  <div v-if="imagePreview" class="w-28 h-28 rounded-lg overflow-hidden border dark:border-gray-600">
                      <img :src="imagePreview" alt="Image Preview" class="w-full h-full object-cover" />
                  </div>
              </div>
              <p v-if="form.errors.image" class="text-sm text-red-500 mt-1">{{ form.errors.image }}</p>
          </div>

          <!-- Document Upload -->
          <div>
              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                  <label
                      for="file"
                      class="cursor-pointer inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition"
                      aria-label="Upload file"
                  >
                      <PhotoIcon class="w-5 h-5 mr-2" />
                      Upload Document <span class="ml-1 text-sm">(PDF, DOC, XLS, etc.)</span>
                  </label>
                  <input
                      id="file"
                      type="file"
                      accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.csv"
                      class="hidden"
                      @change="(e) => handleFileChange(e, 'file')"
                  />

                  <!-- Show existing file if it's a string (edit mode) -->
                  <div v-if="form.file && typeof form.file === 'string'" class="text-sm text-gray-700 dark:text-gray-300">
                      <a :href="form.file" target="_blank" class="text-indigo-600 hover:underline">View Existing File</a>
                  </div>
              </div>
              <p v-if="form.errors.file" class="text-sm text-red-500 mt-1">{{ form.errors.file }}</p>
          </div>
        </div>

        <!-- Submit & Reset Buttons -->
        <div class="flex justify-center items-center gap-4 pt-4">
            <button
                type="submit"
                :disabled="form.processing"
                class="px-6 py-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition"
            >
                <span v-if="!form.processing">Submit</span>
                <span v-else>Submitting...</span>
            </button>
            <button
                type="button"
                @click="resetForm"
                class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg shadow"
            >
                Reset
            </button>
        </div>
    </form>
</template>
