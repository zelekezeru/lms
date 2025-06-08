<script setup>
import { ref, computed } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { PencilIcon, PlusCircleIcon, TrashIcon, PhotoIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
  center: Object,
  coordinator: Object,
});

const showModal = ref(false);
const showEditModal = ref(false);
const imagePreview = ref(null);
const editingCoordinatorId = ref(null);

const form = useForm({
  name: "",
  phone: "",
  status: "Active",
  profile_img: "",
  center_id: props.center.id,
});

const editForm = useForm({
  name: "",
  phone: "",
  status: "Active",
  profile_img: "",
  center_id: props.center.id,
});

// Open modal for creating new coordinator
const openCreateModal = () => {
  imagePreview.value = null;
  form.reset();
  form.center_id = props.center.id;
  showModal.value = true;
};

// Open modal for editing existing coordinator
const openEditModal = (coordinator) => {
  form.coordinator_id = coordinator.id;
  form.name = coordinator.name;
  form.phone = coordinator.phone;
  form.status = coordinator.status || "Active";
  form.profile_img = ""; // Reset; only new image will update
  form.center_id = coordinator.center_id || props.center.id;
  imagePreview.value = coordinator.profile_img || null;
  showModal.value = true;
};

// Submit handler for both create and edit
const submitForm = () => {
  const routeName = route("coordinators.store");

  const method = form.post;

  method(routeName, {
    onSuccess: () => {
      Swal.fire("Success!", "Coordinator created.", "success");
      showModal.value = false;
      form.reset();
    },
    onError: () => {
      Swal.fire("Error", "Please check the form for errors.", "error");
    },
  });
};

// Submit handler for both create and edit
const submitEditForm = () => {
  const routeName = route("coordinators.update", props.coordinator.id)

  const method = form.put;


  method(routeName, {
    onSuccess: () => {
      Swal.fire("Success!", "Coordinator updated.", "success");
      showModal.value = false;
      form.reset();
    },
    onError: () => {
      Swal.fire("Error", "Please check the form for errors.", "error");
    },
  });
};

// File input handling
const handleFileChange = (e) => {
  const file = e.target.files[0];
  form.profile_img = file;

  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  } else {
    imagePreview.value = props.coordinator?.profile_img || null;
  }
};
</script>

