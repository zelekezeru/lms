<script setup>
import { ref, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    LockClosedIcon,
    LockOpenIcon,
    CheckBadgeIcon,
    ClockIcon,
    XCircleIcon,
    ExclamationCircleIcon,
    PencilIcon,
    ArrowPathIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    section: Object,
    course: Object,
    semester: Object,
    weights: Array,
    grades: Array,
    instructor: Object,
    studentsList: Object,
    studentResults: Object,
    courseOffering: Object,
});

// ─── Computed State ───────────────────────────────────────────────────────────

const sumOfWeightPoints = computed(() =>
    props.weights.reduce((sum, w) => sum + (parseFloat(w.point) || 0), 0)
);

const isGradeLocked = computed(() =>
    props.courseOffering?.grade_submission_status === "approved"
);

const isSubmissionPending = computed(() =>
    props.courseOffering?.grade_submission_status === "pending"
);

const isSubmissionRejected = computed(() =>
    props.courseOffering?.grade_submission_status === "rejected"
);

const submittedGrades = computed(() =>
    props.grades?.filter((g) => g.course_id === props.course.id) ?? []
);

const hasSubmittedGrades = computed(() => submittedGrades.value.length > 0);

// ─── Helpers ──────────────────────────────────────────────────────────────────

const getStudentTotalPoints = (studentId) => {
    let total = 0;
    props.weights.forEach((weight) => {
        const result = props.studentResults[studentId]?.[weight.id];
        if (result?.point) total += parseFloat(result.point);
    });
    return total.toFixed(2);
};

const getGradeLetter = (point) => {
    point = parseFloat(point);
    if (isNaN(point)) return "N/A";
    if (point >= 94) return "A";
    if (point >= 90) return "A-";
    if (point >= 87) return "B+";
    if (point >= 84) return "B";
    if (point >= 80) return "B-";
    if (point >= 77) return "C+";
    if (point >= 74) return "C";
    if (point >= 70) return "C-";
    if (point >= 67) return "D+";
    if (point >= 64) return "D";
    if (point >= 60) return "D-";
    if (point == 0) return "NG";
    return "F";
};

const getGradeBadgeClass = (letter) => {
    if (!letter) return "bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400";
    if (letter.startsWith("A")) return "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300";
    if (letter.startsWith("B")) return "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300";
    if (letter.startsWith("C")) return "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300";
    if (letter.startsWith("D")) return "bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300";
    return "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300";
};

// ─── Sorted students ──────────────────────────────────────────────────────────

const sortedStudents = computed(() =>
    [...(props.studentsList ?? [])].sort((a, b) => {
        const nA = `${a.first_name} ${a.middle_name ?? ""}`.toUpperCase();
        const nB = `${b.first_name} ${b.middle_name ?? ""}`.toUpperCase();
        return nA < nB ? -1 : nA > nB ? 1 : 0;
    })
);

// ─── All weights filled check ─────────────────────────────────────────────────

const allWeightsHaveValues = computed(() => {
    if (!sortedStudents.value.length || !props.weights.length) return false;
    const studentsWithSomeData = sortedStudents.value.filter((student) =>
        props.weights.some((weight) => {
            const pt = props.studentResults[student.id]?.[weight.id]?.point;
            return pt !== null && pt !== undefined && pt !== "" && pt !== "NG";
        })
    );
    return studentsWithSomeData.every((student) =>
        props.weights.every((weight) => {
            const pt = props.studentResults[student.id]?.[weight.id]?.point;
            return pt !== null && pt !== undefined && pt !== "" && pt !== "NG";
        })
    );
});

// ─── Grade submission ─────────────────────────────────────────────────────────

const gradeForm = useForm({ grades: [] });

