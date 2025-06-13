<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import {
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/solid";

// Define the props for the user
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const imageLoaded = ref(false);

const handleImageLoad = () => {
    imageLoaded.value = true;
};

const updateProfileImageModal = ref(false);
const selectedImagePreview = ref(null);

const profileImageForm = useForm({
    user_id: props.user.id,
    profile_img: null,
    get _preview() {
        return selectedImagePreview.value || props.user.profileImg;
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
        profileImageForm.profile_img = file;
        selectedImagePreview.value = URL.createObjectURL(file);
    }
};

const submitProfileImageUpdate = () => {
    profileImageForm.post(route('users.update.image', { user: props.user.id }), {
        onSuccess: () => {
            Swal.fire("Success!", "Profile image updated.", "success");
            closeProfileImageModal();
        },
        onError: () => {
            Swal.fire("Error!", "Failed to update image.", "error");
        },
    });
};

// Delete function with SweetAlert confirmation
const deleteUser = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("users.destroy", { user: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The User has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-8xl mx-auto p-6">
            <h1 class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center">
                User Details
            </h1>
            <div class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700">
                <!-- User Image -->
                <div class="flex flex-col items-center mb-8 relative">
                    <div
                        v-if="!imageLoaded"
                        class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                    ></div>
                    <img
                        v-show="imageLoaded"
                        class="rounded-full w-44 h-44 object-contain bg-gray-400"
                        :src="profileImageForm._preview"
                        :alt="`Profile of ` + user.name"
                        @load="handleImageLoad"
                    />
                    <!-- Profile change icon (edit) below the image -->
                    <button
                        v-if="userCan && userCan('update-users')"
                        class="mt-4 bg-white dark:bg-gray-800 rounded-full p-2 shadow hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                        title="Change Profile Picture"
                        @click="openUploadModal"
                        type="button"
                    >
                        <PencilIcon class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <div class="mt-8 pt-4 pb-4">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Name -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Name</span>
                                <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ user.name }}
                                </span>
                            </div>
                            <!-- Email -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                                <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ user.email }}
                                </span>
                            </div>
                            <!-- Phone -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Phone Number</span>
                                <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ user.phone }}
                                </span>
                            </div>
                            <!-- User ID -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">User ID</span>
                                <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ user.user_uuid }}
                                </span>
                            </div>
                            <!-- Status -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                                <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    <span v-if="user.status == 0" class="text-red-500">Inactive</span>
                                    <span v-else class="text-green-500">Active</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Edit and Delete Buttons -->
                <div class="flex justify-end mt-6 space-x-6">
                    <Link
                        v-if="userCan('update-users')"
                        :href="route('users.edit', { user: user.id })"
                        class="text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                        <span>Edit</span>
                    </Link>
                    <button
                        v-if="userCan('delete-users')"
                        @click="deleteUser(user.id)"
                        class="text-red-500 hover:text-red-700"
                    >
                        <TrashIcon class="w-5 h-5" />
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Profile Image Modal -->
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
                        class="px-5 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitProfileImageUpdate"
                        :disabled="profileImageForm.processing"
                        class="px-5 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-400 text-white font-semibold shadow hover:from-blue-700 hover:to-blue-500 transition disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <span v-if="profileImageForm.processing" class="animate-spin mr-2">
                            <svg class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </span>
                        Save
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