<template>
  <div class="p-8 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-4 text-center">Coordinator Management</h1>
    <div class="max-w-5xl mx-auto p-6">

      <!-- Coordinator Info -->
      <div v-if="coordinator">
        <div class="flex justify-center mb-6">
          <div class="rounded-full w-40 h-40 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
            <template v-if="coordinator.profile_img">
              <img
          class="w-full h-full object-contain"
          :src="coordinator.profile_img"
          :alt="'Photo of ' + coordinator.name"
              />
            </template>
            <template v-else>
              <PhotoIcon class="w-24 h-24 text-gray-400" />
            </template>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <span class="text-sm text-gray-500">ID</span>
            <div class="text-lg font-medium">{{ coordinator.userId }}</div>
          </div>
          <div>
            <span class="text-sm text-gray-500">Full Name</span>
            <div class="text-lg font-medium">{{ coordinator.name }}</div>
          </div>
          <div>
            <span class="text-sm text-gray-500">Email</span>
            <div class="text-lg font-medium">{{ coordinator.email }}</div>
          </div>
          <div>
            <span class="text-sm text-gray-500">Phone</span>
            <div class="text-lg font-medium">{{ coordinator.phone || 'N/A' }}</div>
          </div>
          <div>
            <span class="text-sm text-gray-500">Role</span>
            <div class="text-lg font-medium">Coordinator</div>
          </div>
        </div>

        <div class="flex justify-end mt-6 space-x-6">
          <button
            v-if="userCan('update-coordinators')"
            @click="openEditModal(coordinator)"
            class="text-blue-500 hover:text-blue-700 flex items-center gap-1"
          >
            <PencilIcon class="w-5 h-5" />
            Edit
          </button>
          <button
            v-if="userCan('delete-coordinators')"
            @click="deleteEmployee(coordinator.id)"
            class="text-red-500 hover:text-red-700 flex items-center gap-1"
          >
            <TrashIcon class="w-5 h-5" />
            Delete
          </button>
        </div>
      </div>

      <!-- No Coordinator View -->
      <div
        v-else
        class="flex flex-col items-center justify-center p-10 bg-white dark:bg-gray-800 rounded-lg shadow-md"
      >
        <PlusCircleIcon class="w-16 h-16 text-green-400 mb-4 animate-bounce" />
        <div class="text-xl font-semibold mb-2">No coordinator found</div>
        <div class="text-gray-500 mb-6">Add coordinator to this center.</div>
        <button
          @click="openEditModal"
          class="px-6 py-3 bg-green-600 text-white rounded-full flex items-center gap-2"
        >
          <PlusCircleIcon class="w-5 h-5" />
          Add Coordinator
        </button>
      </div>

      <!-- Create Modal -->
      <Modal :show="showModal" @close="showModal = false">
        <div class="p-6 space-y-4">
          <h2 class="text-lg font-bold">
            {{ "New Coordinator" }}
          </h2>
          <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Name -->
            <div>
              <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-300">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border px-3 py-2 rounded bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
                placeholder="Full name"
              />
              <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
            </div>

            <!-- Phone -->
            <div>
              <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-300">Phone</label>
              <input
                v-model="form.phone"
                type="text"
                class="w-full border px-3 py-2 rounded bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
                placeholder="Phone number"
              />
              <div v-if="form.errors.phone" class="text-red-500 text-sm">{{ form.errors.phone }}</div>
            </div>

            <!-- Profile Image -->
            <div>
              <InputLabel for="profile_img" value="Profile Image" class="dark:text-gray-300" />
              <div class="flex items-center gap-4">
                <label for="profile_img"
                  class="cursor-pointer px-4 py-2 bg-black text-white flex items-center gap-2 rounded">
                  <PhotoIcon class="w-5 h-5" />
                  Upload Image
                </label>
                <input id="profile_img" type="file" accept="image/*" class="hidden" @change="handleFileChange" />
                <div class="w-16 h-16 rounded-full border shadow bg-gray-100 dark:bg-gray-700 overflow-hidden">
                  <template v-if="imagePreview">
                    <img :src="imagePreview" class="object-cover w-full h-full" />
                  </template>
                </div>
              </div>
              <InputError :message="form.errors.profile_img" />
            </div>

            <!-- Status -->
            <div>
              <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-300">Status</label>
              <select
                v-model="form.status"
                class="w-full border px-3 py-2 rounded bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
              >
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
              <div v-if="form.errors.status" class="text-red-500 text-sm">{{ form.errors.status }}</div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showModal = false"
                class="px-4 py-2 bg-gray-400 text-white rounded dark:bg-gray-600 dark:hover:bg-gray-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                :disabled="form.processing"
              >
                {{ form.processing ? "Saving..." : "Save" }}
              </button>
            </div>

          </form>
        </div>
      </Modal>
       
      <!-- Create Modal -->
      <Modal :show="showModal" @close="showModal = false">
        <div class="p-6 space-y-4">
          <h2 class="text-lg font-bold">
            {{ "Edit Coordinator" }}
          </h2>
          <form @submit.prevent="submitEditForm" class="space-y-4">
            <!-- Name -->
            <div>
              <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-300">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border px-3 py-2 rounded bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
                placeholder="Full name"
              />
              <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
            </div>

            <!-- Phone -->
            <div>
              <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-300">Phone</label>
              <input
                v-model="form.phone"
                type="text"
                class="w-full border px-3 py-2 rounded bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
                placeholder="Phone number"
              />
              <div v-if="form.errors.phone" class="text-red-500 text-sm">{{ form.errors.phone }}</div>
            </div>

            <!-- Profile Image -->
            <div>
              <InputLabel for="profile_img" value="Profile Image" class="dark:text-gray-300" />
              <div class="flex items-center gap-4">
                <label for="profile_img"
                  class="cursor-pointer px-4 py-2 bg-black text-white flex items-center gap-2 rounded">
                  <PhotoIcon class="w-5 h-5" />
                  Upload Image
                </label>
                <input id="profile_img" type="file" accept="image/*" class="hidden" @change="handleFileChange" />
                <div class="w-16 h-16 rounded-full border shadow bg-gray-100 dark:bg-gray-700 overflow-hidden">
                  <template v-if="imagePreview">
                    <img :src="imagePreview" class="object-cover w-full h-full" />
                  </template>
                </div>
              </div>
              <InputError :message="form.errors.profile_img" />
            </div>

            <!-- Status -->
            <div>
              <label class="block mb-1 font-semibold text-gray-700 dark:text-gray-300">Status</label>
              <select
                v-model="form.status"
                class="w-full border px-3 py-2 rounded bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
              >
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
              <div v-if="form.errors.status" class="text-red-500 text-sm">{{ form.errors.status }}</div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showModal = false"
                class="px-4 py-2 bg-gray-400 text-white rounded dark:bg-gray-600 dark:hover:bg-gray-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
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
