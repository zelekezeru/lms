<script setup>
import { ref } from "vue";
import Button from "primevue/button";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import { AcademicCapIcon, ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    ExclamationCircleIcon,
    ClockIcon,
    CheckBadgeIcon,
    XCircleIcon,
    ArrowPathIcon
} from "@heroicons/vue/24/solid";

const props = defineProps({
    student: { type: Object, required: true },
    grades: { type: Array, required: true },
    results: { type: Array, default: () => [] }
});

const showGradesModal = ref(false);
const selectedGrade = ref(null);

const complaintModal = ref(false);
const complaintGrade = ref(null);
const complaintReason = ref("");

const complaintForm = useForm({
    grade_id: null,
    reason: "",
    filed_by_role: "student",
});

function openModal(grade) {
    selectedGrade.value = grade;
    showGradesModal.value = true;
}

function closeModal() {
    selectedGrade.value = null;
    showGradesModal.value = false;
}

const openComplaintModal = (grade) => {
    complaintGrade.value = grade;
    complaintReason.value = "";
    complaintModal.value = true;
};

const submitComplaint = () => {
    if (!complaintReason.value.trim()) return;

    complaintForm.grade_id = complaintGrade.value.id;
    complaintForm.reason = complaintReason.value;

    complaintForm.post(route("grade.complaints.store"), {
        onSuccess: () => {
            complaintModal.value = false;
            Swal.fire({
                icon: "success",
                title: "Complaint Filed!",
                text: "Your grade complaint has been submitted successfully to the Academic Dean.",
                timer: 2000,
                showConfirmButton: false,
            });
            // Append complaint locally so UI updates immediately
            if (complaintGrade.value) {
                if (!complaintGrade.value.complaints) {
                    complaintGrade.value.complaints = [];
                }
                complaintGrade.value.complaints.push({
                    status: "pending",
                    reason: complaintReason.value,
                    created_at: new Date().toISOString()
                });
            }
        },
        onError: (errors) => {
            Swal.fire({
                icon: "error",
                title: "Failed to Submit",
                text: Object.values(errors)[0] ?? "An error occurred while submitting the complaint.",
            });
        }
    });
};

// PDF Export function for grades
const exportGradesPDF = () => {
    const doc = new jsPDF({
        orientation: "landscape",
        unit: "mm",
        format: "a4",
    });
    const student = props.student;
    const grades = props.grades;
    const pageWidth = doc.internal.pageSize.getWidth();
    let y = 20;

    doc.setFontSize(16);
    doc.text("Student Grades Report", pageWidth / 2, y, { align: "center" });
    y += 10;

    doc.setFontSize(12);
    doc.text(
        `Name: ${student.firstName} ${student.lastName}`,
        14,
        y
    );
    y += 7;
    doc.text(
        `Student ID: ${student.idNo || student.id}`,
        14,
        y
    );
    y += 10;

    // Table data
    const tableData = grades.map((g, i) => [
        i + 1,
        g.course?.name || "N/A",
        g.course?.credit_hours || "N/A",
        g.grade_letter || "N/A",
        g.semester?.name || "N/A"
    ]);

    autoTable(doc, {
        head: [["#", "Course Name", "Credit Hours", "Grade", "Semester"]],
        body: tableData,
        startY: y,
        theme: "grid",
        styles: { fontSize: 10 },
        headStyles: { fillColor: [79, 70, 229], textColor: 255 },
        margin: { left: 14, right: 10 },
    });

    doc.save(
        `${student.firstName}_${student.lastName}_Grades.pdf`
    );
};
</script>

