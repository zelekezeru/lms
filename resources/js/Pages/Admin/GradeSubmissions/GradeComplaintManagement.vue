<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    ExclamationCircleIcon,
    CheckBadgeIcon,
    XCircleIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    ArrowPathIcon,
    ClockIcon,
    PencilIcon,
    AcademicCapIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    complaints: { type: Array, required: true },
});

const searchQuery = ref("");
const filterStatus = ref("all");
const actionComplaint = ref(null);
const actionType = ref(null); // 'approve' | 'reject' | 'improve'
const modalNotes = ref("");
const improvedPoint = ref("");
const improvedLetter = ref("");

const filteredComplaints = computed(() =>
    props.complaints.filter((c) => {
        const matchSearch =
            !searchQuery.value ||
            c.student?.first_name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            c.student?.last_name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            c.course?.name?.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchStatus = filterStatus.value === "all" || c.status === filterStatus.value;
        return matchSearch && matchStatus;
    })
);

const stats = computed(() => ({
    total: props.complaints.length,
    pending: props.complaints.filter((c) => c.status === "pending").length,
    approved: props.complaints.filter((c) => c.status === "approved").length,
    rejected: props.complaints.filter((c) => c.status === "rejected").length,
}));

// ─── Forms ────────────────────────────────────────────────────────────────────

const approveForm = useForm({ review_notes: "" });
const rejectForm = useForm({ review_notes: "" });
const improveForm = useForm({ improved_grade_point: "", improved_grade_letter: "", notes: "" });

const openModal = (complaint, type) => {
    actionComplaint.value = complaint;
    actionType.value = type;
    modalNotes.value = "";
    improvedPoint.value = complaint.original_grade_point ?? "";
    improvedLetter.value = complaint.original_grade_letter ?? "";
};

const closeModal = () => {
    actionComplaint.value = null;
    actionType.value = null;
    modalNotes.value = "";
};

const confirmApprove = () => {
    approveForm.review_notes = modalNotes.value;
    approveForm.post(
        route("grade.complaints.approve", { gradeComplaint: actionComplaint.value.id }),
        {
            onSuccess: () => {
                closeModal();
                Swal.fire({ icon: "success", title: "Complaint Approved", text: "The instructor can now submit an improved grade.", timer: 2500, showConfirmButton: false });
            },
        }
    );
};

const confirmReject = () => {
    if (!modalNotes.value.trim()) {
        Swal.fire({ icon: "warning", title: "Notes required", text: "Please provide a reason for the rejection." });
        return;
    }
    rejectForm.review_notes = modalNotes.value;
    rejectForm.post(
        route("grade.complaints.reject", { gradeComplaint: actionComplaint.value.id }),
        {
            onSuccess: () => {
                closeModal();
                Swal.fire({ icon: "success", title: "Complaint Rejected", timer: 2000, showConfirmButton: false });
            },
        }
    );
};

const confirmImprove = () => {
    if (!improvedPoint.value || !improvedLetter.value) {
        Swal.fire({ icon: "warning", title: "All fields required", text: "Please fill in both the improved grade point and letter." });
        return;
    }
    improveForm.improved_grade_point = improvedPoint.value;
    improveForm.improved_grade_letter = improvedLetter.value;
    improveForm.notes = modalNotes.value;
    improveForm.post(
        route("grade.complaints.improve", { gradeComplaint: actionComplaint.value.id }),
        {
            onSuccess: () => {
                closeModal();
                Swal.fire({ icon: "success", title: "Grade Improved!", text: "The improved grade has been saved and the grade re-locked.", timer: 2500, showConfirmButton: false });
            },
        }
    );
};

const getStatusBadge = (status) => {
    switch (status) {
        case "pending": return "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300";
        case "approved": return "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300";
        case "rejected": return "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300";
        default: return "bg-gray-100 text-gray-600";
    }
};

