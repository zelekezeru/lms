<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    ArrowPathIcon,
    TrashIcon,
    EyeIcon,
    PlusIcon,
    PencilSquareIcon,
    PencilIcon,
    CalendarDaysIcon,
    CheckBadgeIcon,
    ClipboardDocumentCheckIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
    year: {
        type: Object,
        required: true,
    },
});

const { year } = props;

const showForm = ref(false);
const isEditing = ref(false);
const selectedSemester = ref(null);

// Form data
const semesterForm = ref({
    name: "",
    year_id: year.id,
    status: "inactive",
    is_approved: false,
    is_completed: false,
    start_date: "",
    end_date: "",
});

// Toggle create form
const toggleForm = () => {
    showForm.value = !showForm.value;
    isEditing.value = false;
    resetForm();
};

// Reset form
const resetForm = () => {
    semesterForm.value = {
        name: "",
        status: "inactive",
        year_id: year.id,
        is_approved: false,
        is_completed: false,
        start_date: "",
        end_date: "",
    };
    selectedSemester.value = null;
};

// Submit semester (create or update)
const submitSemester = () => {
    const action = isEditing.value
        ? router.put(route("semesters.update", selectedSemester.value.id), semesterForm.value, {
              onSuccess: () => {
                  Swal.fire("Updated!", "Semester has been updated.", "success");
                  resetForm();
                  showForm.value = false;
              },
          })
        : router.post(route("semesters.store"), semesterForm.value, {
              onSuccess: () => {
                  Swal.fire("Added!", "Semester has been added.", "success");
                  resetForm();
                  showForm.value = false;
              },
          });
};

// Edit semester
const editSemester = (semester) => {
    semesterForm.value = { ...semester };
    selectedSemester.value = semester;
    isEditing.value = true;
    showForm.value = true;
};

// Refresh
const refreshing = ref(false);
const refreshData = () => {
    refreshing.value = true;
    router.visit(route("years.show", year.id), {
        only: ["year"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

// Delete year
const deleteYear = (id) => {
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
            router.delete(route("years.destroy", { year: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "The Year has been deleted.", "success");
                },
                onError: () => {
                    Swal.fire("Error!", "There was a problem deleting the Year.", "error");
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>        

        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-semibold mb-6 text-center"> {{ year.name }} - Year Details</h1>

            <!-- Year Details -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border dark:border-gray-700">
                <div class="bg-white shadow-md rounded-2xl p-6 mb-6 border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center space-x-2">
                        <CalendarIcon class="w-5 h-5 text-blue-500" />
                        <span> Academic Year Details</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <span class="text-sm text-gray-500">Name</span>
                            <div class="text-lg font-semibold text-gray-800">{{ year.name }}</div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <span class="text-sm text-gray-500">Approval</span>
                            <div :class="year.is_approved ? 'text-green-600' : 'text-red-600'" class="text-lg font-semibold">
                                {{ year.is_approved ? "Approved" : "Not Approved" }}
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <span class="text-sm text-gray-500">Status</span>
                            <div :class="year.status === 'Active' ? 'text-green-600' : 'text-red-600'" class="text-lg font-semibold">
                                {{ year.status }}
                            </div>
                        </div>
                    </div>

                    <!-- Edit/Delete Year Buttons -->
                    <div class="flex justify-end mt-6 space-x-4">
                        <div v-if="userCan('update-years')">
                            <Link 
                                :href="route('years.edit', { year: year.id })" 
                                class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-800 hover:underline"
                            >
                                <PencilIcon class="w-5 h-5" />
                                <span>Edit</span>
                            </Link>
                        </div>
                        <div v-if="userCan('delete-years')">
                            <button 
                                @click="deleteYear(year.id)" 
                                class="inline-flex items-center space-x-2 text-red-600 hover:text-red-800 hover:underline"
                            >
                                <TrashIcon class="w-5 h-5" />
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>
                </div>


                <!-- Form Toggle & Refresh -->
                <div class="mt-6 flex space-x-6">
                    <button @click="refreshData" class="bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase hover:bg-blue-700 flex items-center">
                        <ArrowPathIcon class="w-5 h-5 mr-2" :class="{ 'animate-spin': refreshing }" />
                        Refresh
                    </button>
                    <button @click="toggleForm" class="bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase hover:bg-green-700 flex items-center">
                        <PlusIcon class="w-5 h-5 mr-2" />
                        {{ isEditing ? "Cancel Editing" : "Add Semester" }}
                    </button>
                </div>

                <!-- Semester Form -->
                <div v-if="showForm" class="mt-6 bg-gray-50 p-4 rounded shadow dark:bg-gray-700">
                    <form @submit.prevent="submitSemester">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
                                <input v-model="semesterForm.name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start Date</label>
                                <input type="date" v-model="semesterForm.start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">End Date</label>
                                <input type="date" v-model="semesterForm.end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" v-model="semesterForm.is_approved" />
                                <label class="text-sm text-gray-700 dark:text-gray-200">Approved</label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" v-model="semesterForm.is_completed" />
                                <label class="text-sm text-gray-700 dark:text-gray-200">Completed</label>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                {{ isEditing ? "Update Semester" : "Create Semester" }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Semester List -->
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="semester in year.semesters"
                        :key="semester.id"
                        class="rounded-xl p-5 shadow-md border dark:border-gray-700 bg-white dark:bg-[#1E293B]"
                    >
                        <div class="flex items-center mb-3 text-indigo-600 dark:text-indigo-400">
                            <CalendarDaysIcon class="w-6 h-6 mr-2" />
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                                <Link :href="route('semesters.show', { semester: semester.id })" class="hover:underline">
                                    {{ semester.name || "No Name" }}
                                </Link>
                            </h2>
                        </div>
                        <div class="text-sm space-y-1 text-gray-600 dark:text-gray-300">
                            <p>
                                <CheckBadgeIcon class="w-4 h-4 inline-block mr-1 text-green-500" />
                                <span>Status: </span>
                                <span
                                    :class="semester.status === 'Active'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900 px-2 py-0.5 rounded-full font-semibold'
                                        : 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 px-2 py-0.5 rounded-full font-semibold'"
                                >
                                    {{ semester.status }}
                                </span>
                            </p>
                            <p>
                                <ClipboardDocumentCheckIcon class="w-4 h-4 inline-block mr-1 text-yellow-500" />
                                <span>Approved:</span>
                                <span
                                    :class="semester.is_approved
                                        ? 'text-green-600 font-semibold'
                                        : 'text-red-600 font-semibold'"
                                >
                                    {{ semester.is_approved ? "Yes" : "No" }}
                                </span>
                            </p>
                            <p>
                                <CheckCircleIcon class="w-4 h-4 inline-block mr-1 text-purple-500" />
                                <span>Completed:</span>
                                <span
                                    :class="semester.is_completed
                                        ? 'text-green-600 font-semibold'
                                        : 'text-red-600 font-semibold'"
                                >
                                    {{ semester.is_completed ? "Yes" : "No" }}
                                </span>
                            </p>
                            <p><CalendarDaysIcon class="w-4 h-4 inline-block mr-1 text-blue-500" /> <span>Start:</span> {{ semester.start_date }} </p>
                            <p><CalendarDaysIcon class="w-4 h-4 inline-block mr-1 text-blue-500" /> <span>End:</span> {{ semester.end_date }}</p>
                        </div>
                        <div class="mt-4 text-right">
                            <button @click="editSemester(semester)" class="text-blue-600 hover:underline text-sm flex items-center space-x-1">
                                <PencilSquareIcon class="w-4 h-4" />
                                <span>Edit</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