<template>
    <div class="p-4 space-y-6">
        <!-- Title and Export -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <AcademicCapIcon class="w-7 h-7 text-indigo-500" />
                Grades for {{ student.firstName }} {{ student.lastName }}
            </h1>
            <button
                @click="exportGradesPDF"
                class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-sm hover:shadow-md transition duration-200"
            >
                <ArrowDownTrayIcon class="w-4 h-4" />
                Download PDF Report
            </button>
        </div>

        <!-- Grades Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-150 dark:border-gray-700 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-xs text-gray-500 uppercase tracking-wider w-12">#</th>
                            <th class="px-4 py-3 font-semibold text-xs text-gray-500 uppercase tracking-wider">Course Name</th>
                            <th class="px-4 py-3 font-semibold text-xs text-gray-500 uppercase tracking-wider">Credits</th>
                            <th class="px-4 py-3 font-semibold text-xs text-gray-500 uppercase tracking-wider">Grade</th>
                            <th class="px-4 py-3 font-semibold text-xs text-gray-500 uppercase tracking-wider">Semester</th>
                            <th class="px-4 py-3 font-semibold text-xs text-gray-500 uppercase tracking-wider text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody v-if="grades.length > 0" class="divide-y divide-gray-150 dark:divide-gray-700/50">
                        <tr
                            v-for="(grade, index) in grades"
                            :key="grade.id"
                            class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition duration-150"
                        >
                            <td class="px-4 py-3.5 text-sm text-gray-500 dark:text-gray-400 font-medium">{{ index + 1 }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-900 dark:text-white font-bold">{{ grade.course.name }}</td>
                            <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-300">{{ grade.course.credit_hours }} credits</td>
                            <td class="px-4 py-3.5 text-sm">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                    {{ grade.grade_letter }}
                                </span>
                                <span v-if="grade.complaints && grade.complaints.length > 0" class="ml-2 inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                    :class="grade.complaints[0].status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                        : grade.complaints[0].status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'">
                                    Complaint: {{ grade.complaints[0].status }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-gray-600 dark:text-gray-300">
                                {{ grade.semester?.name || 'N/A' }}
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <PrimaryButton size="sm" @click="openModal(grade)">View Details</PrimaryButton>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-500 dark:text-gray-400">
                                No course grades available yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grade Details Modal -->
    <Modal :show="showGradesModal" @close="closeModal" :max-width="'lg'">
        <div v-if="selectedGrade" class="p-6 space-y-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2 border-b border-gray-150 dark:border-gray-700 pb-3">
                <AcademicCapIcon class="w-6 h-6 text-indigo-500" />
                Grade Details
            </h2>
            
            <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-medium text-gray-400">Course:</span>
                    <span class="col-span-2 font-bold text-gray-900 dark:text-white">{{ selectedGrade.course.name }} ({{ selectedGrade.course.code }})</span>
                </div>
                
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-medium text-gray-400">Semester:</span>
                    <span class="col-span-2">{{ selectedGrade.semester?.name || 'N/A' }}</span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <span class="font-medium text-gray-400">Credits:</span>
                    <span class="col-span-2">{{ selectedGrade.course.credit_hours }} Credit Hours</span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <span class="font-medium text-gray-400">Grade Letter:</span>
                    <span class="col-span-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                            {{ selectedGrade.grade_letter }}
                        </span>
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <span class="font-medium text-gray-400">Grade Score:</span>
                    <span class="col-span-2 font-bold">{{ parseFloat(selectedGrade.grade_point).toFixed(1) }}%</span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <span class="font-medium text-gray-400">Status:</span>
                    <span class="col-span-2 capitalize font-semibold" :class="selectedGrade.grade_status === 'completed' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'">
                        {{ selectedGrade.grade_status }}
                    </span>
                </div>

                <div class="border-t border-gray-150 dark:border-gray-700 pt-4">
                    <span class="font-bold text-gray-900 dark:text-white block mb-1">Instructor Comments:</span>
                    <p class="italic text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-150 dark:border-gray-700">
                        {{ selectedGrade.grade_comment || "No feedback comments submitted by the instructor." }}
                    </p>
                </div>

                <!-- Complaint Section -->
                <div v-if="selectedGrade.complaints && selectedGrade.complaints.length > 0" class="border-t border-gray-150 dark:border-gray-700 pt-4 space-y-3">
                    <span class="font-bold text-gray-900 dark:text-white block">Grade Complaint Status:</span>
                    <div class="p-3 rounded-xl border border-gray-150 dark:border-gray-700 space-y-2 bg-gray-50 dark:bg-gray-900/40">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500">Current Status:</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full"
                                :class="selectedGrade.complaints[0].status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                    : selectedGrade.complaints[0].status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                                    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'">
                                <component :is="selectedGrade.complaints[0].status === 'approved' ? CheckBadgeIcon : selectedGrade.complaints[0].status === 'rejected' ? XCircleIcon : ClockIcon" class="w-3 h-3" />
                                {{ selectedGrade.complaints[0].status?.toUpperCase() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-700 dark:text-gray-300"><span class="font-medium text-gray-500">Your Reason:</span> {{ selectedGrade.complaints[0].reason }}</p>
                        <div v-if="selectedGrade.complaints[0].review_notes" class="text-xs bg-white dark:bg-gray-800 p-2 rounded border border-gray-150 dark:border-gray-700 mt-2">
                            <span class="font-semibold text-gray-600 dark:text-gray-300 block mb-0.5">Dean Review Notes:</span>
                            <p class="text-gray-700 dark:text-gray-300 italic">{{ selectedGrade.complaints[0].review_notes }}</p>
                        </div>
                    </div>
                </div>

                <div v-else-if="selectedGrade.is_locked" class="border-t border-gray-150 dark:border-gray-700 pt-4">
                    <button
                        @click="openComplaintModal(selectedGrade)"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow transition-all duration-200"
                    >
                        <ExclamationCircleIcon class="w-4 h-4" /> File Grade Complaint
                    </button>
                </div>
            </div>
            
            <div class="flex justify-end pt-2">
                <PrimaryButton @click="closeModal">Close</PrimaryButton>
            </div>
        </div>
    </Modal>

    <!-- Complaint Filing Modal -->
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
                    <p><span class="font-medium text-gray-500">Course:</span> <span class="text-gray-800 dark:text-gray-100 font-semibold">{{ complaintGrade.course?.name }}</span></p>
                    <p><span class="font-medium text-gray-500">Grade:</span>
                        <span class="ml-1 inline-flex px-2 py-0.5 text-xs font-bold rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">{{ complaintGrade.grade_letter }}</span>
                        <span class="ml-1 text-gray-600 dark:text-gray-300">({{ parseFloat(complaintGrade.grade_point).toFixed(1) }}%)</span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Complaint *</label>
                    <textarea
                        v-model="complaintReason"
                        rows="4"
                        placeholder="Explain clearly why you believe your grade is incorrect and should be reviewed..."
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none transition"
                    ></textarea>
                    <p class="text-xs text-gray-400 mt-1">{{ complaintReason.length }}/1000 characters</p>
                </div>

                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl px-4 py-3 text-xs text-amber-700 dark:text-amber-300">
                    <strong>Note:</strong> This complaint will be reviewed by the Academic Dean. If approved, your instructor will be enabled to submit an improved grade. All records are audited.
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
</template>