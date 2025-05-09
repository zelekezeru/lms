<script setup>
import { defineProps, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ArrowDownIcon, ArrowPathIcon, ChevronDownIcon, PlusCircleIcon } from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Select } from "primevue";

const props = defineProps({
    track: { type: Object, required: true },
});

const studentSectionForm = useForm({
    section_id: "",
    student_id: "",
});

const createStudent = ref(false);

const assignStudentToSection = (studentId) => {
    studentSectionForm.student_id = studentId;
    studentSectionForm.post(
        route("student-section.assign", {
            // redirectTo: route("tracks.show", { track: props.track.id }),
            // params: { track: props.track.id },
        }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Changed!",
                    "Student Assigned To Section Successfully",
                    "success"
                );
                studentSectionForm.reset();
            },
        }
    );
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Students
            </h2>
            <!-- <button
                @click="createStudent = !createStudent"
                class="flex items-center text-indigo-600 hover:text-indigo-800"
            >
                <PlusCircleIcon class="w-8 h-8" />
            </button> -->
        </div>
        <!-- Track Study Students List -->
        <div class="overflow-x-auto">
            <div
                class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Students
                    </h2>
                    <!-- <button
                        @click="createStudent = !createStudent"
                        class="flex items-center space-x-6 text-indigo-600 hover:text-indigo-800 transition"
                    >
                        <PlusCircleIcon class="w-8 h-8" />
                        <span class="hidden sm:inline">Add Student</span>
                    </button> -->
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="w-full min-w-[500px] table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Name
                                </th>
                                <th
                                    class="w-30 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Id
                                </th>
                                <th
                                    class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Current Section
                                </th>
                                <th
                                    class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Assign To
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(student, index) in track.students"
                                :key="student.id"
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                                class="border-b border-gray-300 dark:border-gray-600"
                            >
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ student.firstName }}
                                    {{ student.lastName }}
                                </td>
                                <td
                                    class="w-30 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ student.idNo }}
                                </td>
                                <td
                                    class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    {{
                                        student.section
                                            ? student.section.name
                                            : "N/A"
                                    }}
                                </td>
                                <td
                                    class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    <Select
                                        id="studentSectionAssignment"
                                        v-model="studentSectionForm.section_id"
                                        :options="track.sections"
                                        option-value="id"
                                        option-label="name"
                                        checkmark
                                        filter
                                        @change="
                                            assignStudentToSection(student.id)
                                        "
                                        :disabled="
                                            studentSectionForm.processing
                                        "
                                        placeholder="Change Section To..."
                                        :maxSelectevdLabels="3"
                                        class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                                    >
                                        <template #dropdownicon>
                                            <ChevronDownIcon
                                                v-if="
                                                    !studentSectionForm.processing
                                                "
                                                class="w-5 h-5 text-gray-500"
                                            />
                                            <ArrowPathIcon
                                                v-else
                                                class="w-5 h-5 text-gray-500 animate-spin"
                                            />
                                        </template>
                                    </Select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
