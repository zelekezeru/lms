<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
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

// Define the props for the employee
defineProps({
    employee: {
        type: Object,
        required: true,
    },
});
// Multi nav header options
const selectedTab = ref('details');


const tabs = [
    { key: 'details', label: 'Details', icon: CogIcon },
    { key: 'documents', label: 'Documents', icon: BookOpenIcon },
    
];

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

// Delete function with SweetAlert confirmation
const deleteEmployee = (id) => {
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
            router.delete(route("employees.destroy", { employee: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The employee has been deleted.",
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
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ instructor.user.name }}
            </h1>

            <nav
                class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    :class="[
                        'flex items-center px-4 py-2 space-x-2 text-sm font-medium transition',
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
                    ]"
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            
                <!-- Details Panel -->
            <div v-show="selectedTab === 'details'" >
                <div class="max-w-8xl mx-auto p-6">
                    <h1
                        class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
                    >
                        Employee Details
                    </h1>

                    <div class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700">
                        
                        <div class="flex justify-center mb-8">
                            <div
                                v-if="!imageLoaded"
                                class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                            ></div>
                            <img
                                v-show="imageLoaded"
                                class="rounded-full w-44 h-44 object-contain bg-gray-400"
                                :src="employee.profileImg"
                                :alt="`profile image of ` + employee.name"
                                @load="imageLoaded = true"
                            />
                        </div>
                        <div
                            class="grid sm:grid-cols-2 gap-4 place-items-center lg:pl-30 sm:gap-4"
                        >
                            <!-- employee ID -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >ID</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ employee.id }}</span
                                >
                            </div>

                            <!-- employee Name -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Name</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ employee.name }}</span
                                >
                            </div>

                            <!-- employee Email -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Email</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ employee.email }}</span
                                >
                            </div>

                            <!-- Role -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Role</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ employee.userRole }}</span
                                >
                            </div>

                            <!-- Employment Type -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Employment Type</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ employee.employmentType }}</span
                                >
                            </div>

                            <!-- Job Position -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Job Position</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ employee.jobPosition }}</span
                                >
                            </div>
                        </div>

                        <!-- Edit and Delete Buttons -->
                        <div class="flex justify-end mt-6 space-x-6">
                            <Link
                                v-if="userCan('update-employees')"
                                :href="
                                    route('employees.edit', { employee: employee.id })
                                "
                                class="text-blue-500 hover:text-blue-700"
                            >
                                <PencilIcon class="w-5 h-5" />
                                <span>Edit</span>
                            </Link>
                            <button
                                v-if="userCan('delete-employees')"
                                @click="deleteEmployee(employee.id)"
                                class="text-red-500 hover:text-red-700"
                            >
                                <TrashIcon class="w-5 h-5" />
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Documents Panel -->
                     
                <div v-show="selectedTab === 'documents'">
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Employee Documents
                        </h2>
                        
                            
                        <div class="flex justify-center mb-6">
                            

                        </div> 
                    </div>

                    <div class="overflow-x-auto">
                        <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
                    
                            <!-- instructor details -->
                            <div class="grid grid-cols-2 gap-4">


                                
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </AppLayout>
</template>
