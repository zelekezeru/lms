<script setup>

import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";

import { Listbox, MultiSelect } from "primevue";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    PencilSquareIcon,
    PlusCircleIcon,
    BookOpenIcon,
    HomeModernIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});

const deleteStudent = (id) => {
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
            router.delete(route("students.destroy", { student: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The student has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};

const updateProfileImageModal = ref(false);

const selectedImagePreview = ref(null);

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

const profileImageForm = useForm({
    user_id: props.student.user.id,
    profile_image: null,
    // Hold the previous image preview if validation fails
    get _preview() {
        return selectedImagePreview.value || props.student.profileImg;
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
    profileImageForm.post(route('users.update.image', { user: props.student.user.id }), {
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
    <!-- student Image -->
    <div class="flex flex-col items-center mb-8 relative">
        <div
            v-if="!imageLoaded"
            class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
        ></div>
        
        <img
            v-show="imageLoaded"
            class="rounded-full w-44 h-44 object-contain bg-gray-400"
            :src="student.profileImg"
            :alt="`Logo of ` + student.name"
            @load="handleImageLoad"
        />
        
        <!-- Profile change icon (edit) below the image -->
        <button
            v-if="userCan && userCan('update-students')"
            class="mt-4 bg-white dark:bg-gray-800 rounded-full p-2 shadow hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            title="Change Profile Picture"
            @click="openUploadModal"
            type="button"
        >
            <PencilIcon class="w-5 h-5 text-gray-600 dark:text-gray-300" />
        </button>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Student ID</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ student.idNo }}
            </span>
        </div>
        <div class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Full Name</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ student.firstName }}
                {{ student.middleName }}
                {{ student.lastName }}
            </span>
        </div>
        <div v-if="student.mobilePhone" class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Mobile Phone</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ student.mobilePhone }}
            </span>
        </div>
        <div v-if="student.officePhone" class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Office Phone</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ student.officePhone }}
            </span>
        </div>
        <div v-if="student.dateOfBirth" class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Date of Birth</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ student.dateOfBirth }}
            </span>
        </div>
        <div v-if="student.maritalStatus" class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Marital Status</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ student.maritalStatus }}
            </span>
        </div>
        <div v-if="student.sex" class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Sex</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ student.sex }}
            </span>
        </div>
        <div v-if="student.user.email" class="flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
            <span class="text-lg font-medium text-gray-900 dark:text-gray-100 break-words max-w-full">
                {{ student.user.email }}
            </span>
        </div>
        <!-- Action Buttons: span full width on mobile, right on desktop -->
        <div class="sm:col-span-2 flex flex-col sm:flex-row justify-end mt-6 space-y-3 sm:space-y-0 sm:space-x-6">
            <div v-if="userCan('update-students')">
                <Link
                    :href="route('students.edit', { student: student.id })"
                    class="flex items-center space-x-1 text-blue-500 hover:text-blue-700"
                >
                    <PencilIcon class="w-5 h-5" />
                    <span>Edit</span>
                </Link>
            </div>
            <div v-if="userCan('delete-students')">
                <button
                    @click="deleteStudent(student.id)"
                    class="flex items-center space-x-1 text-red-500 hover:text-red-700"
                >
                    <TrashIcon class="w-5 h-5" />
                    <span>Delete</span>
                </button>
            </div>
        </div>
    </div>

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
</template>
