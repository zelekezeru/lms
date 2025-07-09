<script setup>
import { defineProps, ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    ArrowDownIcon,
    ArrowPathIcon,
    ChevronDownIcon,
    PlusCircleIcon,
} from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Select } from "primevue";
import { useI18n } from "vue-i18n"; // ✅ Add this line

const { t } = useI18n(); // ✅ Destructure the `t` function

const props = defineProps({
    track: { type: Object, required: true },
});

const studentSectionForm = useForm({});

props.track.students.forEach((student) => {
    studentSectionForm[student.id] = {
        section_id: student.section.id ?? "",
        processing: false,
    };
});
const createStudent = ref(false);

const assignStudentToSection = (studentId) => {
    studentSectionForm[studentId].processing = true;

    const form = useForm({
        section_id: studentSectionForm[studentId].section_id,
        student_id: studentId,
    });

    form.post(route("student-section.assign"), {
        onSuccess: () => {
            Swal.fire(
                t("students.success"),
                t("students.student_assigned_successfully"),
                "success"
            );
        },
        onFinish: () => {
            studentSectionForm[studentId].processing = false;
        },
        onError: () => {
            studentSectionForm[studentId].processing = false;
            studentSectionForm[studentId].section_id = track.students.find(
                (student) => student.id == studentId
            ).section?.id;
        },
    });
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ $t("students.students") }}
            </h2>
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
                        {{ $t("students.students") }}
                    </h2>
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
                                    {{ $t("students.name") }}
                                </th>
                                <th
                                    class="w-30 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ $t("students.id") }}
                                </th>
                                <th
                                    class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    {{ $t("students.current_section") }}
                                </th>
                                <th
                                    class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    {{ $t("students.assign_to") }}
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
                                    <Link
                                        :href="
                                            route('students.show', {
                                                student: student.id,
                                            })
                                        "
                                    >
                                        {{ student.firstName }}
                                        {{ student.lastName }}
                                    </Link>
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
                                            : $t("students.not_available")
                                    }}
                                </td>
                                <td
                                    class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    <Select
                                        id="studentSectionAssignment"
                                        v-model="
                                            studentSectionForm[student.id]
                                                .section_id
                                        "
                                        :options="
                                            track.sections.filter((section) => {
                                                return (
                                                    section.year.id ==
                                                        student.year.id &&
                                                    section.semester.id ==
                                                        student.semester.id &&
                                                    section.studyMode.id ==
                                                        student.studyMode.id
                                                );
                                            })
                                        "
                                        option-value="id"
                                        option-label="name"
                                        checkmark
                                        filter
                                        @change="
                                            assignStudentToSection(student.id)
                                        "
                                        :disabled="
                                            studentSectionForm[student.id]
                                                .processing
                                        "
                                        :placeholder="'Change Section To...'"
                                        :maxSelectedLabels="3"
                                        class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                                    >
                                        <template #dropdownicon>
                                            <ArrowPathIcon
                                                v-if="
                                                    studentSectionForm[
                                                        student.id
                                                    ].processing
                                                "
                                                class="w-5 h-5 text-gray-500 animate-spin"
                                            />
                                            <ChevronDownIcon
                                                v-else
                                                class="w-5 h-5 text-gray-500"
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
