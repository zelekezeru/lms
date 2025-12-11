<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const form = useForm({
  file: null,
});

function handleFileUpload(e) {
  form.file = e.target.files[0];
}

function submitImport() {
  form.post(route('students.paymentcodes'), {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Import Successful',
        text: 'Student payment codes were imported successfully.',
        timer: 2000,
        showConfirmButton: false,
      });
      form.reset();
      form.clearErrors();
    },
    onError: () => {
      Swal.fire({
        icon: 'error',
        title: 'Import Failed',
        text: 'There was a problem importing the file. Check errors and try again.',
      });
    }
  });
}
</script>

<template>
  <AppLayout>
    <div class="container mx-auto py-12 px-4">
      <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Import Students Payment Codes</h1>

        <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Upload an Excel or CSV file containing students payment codes. The file will be submitted to the students.paymentcodes route for processing.</p>

        <form @submit.prevent="submitImport" enctype="multipart/form-data" class="space-y-4">
          <div>
            <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Choose Excel File</label>
            <input
              id="file"
              type="file"
              name="file"
              @change="handleFileUpload"
              accept=".xlsx,.xls,.csv"
              class="block w-full text-sm text-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"
              required
            />
            <p v-if="form.errors.file" class="text-sm text-red-500 mt-1">{{ form.errors.file }}</p>
          </div>

          <div class="flex items-center gap-3">
            <button
              type="submit"
              class="bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-2 px-4 rounded-lg shadow"
              :disabled="form.processing"
            >
              <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4" />
              </svg>
              Import File
            </button>

            <button
              type="button"
              class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm"
              @click="() => { form.reset(); form.clearErrors(); }"
            >
              Reset
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
