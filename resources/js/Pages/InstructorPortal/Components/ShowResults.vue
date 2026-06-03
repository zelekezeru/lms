<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    PencilIcon,
    CheckCircleIcon,
    XCircleIcon,
    PaperAirplaneIcon,
    LockClosedIcon,
    ExclamationTriangleIcon,
    ArrowPathIcon,
    CheckBadgeIcon,
    ClockIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    section: Object,
    course: Object,
    semester: Object,
    weights: Array,
    instructor: Object,
    studentsList: Array,
    studentResults: Object,
    courseOffering: Object,
});

// ─── State ────────────────────────────────────────────────────────────────────

const activeWeightId = ref(null);
const resultForm = ref({});
const isSaving = ref(false);

// Sorted students
const sortedStudents = computed(() =>
    [...(props.studentsList ?? [])].sort((a, b) => {
        const nameA = `${a.first_name} ${a.middle_name ?? ""}`.toUpperCase();
        const nameB = `${b.first_name} ${b.middle_name ?? ""}`.toUpperCase();
        return nameA < nameB ? -1 : nameA > nameB ? 1 : 0;
    })
);

// ─── Initialization ───────────────────────────────────────────────────────────

onMounted(() => {
    sortedStudents.value.forEach((student) => {
        resultForm.value[student.id] = {};
        props.weights.forEach((weight) => {
            const existing = props.studentResults[student.id]?.[weight.id];
            resultForm.value[student.id][weight.id] = existing?.point ?? null;
        });
    });
});

// ─── Helpers ──────────────────────────────────────────────────────────────────

const getExistingPoint = (studentId, weightId) =>
    props.studentResults[studentId]?.[weightId]?.point ?? null;

