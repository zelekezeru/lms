<script setup>
import { defineProps, ref, reactive } from "vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import Input from '@/Components/Input.vue'; 
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
import { Input } from "postcss";

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
        >
            <div class="w-full px-16 py-8">
                <h1 class="text-lg font-semibold mb-5">
                    Pick Courses You Would Like To Assign To This Curriculum
                </h1>

                <!-- Curriculum Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Curriculum Name</label>
                    <Input type="text" v-model="form.name" placeholder="e.g., Software Engineering Curriculum" class="w-full" />
                    <InputError :message="form.errors.name" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Program Selection (optional if multiple exist) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Program</label>
                    <select v-model="form.program_id" class="w-full border rounded px-3 py-2">
                        <option value="">-- Select Program --</option>
                        <option v-for="program in department.programs" :key="program.id" :value="program.id">
                            {{ program.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.program_id" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Curriculum Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Curriculum Type</label>
                    <select v-model="form.type" class="w-full border rounded px-3 py-2">
                        <option value="">-- Select Type --</option>
                        <option value="Undergraduate">Undergraduate</option>
                        <option value="Graduate">Graduate</option>
                        <option value="Diploma">Diploma</option>
                    </select>
                    <InputError :message="form.errors.type" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Effective Date -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Effective Date</label>
                    <Input type="date" v-model="form.effective_date" class="w-full" />
                    <InputError :message="form.errors.effective_date" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Year -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Year</label>
                    <Input type="number" v-model="form.year" class="w-full" />
                    <InputError :message="form.errors.year" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Semester -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Semester</label>
                    <Input type="number" v-model="form.semester" class="w-full" />
                    <InputError :message="form.errors.semester" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Course Selection -->
                <div class="mb-6">
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
                        list-style="max-height: 300px"
                    />
                    <InputError :message="form.errors.courses" class="mt-2 text-sm text-red-500" />
                </div>

                <!-- Hidden Department ID -->
                <Input type="hidden" name="department_id" :value="department.id" />

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 mt-6">
                    <button
                        @click="assignCurriculum"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                    >
                        Assign
                    </button>
                    <button
                        @click="closeModal"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded"
                    >
                        Cancel
                    </button>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end px-6 pb-4">
                    <button
                        @click="closeModal"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded"
                    >
                        Close
                    </button>
                </div>
            </template>
        </Modal>

    </div>
</template>
