<script setup>
import { ref, computed } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Dialog from 'primevue/dialog';
import Select from "primevue/select";
import { ArrowPathIcon, ChevronDownIcon, EyeIcon, PencilSquareIcon } from "@heroicons/vue/24/outline";
import { TrashIcon } from "@heroicons/vue/24/solid";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";

const props = defineProps({
    center: Object,

    students: {
        type: Array,
        required: true,
    },
    coordinator: {
        type: Object,
        required: false,
    },
    sortInfo: {
        type: Object,
        required: false,
    },
});

const emit = defineEmits(["close"]);

const visible = ref(true);

const studentSectionForm = useForm({
    section_id: "",
    student_id: "",
});

const assignStudentToSection = (studentId) => {
    studentSectionForm.student_id = studentId;
    studentSectionForm.post(route("student-section.assign"), {
        onSuccess: () => {
            Swal.fire("Success!", "Student assigned to section.", "success");
            studentSectionForm.reset();
        },
    });
};
</script>

<template>
    <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Students
            </h2>
        </div>

        <!-- Students Table -->
        <div class="overflow-x-auto shadow-md sm:rounded-lg mt-3">
            <!-- Export list to Excel -->
            
            <Table>
                <TableHeader>
                    <tr>
                        <Thead>No.</Thead>
                        <Thead>Student Name</Thead>
                        <Thead>ID Number</Thead>
                        <Thead>Phone</Thead>
                        <Thead>Program</Thead>
                        <Thead>Actions</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows
                        v-for="(student, index) in students"
                        :key="student.id"
                    >
                        <td class="px-6 py-4">
                            {{ index + 1 }}
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <Link :href="route('students.show', { student: student.id })">
                                {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
                            </Link>
                        </th>
                        <td class="px-6 py-4">{{ student.idNo }}</td>
                        <td class="px-6 py-4">{{ student.mobilePhone }}</td>
                        <td class="px-6 py-4">{{ student.program?.name }}</td>
                        <td class="px-6 py-4 flex space-x-6">
                            <div v-if="userCan('view-students')">
                                <Link :href="route('students.show', { student: student.id })" class="text-blue-500 hover:text-blue-700">
                                    <EyeIcon class="w-5 h-5" />
                                </Link>
                            </div>
                            <div v-if="userCan('update-students')">
                                <Link :href="route('students.edit', { student: student.id })" class="text-green-500 hover:text-green-700">
                                    <PencilSquareIcon class="w-5 h-5" />
                                </Link>
                            </div>
                        </td>
                    </TableZebraRows>
                </tbody>
            </Table>
        </div>
    </div>
</template>
