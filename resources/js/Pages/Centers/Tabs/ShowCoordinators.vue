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

const openCreateModal = () => {
    imagePreview.value = null;
    form.reset();
    form.center_id = props.center.id;
    showCreateModal.value = true;
};

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
        <h1 class="text-2xl font-bold mb-4 text-center">
            Coordinator Management
        </h1>

        <!-- Coordinator Info -->
        <div v-if="coordinator">
            <div class="flex justify-center mb-6">
                <div
                    class="rounded-full w-40 h-40 bg-gray-100 dark:bg-gray-700 overflow-hidden flex items-center justify-center"
                >
                    <img
                        v-if="coordinator.profile_img"
                        :src="coordinator.profile_img"
                        alt="Profile"
                        class="object-contain w-full h-full"
                    />
                    <PhotoIcon v-else class="w-24 h-24 text-gray-400" />
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500">Full Name</span>
                    <div class="text-lg font-medium">
                        {{ coordinator.name }}
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Email</span>
                    <div class="text-lg font-medium">
                        {{ coordinator.email }}
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Phone</span>
                    <div class="text-lg font-medium">
                        {{ coordinator.phone || "N/A" }}
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Status</span>
                    <div class="text-lg font-medium">
                        {{ coordinator.status }}
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

                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="btn-secondary"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="btn-primary"
                            :disabled="form.processing"
                        >
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

                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="btn-secondary"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="btn-primary"
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
