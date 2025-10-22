<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import { EyeIcon, PencilSquareIcon } from "@heroicons/vue/24/solid";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import AppLayout from "@/Layouts/AppLayout.vue";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    students: Array,
    totalStudents: Number,
    activeStudents: Number,
    graduatedStudents: Number,
    regularStudents: Number,
    extentionStudents: Number,
    onlineStudents: Number,
    distanceStudents: Number
});

import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { PlusIcon } from "@heroicons/vue/24/solid";

// Modal state
const showChangeStatusModal = ref(false);

// Student status change form (align fields with modal form)
const statusCreateForm = useForm({
  status: "",
  file: null,
});

// File upload handler
function handleFileUpload(event) {
  statusCreateForm.file = event.target.files[0];
}

// Open modal
function openChangeStatusModal() {
  statusCreateForm.reset();
  statusCreateForm.clearErrors();
  showChangeStatusModal.value = true;
}

// Close modal
function closeChangeStatusModal() {
  showChangeStatusModal.value = false;
  statusCreateForm.reset();
}

// Submit status change
function submitstatusCreate() {
  statusCreateForm.post(route('graduatedStudents.import'), {
    onSuccess: () => {
      Swal.fire("Success", "Student status updated.", "success");
      closeChangeStatusModal();
    },
    onError: () => {
      Swal.fire("Error", "Failed to update status.", "error");
    }
  });
}

// Helper for permissions (stub, replace with actual logic)
function userCan(permission) {
  // Implement your permission logic here
  return true;
}
</script>

