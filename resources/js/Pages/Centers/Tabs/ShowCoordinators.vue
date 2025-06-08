<script setup>
import { ref } from "vue";
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

const showCreateModal = ref(false);
const showEditModal = ref(false);
const imagePreview = ref(null);

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

// Open Create Modal
const openCreateModal = () => {
  imagePreview.value = null;
  form.reset();
  form.center_id = props.center.id;
  showCreateModal.value = true;
};

// Open Edit Modal
const openEditModal = (coordinator) => {
  imagePreview.value = coordinator.profile_img || null;
  editForm.reset();
  editForm.name = coordinator.name;
  editForm.phone = coordinator.phone;
  editForm.status = coordinator.status || "Active";
  editForm.profile_img = "";
  editForm.center_id = coordinator.center_id || props.center.id;
  showEditModal.value = true;
};

// Submit Create Form
const submitForm = () => {
  form.post(route("coordinators.store"), {
    onSuccess: () => {
      Swal.fire("Success!", "Coordinator created.", "success");
      showCreateModal.value = false;
      form.reset();
    },
    onError: () => {
      Swal.fire("Error", "Please check the form for errors.", "error");
    },
  });
};

// Submit Edit Form
const submitEditForm = () => {
  editForm.post(route("coordinators.update", props.coordinator.id), {
    onSuccess: () => {
      Swal.fire("Success!", "Coordinator updated.", "success");
      showEditModal.value = false;
      editForm.reset();
    },
    onError: () => {
      Swal.fire("Error", "Please check the form for errors.", "error");
    },
  });
};

// File input handling
const handleFileChange = (e, targetForm) => {
  const file = e.target.files[0];
  targetForm.profile_img = file;

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

    <!-- Coordinator Info -->
    <div v-if="coordinator">
      <div class="flex justify-center mb-6">
        <div class="rounded-full w-40 h-40 bg-gray-100 dark:bg-gray-700 overflow-hidden flex items-center justify-center">
          <img v-if="coordinator.profile_img" :src="coordinator.profile_img" alt="Profile" class="object-contain w-full h-full" />
          <PhotoIcon v-else class="w-24 h-24 text-gray-400" />
        </div>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div><span class="text-sm text-gray-500">Full Name</span><div class="text-lg font-medium">{{ coordinator.name }}</div></div>
        <div><span class="text-sm text-gray-500">Email</span><div class="text-lg font-medium">{{ coordinator.email }}</div></div>
        <div><span class="text-sm text-gray-500">Phone</span><div class="text-lg font-medium">{{ coordinator.phone || 'N/A' }}</div></div>
        <div><span class="text-sm text-gray-500">Status</span><div class="text-lg font-medium">{{ coordinator.status }}</div></div>
      </div>

      <div class="flex justify-end mt-6 space-x-6">
        <button @click="openEditModal(coordinator)" class="text-blue-500 hover:text-blue-700 flex items-center gap-1">
          <PencilIcon class="w-5 h-5" /> Edit
        </button>
      </div>
    </div>

    <!-- If No Coordinator -->
    <div v-else class="text-center mt-10">
      <PlusCircleIcon class="w-16 h-16 text-green-400 mb-4 animate-bounce" />
      <p class="mb-2 font-semibold">No coordinator assigned</p>
      <button @click="openCreateModal" class="px-6 py-3 bg-green-600 text-white rounded-full flex items-center gap-2">
        <PlusCircleIcon class="w-5 h-5" /> Add Coordinator
      </button>
    </div>

    <!-- Create Modal -->
    <Modal :show="showCreateModal" @close="showCreateModal = false">
      <div class="p-6 space-y-4">
        <h2 class="text-lg font-bold">New Coordinator</h2>
        <form @submit.prevent="submitForm" class="space-y-4">
          <!-- Name -->
          <div>
            <InputLabel value="Name" />
            <input v-model="form.name" type="text" class="input" placeholder="Full Name" />
            <InputError :message="form.errors.name" />
          </div>

          <!-- Phone -->
          <div>
            <InputLabel value="Phone" />
            <input v-model="form.phone" type="text" class="input" placeholder="Phone Number" />
            <InputError :message="form.errors.phone" />
          </div>

          <!-- Image -->
          <div>
            <InputLabel value="Profile Image" />
            <input type="file" accept="image/*" @change="e => handleFileChange(e, form)" />
            <div v-if="imagePreview" class="mt-2">
              <img :src="imagePreview" class="w-16 h-16 rounded-full object-cover" />
            </div>
            <InputError :message="form.errors.profile_img" />
          </div>

          <!-- Status -->
          <div>
            <InputLabel value="Status" />
            <select v-model="form.status" class="input">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
            <InputError :message="form.errors.status" />
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-2">
            <button type="button" @click="showCreateModal = false" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary" :disabled="form.processing">
              {{ form.processing ? "Saving..." : "Save" }}
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Edit Modal -->
    <Modal :show="showEditModal" @close="showEditModal = false">
      <div class="p-6 space-y-4">
        <h2 class="text-lg font-bold">Edit Coordinator</h2>
        <form @submit.prevent="submitEditForm" class="space-y-4">
          <!-- Name -->
        </div>
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
          <div class="flex justify-end space-x-2">
            <button 
              type="button" 
              @click="showEditModal = false" 
              class="btn-secondary dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              class="btn-primary dark:bg-blue-600 dark:hover:bg-blue-500 dark:text-white"
              :disabled="editForm.processing"
            >
              {{ editForm.processing ? "Updating..." : "Update" }}
            </button>
          </div>
        </form>

      </div>
    </Modal>
  </div>
</template>
