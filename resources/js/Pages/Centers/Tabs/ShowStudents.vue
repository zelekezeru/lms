<script setup>
import { defineProps, defineEmits, ref } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Dialog from 'primevue/dialog';
import Select from "primevue/select";
import { ArrowPathIcon, ChevronDownIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    center: Object,
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
    <Dialog
        v-model:visible="visible"
        modal
        header="Center Students"
        :style="{ width: '70vw' }"
        :breakpoints="{ '960px': '95vw' }"
        @hide="$emit('close')"
    >
        <div v-if="students">
            <table class="w-full border text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-2 py-1 border">Name</th>
                        <th class="px-2 py-1 border">ID</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="student in students"
                        :key="student.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-2 py-1 border">
                            <Link :href="route('students.show', { student: student.id })">
                                {{ student.firstName }} {{ student.lastName }}
                            </Link>
                        </td>
                        <td class="px-2 py-1 border">{{ student.idNo }}</td>                     
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="text-gray-500">No students found for this center.</div>
    </Dialog>
</template>
