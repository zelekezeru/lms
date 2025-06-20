<script setup>
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    BookOpenIcon,
} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import { Listbox } from "primevue";
import InputError from "@/Components/InputError.vue";
import { EyeIcon } from "@heroicons/vue/24/outline";

// Define the props for the instructor
const props = defineProps({
    course: {
        type: Object,
        required: true,
    },
    instructors: {
        type: Object,
        required: true,
    },
});

const assignInstructors = ref(false);
console.log("hi");

const instructorAssignmentForm = useForm({
    instructors: props.course.instructors.map((instructor) => instructor.id),
});

const closeInstructorAssignment = () => {
    assignInstructors.value = false;
    instructorAssignmentForm.reset();
    instructorAssignmentForm.clearErrors();
};

const submitInstructorAssignment = () => {
    instructorAssignmentForm.post(
        route("instructors-course.assign", { course: props.course.id }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Successful!",
                    "Instructors assigned successfully.",
                    "success"
                );
                assignInstructors.value = false;
                instructorAssignmentForm.reset();
                instructorAssignmentForm.instructors =
                    props.course.instructors.map((instructor) => instructor.id);
            },
        }
    );
};
</script>

<template>
    <div class="">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Instructors
            </h2>
            <button
                @click="assignInstructors = !assignInstructors"
                class="flex items-center text-indigo-600 hover:text-indigo-800"
            >
                Assign Instructors
            </button>
        </div>
        <div class="overflow-x-auto">
            <div
                class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Instructors
                    </h2>
                </div>
                <!-- Instructor instructors list -->
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    No.
                                </th>
                                <th
                                    class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Name
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(instructor, index) in [...course.instructors].sort((a, b) => a.name.localeCompare(b.name))"
                                :key="instructor.id"
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                                class="border-b border-gray-300 dark:border-gray-600"
                            >
                                <td
                                    class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ index + 1 }}
                                </td>

                                <td
                                    class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <Link
                                        :href="
                                            route('instructors.show', {
                                                instructor: instructor.id,
                                            })
                                        "
                                    >
                                        {{ instructor.name }}
                                    </Link>
                                </td>

                                <!-- Instructor Assessments -->
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <Link
                                        class="text-green-500 hover:text-green-700"
                                        :href="route('instructors.show', {instructor: instructor.id})"
                                    >
                                        <EyeIcon class="w-5 h-5 inline-block" />
                                        <span class="inline-block"
                                            >View</span
                                        >
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <Modal
        :show="assignInstructors"
        @close="assignInstructors = !assignInstructors"
        :maxWidth="'6xl'"
        class="p-24 h-full"
    >
        <div class="w-full px-16 py-8">
            <h1 class="text-lg mb-5">
                Pick Instructors You Would like To Assign To This Section
            </h1>

            <Listbox
                id="cousesList"
                v-model="instructorAssignmentForm.instructors"
                :options="instructors"
                optionLabel="name"
                option-value="id"
                appendTo="self"
                filter
                checkmark
                multiple
                list-style="max-height: 500px"
                placeholder="Select Instructors"
                :maxSelectedLabels="3"
                class="w-full"
            />

            <InputError
                :message="instructorAssignmentForm.errors.programs"
                class="mt-2 text-sm text-red-500"
            />
            <div class="flex justify-end mt-4">
                <button
                    @click="submitInstructorAssignment"
                    :disabled="instructorAssignmentForm.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                >
                    {{
                        instructorAssignmentForm.processing
                            ? "Assign..."
                            : "Assign"
                    }}
                </button>

                <button
                    @click="closeInstructorAssignment"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                >
                    Close
                </button>
            </div>
        </div>
    </Modal>
</template>
