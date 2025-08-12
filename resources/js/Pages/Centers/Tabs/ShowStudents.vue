<script setup>
import { ref, computed } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Dialog from "primevue/dialog";
import Select from "primevue/select";
import {
    ArrowPathIcon,
    ChevronDownIcon,
    EyeIcon,
    PencilSquareIcon,
} from "@heroicons/vue/24/outline";
import { TrashIcon } from "@heroicons/vue/24/solid";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";

const props = defineProps({
    center: Object,

    students: {
        type: Object,
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

const search = ref("");

const filteredStudents = computed(() => {
    if (!search.value) return props.students.data;
    const term = search.value.toLowerCase();
    return props.students.data.filter((student) =>
        [student.firstName, student.middleName, student.lastName, student.idNo, student.mobilePhone]
            .filter(Boolean)
            .join(" ")
            .toLowerCase()
            .includes(term)
    );
});
</script>

<template>
    <div
        class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
    >
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Students
            </h2>
            <!-- Student Search Box -->
            <input
                v-model="search"
                type="text"
                placeholder="Search students..."
                class="ml-4 px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring focus:border-blue-300 dark:bg-gray-800 dark:text-gray-100"
                style="min-width: 220px"
            />
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
                        <Thead>Section</Thead>
                        <Thead>Actions</Thead>
                    </tr>
                </TableHeader>
                <tbody>
                    <TableZebraRows
                        v-for="(student, index) in filteredStudents"
                        :key="student.id"
                    >
                        <td class="px-6 py-4">
                            {{ index + 1 }}
                        </td>
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                        >
                            <Link
                                :href="
                                    route('students.show', {
                                        student: student.id,
                                    })
                                "
                            >
                                {{ student.firstName }}
                                {{ student.middleName }} {{ student.lastName }}
                            </Link>
                        </th>
                        <td class="px-6 py-4">{{ student.idNo }}</td>
                        <td class="px-6 py-4">{{ student.mobilePhone }}</td>
                        <td class="px-6 py-4">{{ student.program?.name }}</td>
                        <td class="px-6 py-4">{{ student.section?.name }}</td>
                        <td class="px-6 py-4 flex space-x-6">
                            <div v-if="userCan('view-students')">
                                <Link
                                    :href="
                                        route('students.show', {
                                            student: student.id,
                                        })
                                    "
                                    class="text-blue-500 hover:text-blue-700"
                                >
                                    <EyeIcon class="w-5 h-5" />
                                </Link>
                            </div>
                        </td>
                    </TableZebraRows>
                </tbody>
            </Table>

            <!-- Pagination Links -->
            <div class="mt-4 flex justify-center">
                <nav v-if="students.links && students.links.length > 1" class="inline-flex -space-x-px">
                    <Link
                        v-for="(link, i) in students.links"
                        :key="i"
                        :href="link.url || ''"
                        class="px-3 py-1 border text-sm"
                        :class="[
                            link.active
                                ? 'bg-indigo-600 text-white border-indigo-600'
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700',
                            link.url ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'
                        ]"
                        v-html="link.label"
                        :disabled="!link.url"
                    />
                </nav>
            </div>
        </div>
    </div>
</template>