const generateGrades = () => {
    if (!allWeightsHaveValues.value) return;

    Swal.fire({
        title: "Submit Grades?",
        html: `<p>This will submit the computed grades for all students in this course.</p>`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        confirmButtonText: "Submit Grades",
    }).then((result) => {
        if (!result.isConfirmed) return;

        const gradesPayload = sortedStudents.value.map((student) => {
            const totalPoint = getStudentTotalPoints(student.id);
            return {
                student_id: student.id,
                grade_point: totalPoint,
                grade_letter: getGradeLetter(totalPoint),
                grade_scale: sumOfWeightPoints.value.toString(),
                grade_status: "Submitted",
                user_id: props.instructor?.id,
                year_id: props.semester.year_id,
                semester_id: props.semester.id,
                section_id: props.section.id,
                course_id: props.course.id,
            };
        });

        gradeForm.grades = gradesPayload;
        gradeForm.post(route("grades.store"), {
            onSuccess: () =>
                Swal.fire({
                    icon: "success",
                    title: "Grades Submitted!",
                    text: "Grades have been submitted successfully.",
                    timer: 2500,
                    showConfirmButton: false,
                }),
            onError: (errors) =>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: Object.values(errors)[0] ?? "Failed to submit grades.",
                }),
        });
    });
};

// ─── Grade Complaint ──────────────────────────────────────────────────────────

const complaintModal = ref(false);
const complaintGrade = ref(null);
const complaintReason = ref("");
const complaintForm = useForm({ grade_id: null, reason: "", filed_by_role: "instructor" });

const openComplaintModal = (grade) => {
    complaintGrade.value = grade;
    complaintReason.value = "";
    complaintModal.value = true;
};

const submitComplaint = () => {
    if (!complaintReason.value.trim()) {
        Swal.fire({ icon: "warning", title: "Reason required", text: "Please provide a reason for the complaint." });
        return;
    }
    complaintForm.grade_id = complaintGrade.value.id;
    complaintForm.reason = complaintReason.value;
    complaintForm.filed_by_role = "instructor";

    complaintForm.post(route("grade.complaints.store"), {
        onSuccess: () => {
            complaintModal.value = false;
            Swal.fire({
                icon: "success",
                title: "Complaint Filed",
                text: "Your grade complaint has been submitted for review by the Academic Dean.",
                timer: 3000,
                showConfirmButton: false,
            });
        },
        onError: (errors) =>
            Swal.fire({
                icon: "error",
                title: "Error",
                text: Object.values(errors)[0] ?? "Failed to file complaint.",
            }),
    });
};

// ─── Import ───────────────────────────────────────────────────────────────────
const fileInput = ref(null);
const submitImport = () => {
    const formData = new FormData();
    formData.append("grades_file", fileInput.value.files[0]);
    formData.append("course_id", props.course.id);
    formData.append("section_id", props.section.id);
    formData.append("semester_id", props.semester.id);
    formData.append("year_id", props.semester.year_id);
    router.post(route("sectionGrades.import"), formData, { forceFormData: true });
};
</script>

