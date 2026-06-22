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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- ── Left Column: Profile Card ────────────────────────── -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gray-50 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-6 flex flex-col items-center text-center shadow-sm">
                <div class="relative group">
                    <div
                        v-if="!imageLoaded"
                        class="w-40 h-40 rounded-2xl bg-gray-200 dark:bg-gray-700 animate-pulse border-4 border-white dark:border-gray-800"
                    ></div>
                    <img
                        v-show="imageLoaded"
                        class="w-40 h-40 rounded-2xl object-cover border-4 border-white dark:border-gray-800 shadow-md bg-gray-100 dark:bg-gray-700 transition duration-300 group-hover:scale-[1.02]"
                        :src="student.profileImg"
                        :alt="`Logo of ` + student.firstName"
                        @load="handleImageLoad"
                    />
                    <!-- Overlay Upload Control -->
                    <button
                        v-if="userCan && userCan('update-students')"
                        @click="openUploadModal"
                        type="button"
                        class="absolute inset-0 w-40 h-40 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white text-xs font-semibold cursor-pointer border-4 border-transparent"
                    >
                        <PencilSquareIcon class="w-6 h-6 mb-1 text-white" />
                        Change Photo
                    </button>
                </div>

                <!-- Always visible trigger on mobile -->
                <button
                    v-if="userCan && userCan('update-students')"
                    @click="openUploadModal"
                    type="button"
                    class="mt-4 lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                >
                    <PencilIcon class="w-3 h-3" />
                    Update Profile Image
                </button>

                <div class="mt-4 space-y-1.5">
                    <h3 class="font-extrabold text-gray-900 dark:text-white text-lg">
                        {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
                    </h3>
                    <p class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                        {{ student.idNo }}
                    </p>
                </div>
            </div>
        </div>

        <!-- ── Right Column: Grouped Detail Cards ────────────────── -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Section: Personal Information -->
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700/60 pb-3">
                    <div class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <UsersIcon class="w-3.5 h-3.5" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Personal Information</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Full Name</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
                        </span>
                    </div>
                    <div v-if="student.sex" class="flex flex-col">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Gender</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ student.sex }}
                        </span>
                    </div>
                    <div v-if="student.dateOfBirth" class="flex flex-col">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Date of Birth</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ student.dateOfBirth }}
                        </span>
                    </div>
                    <div v-if="student.maritalStatus" class="flex flex-col">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Marital Status</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ student.maritalStatus }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section: Contact Details -->
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700/60 pb-3">
                    <div class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <HomeModernIcon class="w-3.5 h-3.5" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Contact Details</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div v-if="student.user?.email" class="flex flex-col sm:col-span-2">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Email Address</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5 break-all">
                            {{ student.user.email }}
                        </span>
                    </div>
                    <div v-if="student.mobilePhone" class="flex flex-col">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Mobile Phone</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ student.mobilePhone }}
                        </span>
                    </div>
                    <div v-if="student.officePhone" class="flex flex-col">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Office Phone</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ student.officePhone }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section: Institutional Information -->
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 dark:border-gray-700/60 pb-3">
                    <div class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <AcademicCapIcon class="w-3.5 h-3.5" />
                    </div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Institutional Details</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Student ID</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ student.idNo }}
                        </span>
                    </div>
                    <div v-if="student.centers?.[0]" class="flex flex-col">
                        <span class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">Center</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ student.centers[0].name }} Center
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="flex justify-end gap-3 pt-2">
                <Link
                    v-if="userCan('update-students')"
                    :href="route('students.edit', { student: student.id })"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-150 dark:border-indigo-800 rounded-xl text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100/70 transition shadow-sm"
                >
                    <PencilIcon class="w-3.5 h-3.5" />
                    Edit Profile
                </Link>
                <button
                    v-if="userCan('delete-students')"
                    @click="deleteStudent(student.id)"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/40 transition shadow-sm"
                >
                    <TrashIcon class="w-3.5 h-3.5" />
                    Delete Student
                </button>
            </div>
        </div>
    </div>

    <!-- ── Profile Image Update Modal ───────────────────────── -->
    <Modal :show="updateProfileImageModal" @close="closeProfileImageModal">
        <div class="p-6 space-y-6 bg-gradient-to-br from-white via-indigo-50/20 to-indigo-100/10 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <PencilSquareIcon class="w-5 h-5 text-indigo-500" />
                Update Profile Image
            </h2>

            <div class="space-y-4">
                <div class="flex items-center justify-center">
                    <img
                        v-if="selectedImagePreview"
                        :src="selectedImagePreview"
                        alt="Selected Preview"
                        class="w-32 h-32 rounded-2xl object-cover border-4 border-indigo-500 shadow-md transition-all duration-300"
                    />
                    <div v-else class="w-32 h-32 rounded-2xl bg-gray-100 dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center text-gray-400">
                        <PencilIcon class="w-8 h-8 mb-1" />
                        <span class="text-[10px]">No image selected</span>
                    </div>
                </div>

                <label class="block">
                    <span class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Choose a new profile picture</span>
                    <input
                        type="file"
                        accept="image/*"
                        @change="handleProfileImageChange"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-850 dark:file:text-indigo-300 dark:hover:file:bg-gray-700 transition"
                    />
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button
                    @click="closeProfileImageModal"
                    class="px-4 py-2 text-xs font-bold bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl text-gray-700 dark:text-gray-200 transition"
                >
                    Cancel
                </button>
                <button
                    @click="submitProfileImageUpdate"
                    :disabled="profileImageForm.processing"
                    class="inline-flex items-center px-4 py-2 text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <svg v-if="profileImageForm.processing" class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>
    </Modal>
</template>
