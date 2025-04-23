<script setup>
import { defineProps, ref, reactive } from "vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Listbox } from 'primevue';
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
    department: Object,
    courses: Array,
    curriculums: Array,
});

const showModal = ref(false);

const form = useForm({
    courses: props.department.curriculums.map(course => course.id),
});

const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const assignCurriculum = () => {
    form.post(route('department-curriculum.attach', { department: props.department.id }), {
        onSuccess: () => {
            Swal.fire('Assigned!', 'Curriculum updated successfully.', 'success');
            closeModal();
        }
    });
};
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Curricula</h2>
            <button @click="openModal" class="text-blue-600 hover:underline">Create Curricula</button>
        </div>

        <!-- Curricula List -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-md overflow-hidden">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-sm text-gray-600 dark:text-gray-300">
                        <th class="px-4 py-2 text-left border-b border-gray-300 dark:border-gray-600">No.</th>
                        <th class="px-4 py-2 text-left border-b border-gray-300 dark:border-gray-600">Course Name</th>
                        <th class="px-4 py-2 text-left border-b border-gray-300 dark:border-gray-600">Credit Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(course, index) in curriculums"
                        :key="course.id"
                        class="text-sm text-gray-700 dark:text-gray-200 border-t border-gray-300 dark:border-gray-600"
                    >
                        <td class="px-4 py-2">{{ index + 1 }}</td>
                        <td class="px-4 py-2">
                            <Link :href="route('courses.show', { course: course.id })" class="text-blue-600 hover:underline">
                                {{ course.name }}
                            </Link>
                        </td>
                        <td class="px-4 py-2">{{ course.creditHours }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <Modal 
            :show="showModal" 
            @close="closeModal" 
            :maxWidth="'6xl'"
            class="p-24 h-full"
        >
            <form @submit.prevent="assignCurriculum">
                <!-- Modal Header -->
                <div class="flex items-center justify-end mb-6 mx-6 my-4">
                    <button type="button" @click="closeModal" class="text-red-500 hover:text-red-700 dark:hover:text-red-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Title -->
                <div class="flex justify-center items-center mb-6">
                    <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                        {{ department.name }} - Curriculum Design
                    </h1>
                </div>

                <!-- Input Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full px-16 py-8">
                    <!-- Year -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Year</label>
                        <input type="number" v-model="form.year" class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100" />
                        <InputError :message="form.errors.year" class="mt-2 text-sm text-red-500" />
                    </div>

                    <!-- Semester -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Semester</label>
                        <input type="number" v-model="form.semester" class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100" />
                        <InputError :message="form.errors.semester" class="mt-2 text-sm text-red-500" />
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Curriculum Description</label>
                        <input type="text" v-model="form.description" class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100" />
                        <InputError :message="form.errors.description" class="mt-2 text-sm text-red-500" />
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Curriculum Status</label>
                        <select v-model="form.status" class="w-full px-3 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100">
                            <option value="">-- Select Status --</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <InputError :message="form.errors.status" class="mt-2 text-sm text-red-500" />
                    </div>
                </div>

                <!-- Course Selection and Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full px-16 py-8">
                    <!-- Course Selection -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Select Courses</label>
                        <Listbox
                            v-model="form.courses"
                            :options="courses"
                            option-label="name"
                            option-value="id"
                            appendTo="self"
                            filter
                            multiple
                            placeholder="Select Courses"
                            class="w-full"
                            list-style="max-height: 200px"
                        />
                        <InputError :message="form.errors.courses" class="mt-2 text-sm text-red-500" />
                    </div>

                    <!-- Buttons -->
                    <div class=" justify-end space-x-2 mt-6">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Assign
                        </button>
                        <button type="button" @click="closeModal" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </Modal>


    </div>
</template>