<template>
  <AppLayout>
    <div class="container mx-auto py-10 px-4 flex flex-col items-center">
      <div class="flex items-center justify-center mb-12">
        <h2 class="text-3xl md:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 via-blue-500 to-blue-400 drop-shadow-lg text-center px-8 py-4 rounded-xl border-b-4 border-blue-400 shadow-xl">
          <span class="inline-block align-middle mr-3 animate-bounce">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3"/>
            </svg>
          </span>
          Students Home
          <span class="inline-block align-middle ml-3 animate-bounce animation-delay-200">
            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7l4-4m0 0l4 4m-4-4v18"/>
            </svg>
          </span>
        </h2>
      </div>
    </div>

    <section class="border-t border-b border-gray-300 dark:border-gray-600 pt-6 pb-6 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-gray-800 dark:text-gray-200">
        <!-- All Students -->
        <Link
          :href="route('students.index')"
          class="flex flex-col items-center bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-blue-100 dark:border-gray-700 bg-gradient-to-br from-blue-500 via-blue-400 to-blue-300 transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
          <h2 class="text-lg font-extrabold text-white mb-1 flex items-center gap-2 justify-center">
            <EyeIcon class="w-5 h-5 text-white opacity-80" />
            All Students
          </h2>
          <span class="text-sm text-teal-100 mb-2 text-center">
            <span class="font-semibold text-white text-xl">{{ props.totalStudents }}</span>
            <span class="ml-1">Student{{ props.totalStudents === 1 ? '' : 's' }} Total</span>
          </span>
        </Link>

        <!-- Active Students -->
        <Link
          :href="route('students.index', { statuses: 'Active' })"
          class="flex flex-col items-center bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-green-100 dark:border-gray-700 bg-gradient-to-br from-green-500 via-green-400 to-green-300 transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-green-400"
        >
          <h2 class="text-lg font-extrabold text-white mb-1 flex items-center gap-2 justify-center">
            <EyeIcon class="w-5 h-5 text-white opacity-80" />
            Active Students
          </h2>
          <span class="text-sm text-teal-100 mb-2 text-center">
            <span class="font-semibold text-white text-xl">{{ props.activeStudents }}</span>
            <span class="ml-1">Student{{ props.activeStudents === 1 ? '' : 's' }} Total</span>
          </span>
        </Link>

        <!-- Graduated Students -->
        <Link
          :href="route('students.index', { statuses: 'Graduated' })"
          class="flex flex-col items-center bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-yellow-100 dark:border-gray-700 bg-gradient-to-br from-yellow-400 via-yellow-300 to-yellow-200 transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-400"
        >
          <h2 class="text-lg font-extrabold text-white mb-1 flex items-center gap-2 justify-center">
            <EyeIcon class="w-5 h-5 text-white opacity-80" />
            Graduated Students
          </h2>
          <span class="text-sm text-teal-100 mb-2 text-center">
            <span class="font-semibold text-white text-xl">{{ props.graduatedStudents }}</span>
            <span class="ml-1">Student{{ props.graduatedStudents === 1 ? '' : 's' }} Total</span>
          </span>           
        </Link>
      </div>

      <div class="grid my-6 grid-cols-1 md:grid-cols-3 gap-6 text-gray-800 dark:text-gray-200">
        <!-- Regular Students -->
        <Link
          :href="route('students.index', { study_mode: 1 })"
          class="flex flex-col items-center bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-indigo-100 dark:border-gray-700 bg-gradient-to-br from-indigo-500 via-indigo-400 to-indigo-300 transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-400"
        >
          <h2 class="text-lg font-extrabold text-white mb-1 flex items-center gap-2 justify-center">
            <EyeIcon class="w-5 h-5 text-white opacity-80" />
            Regular Students
          </h2>
          <span class="text-sm text-teal-100 mb-2 text-center">
            <span class="font-semibold text-white text-xl">{{ props.regularStudents }}</span>
            <span class="ml-1">Student{{ props.regularStudents === 1 ? '' : 's' }} Total</span>
          </span>
        </Link>

        <!-- Online Students -->
        <Link
          :href="route('students.index', { study_mode: 3 })"
          class="flex flex-col items-center bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-pink-100 dark:border-gray-700 bg-gradient-to-br from-pink-400 via-pink-300 to-pink-200 transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-pink-400"
        >
          <h2 class="text-lg font-extrabold text-white mb-1 flex items-center gap-2 justify-center">
            <EyeIcon class="w-5 h-5 text-white opacity-80" />
            Online Students
          </h2>
          <span class="text-sm text-teal-100 mb-2 text-center">
            <span class="font-semibold text-white text-xl">{{ props.onlineStudents }}</span>
            <span class="ml-1">Student{{ props.onlineStudents === 1 ? '' : 's' }} Total</span>
          </span>
        </Link>

        <Link
          :href="route('students.index', { study_mode: 4 })"
          class="flex flex-col items-center bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-teal-100 dark:border-gray-700 bg-gradient-to-br from-teal-400 via-teal-300 to-teal-200 transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400"
        >
          <h2 class="text-lg font-extrabold text-white mb-1 flex items-center gap-2 justify-center">
            <EyeIcon class="w-5 h-5 text-white opacity-80" />
            Distance Students
          </h2>
          <span class="text-sm text-teal-100 mb-2 text-center">
            <span class="font-semibold text-white text-xl">{{ props.distanceStudents }}</span>
            <span class="ml-1">Student{{ props.distanceStudents === 1 ? '' : 's' }} Total</span>
          </span>
        </Link>

        <!-- Students Report -->
        <Link
          :href="route('students.report')"
          class="flex flex-col items-center bg-white dark:bg-gray-800 rounded-xl p-6 shadow border border-gray-300 dark:border-gray-700 bg-gradient-to-br from-gray-500 via-gray-400 to-gray-300 transition hover:scale-105 hover:shadow-2xl duration-200 focus:outline-none focus:ring-2 focus:ring-gray-400"
        >
          <h2 class="text-lg font-extrabold text-white mb-1 flex items-center gap-2 justify-center">
            <EyeIcon class="w-5 h-5 text-white opacity-80" />
            Students Report
          </h2>
        </Link>
      </div>
    </section>

    
    <!-- Change Students status -->
     

    <button
        v-if="userCan('change.status')"
        :key="`change-status-button`"
        class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-400 text-white font-semibold shadow hover:from-blue-700 hover:to-blue-500 transition flex items-center ml-2"
        @click="openChangeStatusModal"
      >
        <PlusIcon class="w-5 h-5 mr-1 inline" />
        Change Status
    </button>


    
    <!-- Create Course Status for a Student -->
    <Modal :show="showChangeStatusModal" @close="closeChangeStatusModal">
      <div class="p-6 space-y-6 bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-xl shadow-xl">
        <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-300 flex items-center gap-2">
          <PlusIcon class="w-6 h-6 text-blue-500 dark:text-blue-300" />
          Change Students Status
        </h2>
        <!-- Select Status -->
        <div>
          <form @submit.prevent="submitstatusCreate" enctype="multipart/form-data" class="flex flex-col items-center gap-4 w-full">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Status</label>
            <select v-model="statusCreateForm.status" class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" required>
              <option value="">Select a status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="graduated">Graduated</option>
            </select>
            <div class="w-full">
              <label
                for="file"
                class="block text-sm font-semibold text-blue-700 dark:text-blue-300 mb-2"
              >
                Choose Excel File
              </label>
              <input
                id="file"
                type="file"
                name="file"
                @change="handleFileUpload"
                required
                class="block w-full text-sm text-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition duration-200"
                accept=".xlsx,.xls,.csv"
              />
              <p v-if="statusCreateForm.errors.file" class="text-sm text-red-500 mt-1">
                {{ statusCreateForm.errors.file }}
              </p>
            </div>
            <button
              type="submit"
              class="bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition duration-300 ease-in-out text-lg flex items-center gap-2"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4"/>
              </svg>
              Import Students
            </button>
          </form>
        </div>
      </div>
    </Modal>
  </AppLayout>
</template>