const gradeLetterOptions = ["A", "A-", "B+", "B", "B-", "C+", "C", "C-", "D+", "D", "D-", "F", "NG"];

const formatDate = (d) => d ? new Date(d).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" }) : "—";
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <ExclamationCircleIcon class="w-8 h-8 text-amber-500" />
                        Grade Complaint Management
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                        Review grade complaints from students and instructors. Approved complaints allow instructors to submit improved grades.
                    </p>
                </div>
                <a :href="route('grade.submissions.index')"
                    class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
                    <AcademicCapIcon class="w-4 h-4" /> Grade Submissions
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total }}</p>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-5 shadow-sm">
                    <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider flex items-center gap-1"><ClockIcon class="w-3.5 h-3.5" /> Pending</p>
                    <p class="text-3xl font-bold text-amber-700 dark:text-amber-300 mt-1">{{ stats.pending }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-5 shadow-sm">
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wider flex items-center gap-1"><CheckBadgeIcon class="w-3.5 h-3.5" /> Approved</p>
                    <p class="text-3xl font-bold text-green-700 dark:text-green-300 mt-1">{{ stats.approved }}</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-5 shadow-sm">
                    <p class="text-xs font-semibold text-red-600 uppercase tracking-wider flex items-center gap-1"><XCircleIcon class="w-3.5 h-3.5" /> Rejected</p>
                    <p class="text-3xl font-bold text-red-700 dark:text-red-300 mt-1">{{ stats.rejected }}</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                <div class="relative flex-1">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    <input v-model="searchQuery" type="text" placeholder="Search by student name or course..."
                        class="w-full pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
                </div>
                <div class="flex items-center gap-2">
                    <FunnelIcon class="w-4 h-4 text-gray-400" />
                    <select v-model="filterStatus" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/60">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course / Section</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Filed By</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Original Grade</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Improved Grade</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Filed On</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-if="filteredComplaints.length === 0">
                            <td colspan="9" class="px-5 py-16 text-center text-sm text-gray-400">
                                <ExclamationCircleIcon class="w-10 h-10 mx-auto mb-3 text-gray-300" />
                                No grade complaints found.
                            </td>
                        </tr>
                        <tr v-for="(complaint, index) in filteredComplaints" :key="complaint.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                            <td class="px-5 py-4 text-sm text-gray-400">{{ index + 1 }}</td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ complaint.student?.first_name }} {{ complaint.student?.last_name }}</p>
                                <p class="text-xs text-gray-400">ID: {{ complaint.student?.idNo ?? "—" }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ complaint.course?.name }}</p>
                                <p class="text-xs text-gray-400">{{ complaint.section?.name }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300 capitalize">{{ complaint.filed_by_role }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex flex-col items-center">
                                    <span class="text-sm font-bold text-gray-800 dark:text-white">{{ complaint.original_grade_letter }}</span>
                                    <span class="text-xs text-gray-400">{{ complaint.original_grade_point }}pt</span>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span v-if="complaint.improved_grade_point" class="inline-flex flex-col items-center">
                                    <span class="text-sm font-bold text-green-700 dark:text-green-300">{{ complaint.improved_grade_letter }}</span>
                                    <span class="text-xs text-gray-400">{{ complaint.improved_grade_point }}pt</span>
                                    <span v-if="complaint.improvement_applied" class="text-[10px] text-green-600">Applied ✓</span>
                                </span>
                                <span v-else class="text-xs text-gray-300 dark:text-gray-600">—</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold" :class="getStatusBadge(complaint.status)">
                                    <ClockIcon v-if="complaint.status === 'pending'" class="w-3 h-3" />
                                    <CheckBadgeIcon v-else-if="complaint.status === 'approved'" class="w-3 h-3" />
                                    <XCircleIcon v-else class="w-3 h-3" />
                                    {{ complaint.status?.charAt(0).toUpperCase() + complaint.status?.slice(1) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">{{ formatDate(complaint.created_at) }}</td>
                            <td class="px-5 py-4 text-center">
                                <!-- Pending: show approve/reject -->
                                <div v-if="complaint.status === 'pending'" class="flex items-center justify-center gap-2">
                                    <button @click="openModal(complaint, 'approve')" class="flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all">
                                        <CheckBadgeIcon class="w-3.5 h-3.5" /> Approve
                                    </button>
                                    <button @click="openModal(complaint, 'reject')" class="flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all">
                                        <XCircleIcon class="w-3.5 h-3.5" /> Reject
                                    </button>
                                </div>
                                <!-- Approved but not improved: instructor can improve -->
                                <div v-else-if="complaint.status === 'approved' && !complaint.improvement_applied">
                                    <button @click="openModal(complaint, 'improve')" class="flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all">
                                        <PencilIcon class="w-3.5 h-3.5" /> Submit Improvement
                                    </button>
                                </div>
                                <span v-else class="text-xs text-gray-400">—</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Modal -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100">
            <div v-if="actionType" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ actionType === 'approve' ? 'Approve Complaint' : actionType === 'reject' ? 'Reject Complaint' : 'Submit Improved Grade' }}
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Complaint Details -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-3 text-sm space-y-1">
                        <p><span class="text-gray-500">Student:</span> <span class="font-semibold text-gray-800 dark:text-gray-100">{{ actionComplaint?.student?.first_name }} {{ actionComplaint?.student?.last_name }}</span></p>
                        <p><span class="text-gray-500">Course:</span> <span class="text-gray-700 dark:text-gray-300">{{ actionComplaint?.course?.name }}</span></p>
                        <p><span class="text-gray-500">Original Grade:</span> <span class="font-bold">{{ actionComplaint?.original_grade_letter }} ({{ actionComplaint?.original_grade_point }}pt)</span></p>
                        <p class="text-xs text-gray-400 italic mt-1 border-t border-gray-200 dark:border-gray-700 pt-1">"{{ actionComplaint?.reason }}"</p>
                    </div>

                    <!-- Improve fields -->
                    <div v-if="actionType === 'improve'" class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Improved Point</label>
                                <input type="number" v-model="improvedPoint" min="0" max="100" step="0.5"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Improved Letter</label>
                                <select v-model="improvedLetter"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                    <option v-for="gl in gradeLetterOptions" :key="gl" :value="gl">{{ gl }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 rounded-xl px-4 py-3 text-xs text-indigo-700 dark:text-indigo-300">
                            The original grade ({{ actionComplaint?.original_grade_letter }} / {{ actionComplaint?.original_grade_point }}pt) will remain in the audit log for reference.
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ actionType === 'improve' ? 'Notes (optional)' : actionType === 'reject' ? 'Rejection Reason *' : 'Notes (optional)' }}
                        </label>
                        <textarea v-model="modalNotes" rows="3"
                            :placeholder="actionType === 'reject' ? 'Explain why this complaint is rejected...' : 'Optional notes...'"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none transition"></textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-1">
                        <button
                            @click="actionType === 'approve' ? confirmApprove() : actionType === 'reject' ? confirmReject() : confirmImprove()"
                            :disabled="approveForm.processing || rejectForm.processing || improveForm.processing"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all disabled:opacity-50"
                            :class="actionType === 'approve' ? 'bg-green-600 hover:bg-green-700' : actionType === 'reject' ? 'bg-red-600 hover:bg-red-700' : 'bg-indigo-600 hover:bg-indigo-700'"
                        >
                            <ArrowPathIcon v-if="approveForm.processing || rejectForm.processing || improveForm.processing" class="w-4 h-4 animate-spin" />
                            {{ actionType === 'approve' ? 'Approve Complaint' : actionType === 'reject' ? 'Reject Complaint' : 'Apply Improvement' }}
                        </button>
                        <button @click="closeModal" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-xl transition-all">Cancel</button>
                    </div>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>
