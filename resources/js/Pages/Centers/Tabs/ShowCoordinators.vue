<script setup>
import { ref } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {
    PencilIcon,
    PlusCircleIcon,
    TrashIcon,
    PhotoIcon,
} from "@heroicons/vue/24/outline";

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
    center_id: props.center && props.center.id ? props.center.id : "",
});

const editForm = useForm({
    name: "",
    phone: "",
    status: "Active",
    center_id: props.center && props.center.id ? props.center.id : "",
});

const openCreateModal = () => {
    imagePreview.value = null;
    form.reset();
    form.center_id = props.center && props.center.id ? props.center.id : "";
    showCreateModal.value = true;
};

const openEditModal = (coordinator) => {
    imagePreview.value = coordinator?.profile_img || null;
    editForm.reset();
    editForm.name = coordinator?.name;
    editForm.phone = coordinator?.phone;
    editForm.status = coordinator?.status || "Active";
    editForm.profile_img = "";
    editForm.center_id = coordinator?.center_id || (props.center && props.center.id ? props.center.id : "");
    showEditModal.value = true;
};

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

const submitEditForm = () => {
    editForm.post(route("coordinators.update", { coordinator: props.coordinator?.id }), {
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

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

const updateProfileImageModal = ref(false);
const selectedImagePreview = ref(null);

const profileImageForm = useForm({
    user_id: props.coordinator?.id,
    profile_image: null,
    // Hold the previous image preview if validation fails
    get _preview() {
        return selectedImagePreview.value || props.coordinator?.profileImg;
    },
});

const openUploadModal = () => {
    updateProfileImageModal.value = true;
};

const closeProfileImageModal = () => {
    updateProfileImageModal.value = false;
    profileImageForm.reset();
    selectedImagePreview.value = null;
};

const handleProfileImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        profileImageForm.profile_image = file;
        selectedImagePreview.value = URL.createObjectURL(file); // Preview
    }
};

const submitProfileImageUpdate = () => {
    profileImageForm.post(route('users.update.image', { user: props.coordinator?.id }), {
        onSuccess: () => {
            Swal.fire("Success!", "Profile image updated.", "success");
            closeProfileImageModal();
        },
        onError: () => {
            Swal.fire("Error!", "Failed to update image.", "error");
        },
    });
};
</script>

<template>
    <div class="p-8 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-4 text-center">
            Coordinator Management
        </h1>
        
        <!-- Coordinator Info -->
        <div v-if="coordinator">
            <!-- coordinator Image -->
            <div class="flex flex-col items-center mb-8 relative">
                <div
                    v-if="!imageLoaded"
                    class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                ></div>
                
                <img
                    v-show="imageLoaded"
                    class="rounded-full w-44 h-44 object-contain bg-gray-400"
                    :src="coordinator?.profileImg"
                    :alt="`Logo of ` + coordinator?.name"
                    @load="handleImageLoad"
                />
                
                <!-- Profile change icon (edit) below the image -->
                <button
                    v-if="userCan && userCan('update-coordinators')"
                    class="mt-4 bg-white dark:bg-gray-800 rounded-full p-2 shadow hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                    title="Change Profile Picture"
                    @click="openUploadModal"
                    type="button"
                >
                    <PencilIcon class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                </button>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500">Full Name</span>
                    <div class="text-lg font-medium">
                        {{ coordinator?.name }}
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Email</span>
                    <div class="text-lg font-medium">
                        {{ coordinator?.email }}
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Phone</span>
                    <div class="text-lg font-medium">
                        {{ coordinator?.phone || "N/A" }}
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Status</span>
                    <div class="text-lg font-medium">
                        {{ coordinator?.status }}
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6 space-x-6">
                <button
                    @click="openEditModal(coordinator)"
                    class="text-blue-500 hover:text-blue-700 flex items-center gap-1"
                >
                    <PencilIcon class="w-5 h-5" /> Edit
                </button>
            </div>
        </div>

        <!-- If No Coordinator -->
        <div v-else class="text-center mt-10">
            <PlusCircleIcon
                class="w-16 h-16 text-green-400 mb-4 animate-bounce"
            />
            <p class="mb-2 font-semibold">No coordinator assigned</p>
            <button
                @click="openCreateModal"
                class="px-6 py-3 bg-green-600 text-white rounded-full flex items-center gap-2"
            >
                <PlusCircleIcon class="w-5 h-5" /> Add Coordinator
            </button>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6 space-y-4">
                <h2 class="text-lg font-bold">New Coordinator</h2>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Name
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Full Name"
                        />
                        <div v-if="form.errors.name" class="text-red-500 text-sm">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Phone
                        </label>
                        <input
                            id="phone"
                            v-model="form.phone"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Phone Number"
                        />
                        <div v-if="form.errors.phone" class="text-red-500 text-sm">
                            {{ form.errors.phone }}
                        </div>
                    </div>

                    <div>
                        <label for="profile_img_create" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Profile Image
                        </label>
                        <input
                            id="profile_img_create"
                            type="file"
                            accept="image/*"
                            class="mt-1 block w-full text-gray-700 dark:text-gray-200"
                            @change="(e) => handleFileChange(e, form)"
                        />
                        <div v-if="imagePreview" class="mt-2">
                            <img
                                :src="imagePreview"
                                class="w-16 h-16 rounded-full object-cover"
                            />
                        </div>
                        <div v-if="form.errors.profile_img" class="text-red-500 text-sm">
                            {{ form.errors.profile_img }}
                        </div>
                    </div>

                    <div>
                        <label for="status_create" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Status
                        </label>
                        <select
                            id="status_create"
                            v-model="form.status"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        <div v-if="form.errors.status" class="text-red-500 text-sm">
                            {{ form.errors.status }}
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="relative px-6 py-2 rounded-lg bg-gradient-to-r from-gray-200 via-gray-300 to-gray-400 dark:from-gray-700 dark:via-gray-800 dark:to-gray-900 text-gray-800 dark:text-gray-100 font-semibold shadow-md hover:from-gray-300 hover:to-gray-500 dark:hover:from-gray-600 dark:hover:to-gray-800 transition-all duration-200 group overflow-hidden"
                        >
                            <span class="absolute left-0 top-0 w-0 h-full bg-gray-400 dark:bg-gray-600 opacity-20 group-hover:w-full transition-all duration-300"></span>
                            <span class="relative z-10">Cancel</span>
                        </button>
                        <button
                            type="submit"
                            class="relative px-6 py-2 rounded-lg bg-gradient-to-r from-green-600 via-green-500 to-green-400 text-white font-semibold shadow-lg hover:from-green-700 hover:via-green-600 hover:to-green-500 transition-all duration-200 group overflow-hidden disabled:opacity-60 disabled:cursor-not-allowed"
                            :disabled="form.processing"
                        >
                            <span class="absolute left-0 top-0 w-0 h-full bg-white opacity-10 group-hover:w-full transition-all duration-300"></span>
                            <span class="relative z-10 flex items-center justify-center">
                                <span v-if="form.processing" class="animate-spin mr-2">
                                    <svg class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                </span>
                                {{ form.processing ? "Saving..." : "Save" }}
                            </span>
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
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Name
                        </label>
                        <input
                            id="edit_name"
                            v-model="editForm.name"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Full Name"
                        />
                        <div v-if="editForm.errors.name" class="text-red-500 text-sm">
                            {{ editForm.errors.name }}
                        </div>
                    </div>

                    <div>
                        <label for="edit_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Phone
                        </label>
                        <input
                            id="edit_phone"
                            v-model="editForm.phone"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Phone Number"
                        />
                        <div v-if="editForm.errors.phone" class="text-red-500 text-sm">
                            {{ editForm.errors.phone }}
                        </div>
                    </div>

                    <div>
                        <label for="profile_img_edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Profile Image
                        </label>
                        <input
                            id="profile_img_edit"
                            type="file"
                            accept="image/*"
                            class="mt-1 block w-full text-gray-700 dark:text-gray-200"
                            @change="(e) => handleFileChange(e, editForm)"
                        />
                        <div v-if="imagePreview" class="mt-2">
                            <img
                                :src="imagePreview"
                                class="w-16 h-16 rounded-full object-cover"
                            />
                        </div>
                        <div v-if="editForm.errors.profile_img" class="text-red-500 text-sm">
                            {{ editForm.errors.profile_img }}
                        </div>
                    </div>

                    <div>
                        <label for="status_edit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Status
                        </label>
                        <select
                            id="status_edit"
                            v-model="editForm.status"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        <div v-if="editForm.errors.status" class="text-red-500 text-sm">
                            {{ editForm.errors.status }}
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="relative px-6 py-2 rounded-lg bg-gradient-to-r from-gray-200 via-gray-300 to-gray-400 dark:from-gray-700 dark:via-gray-800 dark:to-gray-900 text-gray-800 dark:text-gray-100 font-semibold shadow-md hover:from-gray-300 hover:to-gray-500 dark:hover:from-gray-600 dark:hover:to-gray-800 transition-all duration-200 group overflow-hidden"
                        >
                            <span class="absolute left-0 top-0 w-0 h-full bg-gray-400 dark:bg-gray-600 opacity-20 group-hover:w-full transition-all duration-300"></span>
                            <span class="relative z-10">Cancel</span>
                        </button>
                        <button
                            type="submit"
                            class="relative px-6 py-2 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 text-white font-semibold shadow-lg hover:from-blue-700 hover:via-blue-600 hover:to-blue-500 transition-all duration-200 group overflow-hidden disabled:opacity-60 disabled:cursor-not-allowed"
                            :disabled="editForm.processing"
                        >
                            <span class="absolute left-0 top-0 w-0 h-full bg-white opacity-10 group-hover:w-full transition-all duration-300"></span>
                            <span class="relative z-10 flex items-center justify-center">
                                <span v-if="editForm.processing" class="animate-spin mr-2">
                                    <svg class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                </span>
                                {{ editForm.processing ? "Updating..." : "Update" }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
        
        <Modal :show="updateProfileImageModal" @close="closeProfileImageModal">
            <div class="p-6 space-y-6 bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-xl shadow-xl">
                <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-300 flex items-center gap-2">
                    <PencilIcon class="w-6 h-6 text-blue-500 dark:text-blue-300" />
                    Update Profile Image
                </h2>

                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Choose a new profile picture</span>
                    <input
                        type="file"
                        accept="image/*"
                        @change="handleProfileImageChange"
                        class="block w-full text-sm text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100
                            dark:file:bg-gray-700 dark:file:text-blue-200 dark:hover:file:bg-gray-600"
                    />
                </label>

                <!-- Preview selected image -->
                <div v-if="selectedImagePreview" class="flex justify-center">
                    <img
                        :src="selectedImagePreview"
                        alt="Selected Preview"
                        class="w-32 h-32 rounded-full object-cover border-4 border-blue-300 shadow-lg transition-all duration-300"
                    />
                </div>
                <div v-else class="flex justify-center">
                    <div class="w-32 h-32 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-500 text-4xl">
                        <PencilIcon class="w-12 h-12" />
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button
                        @click="closeProfileImageModal"
                        class="relative px-6 py-2 rounded-lg bg-gradient-to-r from-gray-200 via-gray-300 to-gray-400 dark:from-gray-700 dark:via-gray-800 dark:to-gray-900 text-gray-800 dark:text-gray-100 font-semibold shadow-md hover:from-gray-300 hover:to-gray-500 dark:hover:from-gray-600 dark:hover:to-gray-800 transition-all duration-200 group overflow-hidden"
                    >
                        <span class="absolute left-0 top-0 w-0 h-full bg-gray-400 dark:bg-gray-600 opacity-20 group-hover:w-full transition-all duration-300"></span>
                        <span class="relative z-10">Cancel</span>
                    </button>

                    <button
                        @click="submitProfileImageUpdate"
                        :disabled="profileImageForm.processing"
                        class="relative px-6 py-2 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 text-white font-semibold shadow-lg hover:from-blue-700 hover:via-blue-600 hover:to-blue-500 transition-all duration-200 group overflow-hidden disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <span class="absolute left-0 top-0 w-0 h-full bg-white opacity-10 group-hover:w-full transition-all duration-300"></span>
                        <span class="relative z-10 flex items-center justify-center">
                            <span v-if="profileImageForm.processing" class="animate-spin mr-2">
                                <svg class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </span>
                            Save
                        </span>
                    </button>
                </div>
            </div>
        </Modal>
    </div>
</template>