<template>
    <div class="space-y-5 px-1 py-2">

        <!-- Status Banner -->
        <div v-if="isGradeLocked"
            class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl px-5 py-4">
            <LockClosedIcon class="w-6 h-6 text-green-600 dark:text-green-400 shrink-0" />
            <div class="flex-1">
                <p class="text-sm font-bold text-green-700 dark:text-green-300">Grades Approved & Locked</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">
                    Approved by admin on {{ new Date(courseOffering.grade_submission_approved_at).toLocaleDateString() }}.
                    Grades are official and read-only. File a complaint to request modifications.
                </p>
            </div>
            <CheckBadgeIcon class="w-8 h-8 text-green-500 dark:text-green-400 shrink-0" />
        </div>

        <div v-else-if="isSubmissionPending"
            class="flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl px-5 py-4">
            <ClockIcon class="w-6 h-6 text-amber-600 dark:text-amber-400 shrink-0" />
            <div>
                <p class="text-sm font-bold text-amber-700 dark:text-amber-300">Awaiting Admin Approval</p>
                <p class="text-xs text-amber-600 dark:text-amber-400">Grade submission is pending admin/registrar review.</p>
            </div>
        </div>

        <div v-else-if="isSubmissionRejected"
            class="flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl px-5 py-4">
            <XCircleIcon class="w-6 h-6 text-red-600 dark:text-red-400 shrink-0" />
            <div>
                <p class="text-sm font-bold text-red-700 dark:text-red-300">Submission Rejected</p>
                <p class="text-xs text-red-600 dark:text-red-400">{{ courseOffering.grade_submission_notes ?? 'No notes provided.' }}</p>
            </div>
        </div>

        <!-- ═══ NOT YET SUBMITTED ═══ -->
        <div v-if="!hasSubmittedGrades">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Grade Generation</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                            <th v-for="weight in weights" :key="weight.id" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ weight.name }}<br /><span class="text-[10px] font-normal opacity-60">{{ weight.point }}pt</span>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr
                            v-for="(student, index) in sortedStudents"
                            :key="student.id"
                            :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/40 dark:bg-gray-800/40'"
                        >
                            <td class="px-4 py-2.5 text-xs text-gray-400">{{ index + 1 }}</td>
                            <td class="px-4 py-2.5 text-sm font-medium text-gray-800 dark:text-gray-100">
                                {{ student.first_name }} {{ student.middle_name }} {{ student.last_name }}
                            </td>
                            <td v-for="weight in weights" :key="weight.id" class="px-4 py-2.5 text-center text-sm">
                                <span v-if="studentResults[student.id]?.[weight.id]?.point !== null && studentResults[student.id]?.[weight.id]?.point !== undefined"
                                    class="font-semibold text-gray-700 dark:text-gray-200">
                                    {{ studentResults[student.id]?.[weight.id]?.point ?? "—" }}
                                </span>
                                <span v-else class="text-gray-300 dark:text-gray-600">N/A</span>
                            </td>
                            <td class="px-4 py-2.5 text-center text-sm font-bold text-gray-900 dark:text-white">
                                {{ getStudentTotalPoints(student.id) }}
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full"
                                    :class="getGradeBadgeClass(getGradeLetter(getStudentTotalPoints(student.id)))">
                                    {{ getGradeLetter(getStudentTotalPoints(student.id)) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 dark:bg-gray-700/40 border-t border-gray-200 dark:border-gray-700">
                            <td :colspan="3 + weights.length" class="px-4 py-4 text-center">
                                <div v-if="allWeightsHaveValues && !isSubmissionPending && !isGradeLocked" class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                    <button
                                        @click="generateGrades"
                                        :disabled="gradeForm.processing"
                                        class="flex items-center gap-2 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl shadow transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md"
                                    >
                                        <ArrowPathIcon v-if="gradeForm.processing" class="w-4 h-4 animate-spin" />
                                        <CheckBadgeIcon v-else class="w-4 h-4" />
                                        Submit Grades
                                    </button>
                                </div>
                                <p v-else-if="isSubmissionPending" class="text-sm text-amber-600 dark:text-amber-400 flex items-center justify-center gap-2">
                                    <ClockIcon class="w-4 h-4" /> Awaiting admin approval...
                                </p>
                                <p v-else-if="isGradeLocked" class="text-sm text-green-600 dark:text-green-400 flex items-center justify-center gap-2">
                                    <LockClosedIcon class="w-4 h-4" /> Grades locked.
                                </p>
                                <p v-else class="text-sm text-red-500 dark:text-red-400 flex items-center justify-center gap-2">
                                    <ExclamationCircleIcon class="w-4 h-4" /> Fill all assessment scores before submitting grades.
                                </p>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- ═══ SUBMITTED GRADES ═══ -->
        <div v-else>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <LockClosedIcon v-if="isGradeLocked" class="w-5 h-5 text-green-600" />
                    Submitted Grades
                </h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Grade</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th v-if="isGradeLocked" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Complaint</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr
                            v-for="(grade, index) in grades"
                            :key="grade.id"
                            :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/40 dark:bg-gray-800/40'"
                        >
                            <td class="px-4 py-3 text-xs text-gray-400">{{ index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-100">
                                <a :href="route('students.show', grade.student?.id)" class="hover:underline text-blue-600 dark:text-blue-400">
                                    {{ grade.student?.first_name }} {{ grade.student?.middle_name }} {{ grade.student?.last_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-gray-900 dark:text-white">
                                {{ grade.changed_grade ?? grade.grade_point }}
                                <span v-if="grade.changed_grade && grade.changed_grade !== grade.grade_point" class="ml-1 text-xs text-amber-500">(modified)</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex px-2.5 py-0.5 text-xs font-bold rounded-full"
                                    :class="getGradeBadgeClass(grade.changed_letter ?? grade.grade_letter)">
                                    {{ grade.changed_letter ?? grade.grade_letter }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full"
                                    :class="grade.is_locked
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                        : grade.grade_status === 'Under Review'
                                            ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'">
                                    <LockClosedIcon v-if="grade.is_locked" class="w-3 h-3" />
                                    <LockOpenIcon v-else-if="grade.grade_status === 'Under Review'" class="w-3 h-3" />
                                    {{ grade.is_locked ? 'Locked' : grade.grade_status }}
                                </span>
                            </td>
                            <!-- Complaint column -->
                            <td v-if="isGradeLocked" class="px-4 py-3 text-center">
                                <div v-if="grade.complaints && grade.complaints.length > 0">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full"
                                        :class="grade.complaints[0].status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                            : grade.complaints[0].status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'">
                                        {{ grade.complaints[0].status === 'pending' ? '⏳ Pending Review' : grade.complaints[0].status === 'approved' ? '✓ Approved' : '✗ Rejected' }}
                                    </span>
                                </div>
                                <button
                                    v-else-if="grade.is_locked"
                                    @click="openComplaintModal(grade)"
                                    class="flex items-center gap-1 px-3 py-1 text-xs font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 hover:bg-amber-100 dark:hover:bg-amber-900/40 rounded-lg transition-colors"
                                >
                                    <ExclamationCircleIcon class="w-3.5 h-3.5" /> File Complaint
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ═══ Complaint Modal ═══ -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="complaintModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg p-6 space-y-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <ExclamationCircleIcon class="w-5 h-5 text-amber-500" />
                            File Grade Complaint
                        </h3>
                        <button @click="complaintModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div v-if="complaintGrade" class="bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-3 text-sm space-y-1">
                        <p><span class="font-medium text-gray-500">Student:</span> <span class="text-gray-800 dark:text-gray-100 font-semibold">{{ complaintGrade.student?.first_name }} {{ complaintGrade.student?.last_name }}</span></p>
                        <p><span class="font-medium text-gray-500">Current Grade:</span>
                            <span class="ml-1 inline-flex px-2 py-0.5 text-xs font-bold rounded-full" :class="getGradeBadgeClass(complaintGrade.grade_letter)">{{ complaintGrade.grade_letter }}</span>
                            <span class="ml-1 text-gray-600 dark:text-gray-300">({{ complaintGrade.grade_point }}pt)</span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Complaint *</label>
                        <textarea
                            v-model="complaintReason"
                            rows="4"
                            placeholder="Explain why this grade should be reviewed and what correction you believe is needed..."
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none transition"
                        ></textarea>
                        <p class="text-xs text-gray-400 mt-1">{{ complaintReason.length }}/1000 characters</p>
                    </div>

                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl px-4 py-3 text-xs text-amber-700 dark:text-amber-300">
                        <strong>Note:</strong> This complaint will be reviewed by the Academic Dean. If approved, you will be able to submit an improved grade. The original grade and all changes are permanently recorded in the audit log.
                    </div>

                    <div class="flex items-center gap-3 pt-1">
                        <button
                            @click="submitComplaint"
                            :disabled="complaintForm.processing || !complaintReason.trim()"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl shadow transition-all"
                        >
                            <ArrowPathIcon v-if="complaintForm.processing" class="w-4 h-4 animate-spin" />
                            Submit Complaint
                        </button>
                        <button @click="complaintModal = false" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>