const sumOfWeightPoints = computed(() =>
    props.weights.reduce((s, w) => s + (parseFloat(w.point) || 0), 0)
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

// ─── Per-weight progress ──────────────────────────────────────────────────────

const getWeightFilledCount = (weightId) => {
    return sortedStudents.value.filter((s) => {
        const pt = getExistingPoint(s.id, weightId);
        return pt !== null && pt !== undefined && pt !== "";
    }).length;
};

const getWeightProgress = (weightId) => {
    const total = sortedStudents.value.length;
    if (!total) return 0;
    return Math.round((getWeightFilledCount(weightId) / total) * 100);
};

// ─── Student total & grade ────────────────────────────────────────────────────

const getStudentTotalPoints = (studentId) => {
    let total = 0;
    props.weights.forEach((weight) => {
        let point =
            activeWeightId.value === weight.id
                ? resultForm.value[studentId]?.[weight.id]
                : getExistingPoint(studentId, weight.id);
        total += parseFloat(point) || 0;
    });
    return total.toFixed(2);
};

const getStudentGradeLetter = (studentId) => {
    for (const weight of props.weights) {
        const pt = getExistingPoint(studentId, weight.id);
        if (pt === null || pt === undefined || pt === "")
            return { label: "Incomplete", color: "text-gray-400" };
    }
    const total = parseFloat(getStudentTotalPoints(studentId));
    if (isNaN(total)) return { label: "—", color: "text-gray-400" };
    if (total >= 94) return { label: "A (4.0)", color: "text-green-600 dark:text-green-400 font-bold" };
    if (total >= 90) return { label: "A- (3.7)", color: "text-green-500 font-bold" };
    if (total >= 87) return { label: "B+ (3.3)", color: "text-blue-600 dark:text-blue-400 font-bold" };
    if (total >= 84) return { label: "B (3.0)", color: "text-blue-500 font-bold" };
    if (total >= 80) return { label: "B- (2.7)", color: "text-blue-400 font-bold" };
    if (total >= 77) return { label: "C+ (2.3)", color: "text-yellow-600 dark:text-yellow-400 font-bold" };
    if (total >= 74) return { label: "C (2.0)", color: "text-yellow-500 font-bold" };
    if (total >= 70) return { label: "C- (1.7)", color: "text-yellow-400 font-bold" };
    if (total >= 67) return { label: "D+ (1.3)", color: "text-orange-500 font-bold" };
    if (total >= 64) return { label: "D (1.0)", color: "text-orange-600 font-bold" };
    if (total >= 60) return { label: "D- (0.7)", color: "text-orange-700 font-bold" };
    if (total == 0) return { label: "NG", color: "text-gray-500" };
    return { label: "F (0.0)", color: "text-red-600 dark:text-red-400 font-bold" };
};

// ─── Activate weight column ───────────────────────────────────────────────────

const activateWeight = async (weightId) => {
    if (isGradeLocked.value) return;
    activeWeightId.value = weightId;
    await nextTick();
    const firstInput = document.querySelector(`[data-input-weight="${weightId}"]`);
    if (firstInput) {
        firstInput.focus();
        firstInput.scrollIntoView({ behavior: "smooth", block: "center" });
    }
};

const cancelActiveWeight = () => {
    activeWeightId.value = null;
    // Reset form values back to saved values
    sortedStudents.value.forEach((student) => {
        if (resultForm.value[student.id]) {
            const existing = getExistingPoint(student.id, activeWeightId.value);
            resultForm.value[student.id][activeWeightId.value] = existing;
        }
    });
};

// ─── Tab/Enter key navigation ─────────────────────────────────────────────────

const handleKeyNav = (event, studentIndex, weightId) => {
    if (event.key === "Tab" || event.key === "Enter") {
        event.preventDefault();
        const nextIndex = studentIndex + 1;
        if (nextIndex < sortedStudents.value.length) {
            const nextStudent = sortedStudents.value[nextIndex];
            const nextInput = document.querySelector(
                `[data-input-weight="${weightId}"][data-student-idx="${nextIndex}"]`
            );
            if (nextInput) {
                nextInput.focus();
                nextInput.select();
            }
        } else {
            // Submit on last student
            submitWeightResults();
        }
    }
};

// ─── Submit weight results ────────────────────────────────────────────────────

const submitWeightResults = () => {
    const weightId = activeWeightId.value;
    if (!weightId) return;

    const weight = props.weights.find((w) => w.id === weightId);
    const results = [];

    for (const student of sortedStudents.value) {
        const rawValue = resultForm.value[student.id]?.[weightId];
        // Skip if empty (allow partial saves)
        if (rawValue === null || rawValue === undefined || rawValue === "") continue;

        const point = parseFloat(rawValue);
        if (isNaN(point)) continue;

        if (point < 0 || point > weight.point) {
            Swal.fire({
                icon: "error",
                title: "Invalid Score",
                html: `<strong>${student.first_name} ${student.last_name ?? ""}</strong>: Score must be between <strong>0</strong> and <strong>${weight.point}</strong>`,
                confirmButtonColor: "#4f46e5",
            });
            return;
        }
        results.push({
            instructor_id: props.instructor?.id,
            weight_id: weightId,
            student_id: student.id,
            point,
        });
    }

    if (results.length === 0) {
        Swal.fire({ icon: "info", title: "No data", text: "Enter at least one score to save.", confirmButtonColor: "#4f46e5" });
        return;
    }

    isSaving.value = true;
    router.post(
        route("results.store"),
        { results },
        {
            onSuccess: () => {
                activeWeightId.value = null;
                isSaving.value = false;
                Swal.fire({
                    icon: "success",
                    title: "Saved!",
                    html: `<p>${results.length} score(s) saved for <strong>${weight.name}</strong>.</p>`,
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
            onError: (errors) => {
                isSaving.value = false;
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: Object.values(errors)[0] ?? "Failed to save results.",
                });
            },
        }
    );
};

// ─── Grade submission request ─────────────────────────────────────────────────

const isGradeSubmitable = computed(() =>
    !!(props.courseOffering?.grades_submitable || props.semester?.grades_submitable)
);

const allResultsFilled = computed(() => {
    if (!props.weights?.length || !sortedStudents.value.length) return false;
    return sortedStudents.value.every((student) =>
        props.weights.every((weight) => {
            const pt = getExistingPoint(student.id, weight.id);
            return pt !== null && pt !== undefined && pt !== "";
        })
    );
});

const canRequestSubmission = computed(() =>
    isGradeSubmitable.value &&
    allResultsFilled.value &&
    sumOfWeightPoints.value === 100 &&
    !isGradeLocked.value &&
    !isSubmissionPending.value &&
    props.courseOffering?.grade_submission_status !== "approved"
);

const requestSubmissionForm = useForm({});

const requestGradeSubmission = () => {
    Swal.fire({
        title: "Request Grade Submission?",
        html: `<p>This will send a grade submission request to the Admin/Registrar for approval.</p><p class="text-sm text-gray-500 mt-2">You will not be able to edit results once approved and locked.</p>`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        confirmButtonText: "Yes, request submission",
    }).then((result) => {
        if (result.isConfirmed) {
            requestSubmissionForm.post(
                route("grade.submission.request", { courseOffering: props.courseOffering.id }),
                {
                    onSuccess: () =>
                        Swal.fire({
                            icon: "success",
                            title: "Requested!",
                            text: "Grade submission request sent. Awaiting admin/registrar approval.",
                            timer: 3000,
                            showConfirmButton: false,
                        }),
                }
            );
        }
    });
};

// ─── Cell color coding ────────────────────────────────────────────────────────

const getCellClass = (studentId, weightId) => {
    const pt = getExistingPoint(studentId, weightId);
    if (pt !== null && pt !== undefined && pt !== "") return "text-gray-800 dark:text-gray-100";
    return "text-gray-300 dark:text-gray-600";
};
</script>

<template>
    <div class="space-y-4">

        <!-- Status Banner -->
        <div v-if="isGradeLocked"
            class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl px-4 py-3">
            <LockClosedIcon class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" />
            <div>
                <p class="text-sm font-semibold text-green-700 dark:text-green-300">Grades Approved & Locked</p>
                <p class="text-xs text-green-600 dark:text-green-400">Results are read-only. File a grade complaint to request modifications.</p>
            </div>
        </div>

        <div v-else-if="isSubmissionPending"
            class="flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl px-4 py-3">
            <ClockIcon class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" />
            <div>
                <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">Grade Submission Pending Approval</p>
                <p class="text-xs text-amber-600 dark:text-amber-400">Your submission request is awaiting Admin/Registrar approval. Results are currently read-only.</p>
            </div>
        </div>

        <div v-else-if="isSubmissionRejected"
            class="flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl px-4 py-3">
            <XCircleIcon class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" />
            <div>
                <p class="text-sm font-semibold text-red-700 dark:text-red-300">Grade Submission Rejected</p>
                <p class="text-xs text-red-600 dark:text-red-400">Your submission was rejected. Please review and resubmit. Notes: {{ courseOffering.grade_submission_notes ?? '—' }}</p>
            </div>
        </div>

        <!-- Weight Progress Summary -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2">
            <div
                v-for="weight in weights"
                :key="weight.id"
                @click="!isGradeLocked && !isSubmissionPending && activateWeight(weight.id)"
                :class="[
                    'relative rounded-lg border p-3 cursor-pointer transition-all duration-200',
                    activeWeightId === weight.id
                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 shadow-md ring-2 ring-indigo-300 dark:ring-indigo-700'
                        : isGradeLocked || isSubmissionPending
                            ? 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 cursor-not-allowed opacity-70'
                            : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-indigo-300 hover:shadow-sm',
                ]"
            >
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">{{ weight.name }}</p>
                <p class="text-[10px] text-gray-400 mb-2">{{ weight.point }}pt</p>
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                    <div
                        class="h-full rounded-full transition-all duration-500"
                        :class="getWeightProgress(weight.id) === 100 ? 'bg-green-500' : 'bg-indigo-400'"
                        :style="{ width: getWeightProgress(weight.id) + '%' }"
                    ></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">
                    {{ getWeightFilledCount(weight.id) }}/{{ sortedStudents.length }} filled
                </p>
                <div v-if="activeWeightId === weight.id" class="absolute -top-1 -right-1 w-3 h-3 bg-indigo-500 rounded-full animate-ping"></div>
                <div v-if="activeWeightId === weight.id" class="absolute -top-1 -right-1 w-3 h-3 bg-indigo-500 rounded-full"></div>
                <LockClosedIcon v-if="isGradeLocked || isSubmissionPending" class="absolute top-2 right-2 w-3 h-3 text-gray-400" />
            </div>
        </div>

        <!-- Active weight editing hint -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
            <div v-if="activeWeightId" class="flex items-center justify-between bg-indigo-600 text-white px-4 py-2.5 rounded-xl shadow-md">
                <div class="flex items-center gap-2 text-sm font-medium">
                    <PencilIcon class="w-4 h-4" />
                    Editing: <strong>{{ weights.find(w => w.id === activeWeightId)?.name }}</strong>
                    (max {{ weights.find(w => w.id === activeWeightId)?.point }}pt per student) — Tab/Enter to move between students
                </div>
                <div class="flex items-center gap-2">
                    <button
                        @click="cancelActiveWeight"
                        class="flex items-center gap-1 px-3 py-1 bg-white/20 hover:bg-white/30 rounded-lg text-xs font-medium transition"
                    >
                        <XCircleIcon class="w-4 h-4" /> Cancel
                    </button>
                    <button
                        @click="submitWeightResults"
                        :disabled="isSaving"
                        class="flex items-center gap-1 px-3 py-1 bg-white text-indigo-700 hover:bg-indigo-50 rounded-lg text-xs font-semibold shadow transition"
                    >
                        <ArrowPathIcon v-if="isSaving" class="w-4 h-4 animate-spin" />
                        <CheckCircleIcon v-else class="w-4 h-4" />
                        {{ isSaving ? 'Saving...' : 'Save Column' }}
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Results Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/60">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-8 sticky left-0 bg-gray-50 dark:bg-gray-700/60 z-10">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[160px] sticky left-8 bg-gray-50 dark:bg-gray-700/60 z-10">Student</th>
                        <th
                            v-for="weight in weights"
                            :key="weight.id"
                            class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider min-w-[120px] cursor-pointer transition-colors duration-200"
                            :class="activeWeightId === weight.id
                                ? 'bg-indigo-600 text-white'
                                : 'text-gray-500 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20'"
                            @click="!isGradeLocked && !isSubmissionPending && activateWeight(weight.id)"
                        >
                            <div class="flex flex-col items-center gap-0.5">
                                <span>{{ weight.name }}</span>
                                <span class="font-normal text-[10px] opacity-70">{{ weight.point }}pt</span>
                                <span v-if="!isGradeLocked && !isSubmissionPending && activeWeightId !== weight.id"
                                    class="text-[9px] opacity-50 flex items-center gap-0.5">
                                    <PencilIcon class="w-2.5 h-2.5" /> click to edit
                                </span>
                            </div>
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
                        class="transition-colors duration-150 hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10"
                    >
                        <td class="px-4 py-2.5 text-xs text-gray-400 sticky left-0 bg-inherit">{{ index + 1 }}</td>
                        <td class="px-4 py-2.5 text-sm font-medium text-gray-800 dark:text-gray-100 sticky left-8 bg-inherit">
                            {{ student.first_name }} {{ student.middle_name }} {{ student.last_name }}
                        </td>

                        <!-- Score cells -->
                        <td
                            v-for="weight in weights"
                            :key="weight.id"
                            class="px-2 py-1.5 text-center transition-colors duration-150"
                            :class="activeWeightId === weight.id ? 'bg-indigo-50/60 dark:bg-indigo-900/20' : ''"
                        >
                            <!-- Editable input -->
                            <input
                                v-if="activeWeightId === weight.id"
                                type="number"
                                step="0.5"
                                min="0"
                                :max="weight.point"
                                :data-input-weight="weight.id"
                                :data-student-idx="index"
                                v-model.number="resultForm[student.id][weight.id]"
                                @keydown="handleKeyNav($event, index, weight.id)"
                                class="w-20 h-8 text-center text-sm border-2 border-indigo-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 dark:text-white transition-all"
                                :class="resultForm[student.id]?.[weight.id] > weight.point ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : ''"
                            />
                            <!-- Read-only display -->
                            <span v-else>
                                <span
                                    v-if="getExistingPoint(student.id, weight.id) !== null && getExistingPoint(student.id, weight.id) !== undefined && getExistingPoint(student.id, weight.id) !== ''"
                                    class="inline-flex items-center justify-center w-14 h-7 rounded-lg text-sm font-semibold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700"
                                >
                                    {{ getExistingPoint(student.id, weight.id) }}
                                </span>
                                <span v-else class="inline-flex items-center justify-center w-14 h-7 rounded-lg text-xs text-gray-300 dark:text-gray-600 border border-dashed border-gray-200 dark:border-gray-700">
                                    —
                                </span>
                            </span>
                        </td>

                        <!-- Total -->
                        <td class="px-4 py-2.5 text-center">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ getStudentTotalPoints(student.id) }}
                            </span>
                            <span class="text-xs text-gray-400">/{{ sumOfWeightPoints }}</span>
                        </td>

                        <!-- Grade -->
                        <td class="px-4 py-2.5 text-center">
                            <span :class="getStudentGradeLetter(student.id).color" class="text-xs">
                                {{ getStudentGradeLetter(student.id).label }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="sortedStudents.length === 0">
                        <td :colspan="4 + weights.length" class="px-4 py-12 text-center text-sm text-gray-400">
                            No enrolled students found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer: Request Grade Submission -->
        <div class="pt-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-4 shadow-sm">
            <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Grade Submission</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    <span v-if="isGradeLocked" class="text-green-600 dark:text-green-400 flex items-center gap-1">
                        <CheckBadgeIcon class="w-4 h-4" /> Grades approved and locked by admin.
                    </span>
                    <span v-else-if="isSubmissionPending" class="text-amber-600 dark:text-amber-400 flex items-center gap-1">
                        <ClockIcon class="w-4 h-4" /> Submission pending admin/registrar approval.
                    </span>
                    <span v-else-if="!isGradeSubmitable" class="text-amber-600 dark:text-amber-400 flex items-center gap-1">
                        <ClockIcon class="w-4 h-4" /> Grade submission has not been opened by the admin for this semester or course assessment.
                    </span>
                    <span v-else-if="!allResultsFilled" class="text-red-500 flex items-center gap-1">
                        <ExclamationTriangleIcon class="w-4 h-4" /> Fill all student scores before requesting submission.
                    </span>
                    <span v-else-if="sumOfWeightPoints !== 100" class="text-red-500 flex items-center gap-1">
                        <ExclamationTriangleIcon class="w-4 h-4" /> Assessment weights must total exactly 100pt (currently {{ sumOfWeightPoints }}pt).
                    </span>
                    <span v-else class="text-indigo-600 dark:text-indigo-400">All scores filled. Ready to request submission.</span>
                </p>
            </div>
            <button
                v-if="!isGradeLocked && !isSubmissionPending"
                @click="requestGradeSubmission"
                :disabled="!canRequestSubmission"
                class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold shadow transition-all duration-200"
                :class="canRequestSubmission
                    ? 'bg-indigo-600 hover:bg-indigo-700 text-white hover:shadow-md hover:-translate-y-0.5'
                    : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'"
            >
                <PaperAirplaneIcon class="w-4 h-4" />
                Request Grade Submission
            </button>
            <span v-else-if="isSubmissionPending" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                <ClockIcon class="w-4 h-4" /> Awaiting Approval
            </span>
            <span v-else-if="isGradeLocked" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                <LockClosedIcon class="w-4 h-4" /> Grades Locked
            </span>
        </div>
    </div>
</template>
