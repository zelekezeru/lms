<script setup>
import { ref, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    AcademicCapIcon,
    ClockIcon,
    CheckBadgeIcon,
    XCircleIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    ArrowPathIcon,
    LockClosedIcon,
    DocumentTextIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    offerings: { type: Array, required: true },
    semesters: { type: Array, default: () => [] },
});

const activeTab = ref("requests");
const searchQuery = ref("");
const filterStatus = ref("all");

const offeringsSearchQuery = ref("");
const semestersSearchQuery = ref("");

// Filter offerings that have requested grade submission (i.e. status is set)
const submissionRequests = computed(() => {
    return props.offerings.filter((o) => o.grade_submission_status !== null && o.grade_submission_status !== "");
});

const filteredRequests = computed(() => {
    return submissionRequests.value.filter((o) => {
        const matchesSearch =
            !searchQuery.value ||
            o.course?.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            o.section?.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            o.instructor?.user?.name?.toLowerCase().includes(searchQuery.value.toLowerCase());

        const matchesStatus =
            filterStatus.value === "all" || o.grade_submission_status === filterStatus.value;

        return matchesSearch && matchesStatus;
    });
});

const filteredOfferings = computed(() => {
    return props.offerings.filter((o) => {
        return (
            !offeringsSearchQuery.value ||
            o.course?.name?.toLowerCase().includes(offeringsSearchQuery.value.toLowerCase()) ||
            o.section?.name?.toLowerCase().includes(offeringsSearchQuery.value.toLowerCase()) ||
            o.instructor?.user?.name?.toLowerCase().includes(offeringsSearchQuery.value.toLowerCase())
        );
    });
});

const filteredSemesters = computed(() => {
    return props.semesters.filter((s) => {
        return (
            !semestersSearchQuery.value ||
            s.name?.toLowerCase().includes(semestersSearchQuery.value.toLowerCase()) ||
            s.year?.name?.toLowerCase().includes(semestersSearchQuery.value.toLowerCase())
        );
    });
});

const stats = computed(() => ({
    total: submissionRequests.value.length,
    pending: submissionRequests.value.filter((o) => o.grade_submission_status === "pending").length,
    approved: submissionRequests.value.filter((o) => o.grade_submission_status === "approved").length,
    rejected: submissionRequests.value.filter((o) => o.grade_submission_status === "rejected").length,
}));

