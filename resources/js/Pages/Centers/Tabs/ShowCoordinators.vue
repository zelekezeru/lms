<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Modal from "@/Components/Modal.vue";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    center: Object,
    coordinator: Object,
});

// Modal toggle
const showModal = ref(false);

// Form state
const form = useForm({
  name: "",
  phone: "",
  status: "Active",
  profile_img: "",
  center_id: props.center.id,
});

// Submit handler
const submitForm = () => {
  form.post(route("coordinators.store"), {
    onSuccess: () => {
      Swal.fire("Success!", "Coordinator created successfully.", "success");
      showModal.value = false;
      form.reset();
    },
    onError: () => {
      Swal.fire("Error", "Please check the form for errors.", "error");
    },
  });
};
</script>

<template>
  <div class="p-8 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-4 text-center">Coordinator Management</h1>
    
    <div class="max-w-5xl mx-auto p-6">
    
    <div v-if="coordinator">
        <div class="flex justify-center mb-6">
            <div v-if="!imageLoaded" class="rounded-full w-40 h-40 bg-gray-300 dark:bg-gray-600 animate-pulse"></div>
            <img
                v-show="imageLoaded"
                class="rounded-full w-40 h-40 object-contain"
                :src="profileImage"
                :alt="`Photo of ` + (coordinator.firstName || 'Coordinator')"
                @load="handleImageLoad"
            />
        </div>
        
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">ID</span>
                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ coordinator.user_uuid || 'N/A' }}
                </div>
            </div>

            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Full Name</span>
                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ coordinator.name }}
                </div>
            </div>

            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ coordinator.email || 'N/A' }}
                </div>
            </div>

            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ coordinator.phone ?? 'N/A' }}
                </div>
            </div>

            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Role</span>
                <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Coordinator
                </div>
            </div>

            <div v-if="userCan('view-password') && coordinator.default_password">
              <span class="text-sm text-gray-500 dark:text-gray-400">Default Password</span>
              <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ coordinator.default_password }}
              </div>
            </div>
        </div>

        <div class="flex justify-end mt-6 space-x-4">
            <Link
                v-if="userCan('update-coordinators')"
                :href="route('coordinators.edit', { coordinator: coordinator.id })"
                class="text-blue-600 hover:text-blue-800"
            >
                <PencilIcon class="w-5 h-5 inline-block mr-1" />
                Edit
            </Link>

            <button
                v-if="userCan('delete-coordinators')"
                @click="deleteCoordinator(coordinator.id)"
                class="text-red-600 hover:text-red-800"
            >
                <TrashIcon class="w-5 h-5 inline-block mr-1" />
                Delete
            </button>
        </div>
    </div>

    <div v-else class="flex flex-col items-center justify-center p-10 bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
      <PlusCircleIcon class="w-16 h-16 text-green-400 mb-4 animate-bounce" />
      <div class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">
        {{ $t('coordinators.no_coordinators_found') }}
      </div>
      <div class="text-gray-500 dark:text-gray-400 mb-6">
        Add coordinator to this center.
      </div>
      <button
        @click="showModal = true"
        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold rounded-full shadow-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-400"
      >
        <PlusCircleIcon class="w-6 h-6 mr-2" />
        Add Coordinator
      </button>
    </div>

    <!-- Modal -->
    <Modal :show="showModal" @close="showModal = false">
      <div class="p-6 space-y-4">
        <h2 class="text-lg font-bold">New Coordinator</h2>
        <form @submit.prevent="submitForm" class="space-y-4">

          <div>
            <label class="block mb-1 font-semibold">Name</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full border px-3 py-2 rounded"
              placeholder="Full name"
            />
            <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
          </div>

          <div>
            <label class="block mb-1 font-semibold">Phone</label>
            <input
              v-model="form.phone"
              type="text"
              class="w-full border px-3 py-2 rounded"
              placeholder="Phone number"
            />
            <div v-if="form.errors.phone" class="text-red-500 text-sm">{{ form.errors.phone }}</div>
          </div>
          
           <!-- Profile Image Upload & Preview -->
            <div>
                <InputLabel for="profile_img" value="Profile Image" />
                <div class="flex items-center gap-4">
                    <label
                        for="profile_img"
                        class="cursor-pointer px-4 py-2 text-white flex items-center gap-2 rounded-md shadow transition bg-black hover:bg-blue-700"
                    >
                        <PhotoIcon class="w-5 h-5" /> Upload Image
                    </label>
                    <input
                        id="profile_img"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleFileChange"
                    />
                    <div
                        v-if="form.imagePreview"
                        class="w-16 h-16 rounded-full border shadow overflow-hidden"
                    >
                        <img
                            :src="form.imagePreview"
                            alt="Profile Preview"
                            class="object-cover w-full h-full"
                        />
                    </div>
                </div>
                <InputError :message="form.errors.profile_img" />
            </div>

          <div>
            <label class="block mb-1 font-semibold">Status</label>
            <select
              v-model="form.status"
              class="w-full border px-3 py-2 rounded"
            >
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
            <div v-if="form.errors.status" class="text-red-500 text-sm">{{ form.errors.status }}</div>
          </div>

          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2 bg-gray-400 text-white rounded"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-green-600 text-white rounded"
              :disabled="form.processing"
            >
              {{ form.processing ? "Saving..." : "Save" }}
            </button>
          </div>

        </form>
      </div>
    </Modal>
      </div>
  </div>
</template>
