<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref, computed } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import InputError from '@/Components/InputError.vue';
import { PencilIcon, TrashIcon, CogIcon, PencilSquareIcon, XMarkIcon, PlusCircleIcon} from "@heroicons/vue/24/solid";


const props = defineProps({
    center: {
        type: Object,
        required: true,
    },
    coordinator: {
        type: Object,
        required: true,
    },
});

const imageLoaded = ref(false);
const handleImageLoad = () => {
    imageLoaded.value = true;
};

// Fallback image if coordinator.profileImg is missing or null
const profileImage = computed(() => {
    return props.coordinator?.profileImg || '/default-profile.png';
});

// Delete coordinator with confirmation
const deleteCoordinator = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("coordinators.destroy", { coordinator: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "Coordinator has been removed.", "success");
                },
            });
        }
    });
};

const createCoordinatorModal = ref(false);
// Form for assigning payments
const paymentForm = useForm({
    center_id:  props.center.id, // Set student ID here
});

// Form for creating new coordinator
const coordinatorCreationForm = useForm({
    name: null,
    phone: null,
    status: null,
});

const closeCoordinator = () => {
    assignCoordinators.value = false;
    coordinatorForm.reset();
};

const closeCreateCoordinatorModal = () => {
    createCoordinatorModal.value = false;
    coordinatorCreationForm.reset();
};

const submitNewCoordinator = () => {
    coordinatorCreationForm.post(route('coordinators.store'), {
        onSuccess: () => {
            Swal.fire("Success!", "Coordinator has been created successfully.", "success");
            createCoordinatorModal.value = false;
            coordinatorCreationForm.reset();
        },
        onError: (errors) => {
            Swal.fire("Error!", "Failed to create the coordinator. Please check the form for errors.", "error");
            console.log(errors); // Log errors for debugging
        },
    });
};
</script>

<template>
    <div class="max-w-5xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                Current Coordinator Details
            </h3>
        </div>

        <div  class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-lg p-6">
            <button
                @click="createCoordinatorModal = !createCoordinatorModal"
                class="flex text-green-600 hover:text-green-800">
                <PlusCircleIcon class="mx-2 w-8 h-8" />
                Add New
            </button>
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
                            {{ coordinator.coordinator_uuid || 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Full Name</span>
                        <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ coordinator.firstName || '' }} {{ coordinator.lastName || '' }}
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
                        <span class="text-sm text-gray-500 dark:text-gray-400">Position</span>
                        <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ coordinator.position ?? 'N/A' }}
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
            <div v-else class="text-center p-6 text-gray-500">
                {{ $t('coordinators.no_coordinators_found') }}
            </div>
        </div>
    </div>

    
    <Modal
        :show="createCoordinatorModal"
        @close="closeCreateCoordinatorModal"
        :maxWidth="'md'"
        class="p-6"
    >
        <div class="w-full px-8 py-6">
            <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
                Create Coordinator for - <span class=" text-yellow-700 underline" >{{ student.first_name }} {{ student.middle_name }}</span>
            </h1>

            <form @submit.prevent="submitNewCoordinator">

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Full Name</label>
                    <input type="number" v-model="coordinatorCreationForm.name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" />
                    <InputError :message="coordinatorCreationForm.errors.name" class="mt-2 text-sm text-red-500" />
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Coordinator Naration</label>
                    <input type="text" v-model="coordinatorCreationForm.phone" id="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" />
                    <InputError :message="coordinatorCreationForm.errors.phone" class="mt-2 text-sm text-red-500" />
                </div>                

                <div class="mb-4">
                    <label for="coordinator_date" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Coordinator Date</label>
                    <input type="date" v-model="coordinatorCreationForm.coordinator_date" id="coordinator_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" />
                    <InputError :message="coordinatorCreationForm.errors.coordinator_date" class="mt-2 text-sm text-red-500" />
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Status</label>
                    <select v-model="coordinatorCreationForm.status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    <InputError :message="coordinatorCreationForm.errors.status" class="mt-2 text-sm text-red-500" />
                </div>

                <div class="flex justify-end mt-4">
                    <button
                        type="submit"
                        :disabled="coordinatorCreationForm.processing"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                    >
                        {{ coordinatorCreationForm.processing ? "Creating..." : "Create Coordinator" }}
                    </button>

                    <button
                        type="button"
                        @click="closeCreateCoordinatorModal"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                    >
                        Close
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