const toggleSemesterSubmitable = (semesterId) => {
    router.post(route("grade.submission.toggle-semester", { semester: semesterId }), {}, {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Toggled!",
                text: "Semester grade submission window updated.",
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
};

const toggleOfferingSubmitable = (offeringId) => {
    router.post(route("grade.submission.toggle-offering", { courseOffering: offeringId }), {}, {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Toggled!",
                text: "Section course assessment grade submission window updated.",
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
};

// ─── Approve/Reject Actions ───────────────────────────────────────────────────

const approveForm = useForm({ notes: "" });
const rejectForm = useForm({ notes: "" });
const actionOffering = ref(null);
const actionModal = ref(null); // 'approve' | 'reject'
const modalNotes = ref("");

const openModal = (offering, type) => {
    actionOffering.value = offering;
    actionModal.value = type;
    modalNotes.value = "";
};

const closeModal = () => {
    actionOffering.value = null;
    actionModal.value = null;
    modalNotes.value = "";
};

const confirmApprove = () => {
    Swal.fire({
        title: "Approve Grade Submission?",
        html: `<p>This will <strong>lock all grades</strong> for <strong>${actionOffering.value.course?.name}</strong> — Section ${actionOffering.value.section?.name}.</p><p class="text-sm text-gray-500 mt-2">Once approved, grades cannot be changed without an official complaint process.</p>`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#16a34a",
        confirmButtonText: "Approve & Lock Grades",
    }).then((result) => {
        if (result.isConfirmed) {
            approveForm.notes = modalNotes.value;
            approveForm.post(
                route("grade.submission.approve", { courseOffering: actionOffering.value.id }),
                {
                    onSuccess: () => {
                        closeModal();
                        Swal.fire({
                            icon: "success",
                            title: "Approved!",
                            text: "Grades have been approved and locked.",
                            timer: 2500,
                            showConfirmButton: false,
                        });
                    },
                }
            );
        }
    });
};

const confirmReject = () => {
    if (!modalNotes.value.trim()) {
        Swal.fire({ icon: "warning", title: "Notes required", text: "Please provide rejection notes so the instructor knows what to fix." });
        return;
    }
    rejectForm.notes = modalNotes.value;
    rejectForm.post(
        route("grade.submission.reject", { courseOffering: actionOffering.value.id }),
        {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    icon: "success",
                    title: "Rejected",
                    text: "Submission rejected. Instructor will be notified.",
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
        }
    );
};

const getStatusBadge = (status) => {
    switch (status) {
        case "pending": return "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300";
        case "approved": return "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300";
        case "rejected": return "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300";
        default: return "bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400";
    }
};

const getStatusIcon = (status) => {
    switch (status) {
        case "pending": return ClockIcon;
        case "approved": return CheckBadgeIcon;
        case "rejected": return XCircleIcon;
        default: return DocumentTextIcon;
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return "—";
    return new Date(dateStr).toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric", hour: "2-digit", minute: "2-digit" });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <AcademicCapIcon class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
                        Grade Submission Management
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
                        Configure grade submission windows and review requests. Approved requests lock student grades.
                    </p>
                </div>
                <a
                    :href="route('grade.complaints.index')"
                    class="flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow transition-all"
                >
                    <DocumentTextIcon class="w-4 h-4" />
                    Grade Complaints
                </a>
            </div>

            <!-- Navigation Tabs -->
            <div class="border-b border-gray-250 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                        @click="activeTab = 'requests'"
                        :class="[activeTab === 'requests' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300', 'whitespace-nowrap py-4 px-1 font-medium text-sm transition-all duration-150']"
                    >
                        Grade Submission Requests ({{ stats.pending }} pending)
                    </button>
                    <button
                        @click="activeTab = 'offerings'"
                        :class="[activeTab === 'offerings' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300', 'whitespace-nowrap py-4 px-1 font-medium text-sm transition-all duration-150']"
                    >
                        Section Course Window Toggles
                    </button>
                    <button
                        @click="activeTab = 'semesters'"
                        :class="[activeTab === 'semesters' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 font-bold border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300', 'whitespace-nowrap py-4 px-1 font-medium text-sm transition-all duration-150']"
                    >
                        Semester Window Toggles
                    </button>
                </nav>
            </div>

            <!-- Tab 1: Requests -->
            <div v-if="activeTab === 'requests'" class="space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Requests</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total }}</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-5 shadow-sm">
                        <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1"><ClockIcon class="w-3.5 h-3.5" /> Pending</p>
                        <p class="text-3xl font-bold text-amber-700 dark:text-amber-300 mt-1">{{ stats.pending }}</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-5 shadow-sm">
                        <p class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider flex items-center gap-1"><CheckBadgeIcon class="w-3.5 h-3.5" /> Approved</p>
                        <p class="text-3xl font-bold text-green-700 dark:text-green-300 mt-1">{{ stats.approved }}</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-5 shadow-sm">
                        <p class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider flex items-center gap-1"><XCircleIcon class="w-3.5 h-3.5" /> Rejected</p>
                        <p class="text-3xl font-bold text-red-700 dark:text-red-300 mt-1">{{ stats.rejected }}</p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <div class="relative flex-1">
                        <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search requests by course, section, or instructor..."
                            class="w-full pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <FunnelIcon class="w-4 h-4 text-gray-400" />
                        <select
                            v-model="filterStatus"
                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                        >
                            <option value="all">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <!-- Submissions Table -->
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/60">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Section</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Instructor</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Requested At</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Approved By</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="filteredRequests.length === 0">
                                <td colspan="8" class="px-5 py-16 text-center text-sm text-gray-400 dark:text-gray-500">
                                    <AcademicCapIcon class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                                    No grade submission requests found.
                                </td>
                            </tr>
                            <tr
                                v-for="(offering, index) in filteredRequests"
                                :key="offering.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150"
                            >
                                <td class="px-5 py-4 text-sm text-gray-400">{{ index + 1 }}</td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ offering.course?.name }}</p>
                                    <p class="text-xs text-gray-400">{{ offering.course?.code }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ offering.section?.name }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ offering.instructor?.user?.name ?? "—" }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold" :class="getStatusBadge(offering.grade_submission_status)">
                                        <component :is="getStatusIcon(offering.grade_submission_status)" class="w-3.5 h-3.5" />
                                        {{ offering.grade_submission_status?.charAt(0).toUpperCase() + offering.grade_submission_status?.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">
                                    {{ formatDate(offering.grade_submission_requested_at) }}
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">
                                    <div v-if="offering.grade_submission_status === 'approved'">
                                        <p class="font-medium text-green-700 dark:text-green-300">{{ offering.grade_submission_approved_by?.name ?? "—" }}</p>
                                        <p class="text-[10px] text-gray-400">{{ formatDate(offering.grade_submission_approved_at) }}</p>
                                    </div>
                                    <span v-else>—</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div v-if="offering.grade_submission_status === 'pending'" class="flex items-center justify-center gap-2">
                                        <button
                                            @click="openModal(offering, 'approve')"
                                            class="flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-200"
                                        >
                                            <CheckBadgeIcon class="w-3.5 h-3.5" /> Approve
                                        </button>
                                        <button
                                            @click="openModal(offering, 'reject')"
                                            class="flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-200"
                                        >
                                            <XCircleIcon class="w-3.5 h-3.5" /> Reject
                                        </button>
                                    </div>
                                    <span v-else-if="offering.grade_submission_status === 'approved'" class="flex items-center justify-center gap-1 text-xs text-green-600 dark:text-green-400 font-medium">
                                        <LockClosedIcon class="w-3.5 h-3.5" /> Locked
                                    </span>
                                    <span v-else class="text-xs text-gray-400">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Course Offering Toggles -->
            <div v-if="activeTab === 'offerings'" class="space-y-6">
                <!-- Search -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <div class="relative">
                        <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                        <input
                            v-model="offeringsSearchQuery"
                            type="text"
                            placeholder="Search section course assessments by name, code, section, or instructor..."
                            class="w-full pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                        />
                    </div>
                </div>

                <!-- Toggles Table -->
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/60">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Course</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Section</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Instructor</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitable Status</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="filteredOfferings.length === 0">
                                <td colspan="6" class="px-5 py-16 text-center text-sm text-gray-400 dark:text-gray-500">
                                    No section course assessments found.
                                </td>
                            </tr>
                            <tr
                                v-for="(offering, index) in filteredOfferings"
                                :key="offering.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150"
                            >
                                <td class="px-5 py-4 text-sm text-gray-400">{{ index + 1 }}</td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ offering.course?.name }}</p>
                                    <p class="text-xs text-gray-400">{{ offering.course?.code }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ offering.section?.name }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ offering.instructor?.user?.name ?? "—" }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex px-3 py-1 text-xs font-semibold rounded-full"
                                        :class="offering.grades_submitable ? 'bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700/50 dark:text-gray-400'"
                                    >
                                        {{ offering.grades_submitable ? '✓ Submitable Enabled' : '✗ Submission Closed' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button
                                        @click="toggleOfferingSubmitable(offering.id)"
                                        class="px-4 py-1.5 text-xs font-semibold rounded-lg shadow-sm transition-all duration-200"
                                        :class="offering.grades_submitable ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white'"
                                    >
                                        {{ offering.grades_submitable ? 'Disable' : 'Enable' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Semester Toggles -->
            <div v-if="activeTab === 'semesters'" class="space-y-6">
                <!-- Search -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                    <div class="relative">
                        <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                        <input
                            v-model="semestersSearchQuery"
                            type="text"
                            placeholder="Search semesters by name or academic year..."
                            class="w-full pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                        />
                    </div>
                </div>

                <!-- Toggles Table -->
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/60">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Semester Name</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Academic Year</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitable Status</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-if="filteredSemesters.length === 0">
                                <td colspan="5" class="px-5 py-16 text-center text-sm text-gray-400 dark:text-gray-500">
                                    No semesters found.
                                </td>
                            </tr>
                            <tr
                                v-for="(semester, index) in filteredSemesters"
                                :key="semester.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150"
                            >
                                <td class="px-5 py-4 text-sm text-gray-400">{{ index + 1 }}</td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ semester.name }}</p>
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ semester.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    Year: {{ semester.year?.name || 'N/A' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex px-3 py-1 text-xs font-semibold rounded-full"
                                        :class="semester.grades_submitable ? 'bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700/50 dark:text-gray-400'"
                                    >
                                        {{ semester.grades_submitable ? '✓ Submitable Enabled' : '✗ Submission Closed' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button
                                        @click="toggleSemesterSubmitable(semester.id)"
                                        class="px-4 py-1.5 text-xs font-semibold rounded-lg shadow-sm transition-all duration-200"
                                        :class="semester.grades_submitable ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white'"
                                    >
                                        {{ semester.grades_submitable ? 'Disable' : 'Enable' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <!-- Approve/Reject Modal -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
        >
            <div v-if="actionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <component :is="actionModal === 'approve' ? CheckBadgeIcon : XCircleIcon"
                                class="w-5 h-5"
                                :class="actionModal === 'approve' ? 'text-green-600' : 'text-red-600'" />
                            {{ actionModal === 'approve' ? 'Approve Grade Submission' : 'Reject Grade Submission' }}
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-3 text-sm space-y-1">
                        <p><span class="text-gray-500">Course:</span> <span class="font-semibold text-gray-800 dark:text-gray-100">{{ actionOffering?.course?.name }}</span></p>
                        <p><span class="text-gray-500">Section:</span> <span class="text-gray-700 dark:text-gray-300">{{ actionOffering?.section?.name }}</span></p>
                        <p><span class="text-gray-500">Instructor:</span> <span class="text-gray-700 dark:text-gray-300">{{ actionOffering?.instructor?.user?.name }}</span></p>
                    </div>

                    <div v-if="actionModal === 'approve'" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl px-4 py-3 text-xs text-green-700 dark:text-green-300">
                        <strong>Warning:</strong> Approving will lock all grades permanently. Students and instructors must file a formal complaint to request any changes.
                    </div>
                    <div v-else class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl px-4 py-3 text-xs text-red-700 dark:text-red-300">
                        <strong>Required:</strong> Provide notes explaining why the submission was rejected so the instructor can make corrections.
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ actionModal === 'approve' ? 'Notes (optional)' : 'Rejection Reason *' }}
                        </label>
                        <textarea
                            v-model="modalNotes"
                            rows="3"
                            :placeholder="actionModal === 'approve' ? 'Optional notes for the instructor...' : 'Explain what needs to be corrected...'"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none transition"
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-1">
                        <button
                            @click="actionModal === 'approve' ? confirmApprove() : confirmReject()"
                            :disabled="actionModal === 'reject' && !modalNotes.trim()"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="actionModal === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                        >
                            <ArrowPathIcon v-if="approveForm.processing || rejectForm.processing" class="w-4 h-4 animate-spin" />
                            {{ actionModal === 'approve' ? 'Approve & Lock Grades' : 'Reject Submission' }}
                        </button>
                        <button @click="closeModal" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-xl transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>
