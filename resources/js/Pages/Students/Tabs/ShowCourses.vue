<script setup>
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import Select from "primevue/select";
import Listbox from "primevue/listbox";
import { defineProps, ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { PlusCircleIcon, MinusCircleIcon } from "@heroicons/vue/24/solid";
import {
    BookmarkIcon,
    CheckCircleIcon,
    ClockIcon,
    XCircleIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    student: { type: Object, required: true },
    courses: { type: Array, required: true },
    studyModes: { type: Array, required: false, default: () => [] },
    activeSemester: { type: Object, required: true },
});

const unAddableCourses = computed(() =>
    props.student.enrollments
        .filter(
            (enrollment) =>
                enrollment.status == "enrolled" ||
                enrollment.status == "completed"
        )
        .map((e) => e.course.id)
);

const filteredCoursesList = computed(() =>
    props.courses.filter(
        (course) => !unAddableCourses.value.includes(course.id)
    )
);

const showAddModal = ref(false);
const showDropModal = ref(false);

const addForm = useForm({ course_id: "", section_id: "" });
const dropForm = useForm({});

const enrolledIds = computed(
    () =>
        new Set(
            props.student.enrollments
                .filter((e) => e.status == "enrolled")
                .map((e) => e.course.id)
        )
);
const availableCourses = computed(() =>
    props.courses
        .filter((c) => !enrolledIds.value.has(c.id))
        .map((c) => ({ ...c, label: c.name }))
);

const selectedStudyModeId = ref(props.studyModes[0].id || null);

const sectionOptions = computed(() => {
    const selected = props.studyModes.find(
        (sm) => sm.id === selectedStudyModeId.value
    );

    return (
        selected?.sections?.filter((section) => {
            const courses = Array.isArray(section.courses)
                ? section.courses
                : [];
            return courses
                .map((course) => course.id)
                .includes(addForm.course_id);
        }) || []
    );
});

const resetAdd = () => {
    addForm.reset();
    addForm.clearErrors();
};
const resetDrop = () => {
    dropForm.reset();
    dropForm.clearErrors();
};

const submitAdd = () => {
    addForm.post(
        route("enrollments-student.add", { student: props.student.id }),
        {
            onSuccess: () => {
                Swal.fire("Added!", "Course added successfully.", "success");
                showAddModal.value = false;
                resetAdd();
            },
        }
    );
};

const addEnrollmentBack = (enrollment) => {
    addForm.course_id = enrollment.course.id;
    showAddModal.value = true;
};

const dropEnrollment = (enrollmentId) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This action will drop the course from the student!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, drop it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                route("enrollments-student.drop", {
                    student: props.student.id,
                }),
                { enrollment_id: enrollmentId },
                {
                    onSuccess: () => {
                        Swal.fire(
                            "Dropped!",
                            "Course dropped successfully.",
                            "success"
                        );
                    },
                }
            );
        }
    });
};
</script>

<template>
    <div v-if="userCanAny(['create-students'])" class="space-y-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700/60">
            <div>
                <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">
                    Course Registrations
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Manage student course enrollment, active schedules, and status records.
                </p>
            </div>
            <button
                @click="showAddModal = true"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition active:scale-95"
            >
                <PlusCircleIcon class="w-4 h-4" /> Add Course
            </button>
        </div>

        <!-- ── Pending Courses ─────────────────────────────────────── -->
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 flex items-center gap-3 border-b border-gray-50 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                    <ClockIcon class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                        Pending Courses
                    </h3>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">
                        Waiting for payment verification
                    </p>
                </div>
            </div>

            <div v-if="student.enrollments.filter(e => e.status === 'pending').length === 0" class="p-8 text-center">
                <p class="text-sm text-gray-400 italic">No pending courses for this semester.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider w-16">No.</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Course Name</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Credits</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Grade</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-750">
                        <tr
                            v-for="(enrollment, index) in student.enrollments.filter(e => e.status === 'pending')"
                            :key="enrollment.id"
                            class="hover:bg-gray-50/50 dark:hover:bg-gray-700/10 transition"
                        >
                            <td class="px-6 py-4 text-xs font-bold text-gray-400">{{ index + 1 }}</td>
                            <td class="px-6 py-4">
                                <Link
                                    :href="route('courses.show', { course: enrollment.course.id })"
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400"
                                >
                                    {{ enrollment.course.name }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400">{{ enrollment.course.code }}</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-900 dark:text-gray-100">{{ enrollment.course.credit_hours }} cr</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-500">
                                {{ student.grades?.find(g => g.course_id === enrollment.course.id)?.grade_letter ?? "—" }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                    PENDING
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="dropEnrollment(enrollment.id)"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-400 hover:text-rose-800 transition"
                                >
                                    <MinusCircleIcon class="w-4 h-4" /> Drop
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Enrolled Courses ────────────────────────────────────── -->
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 flex items-center gap-3 border-b border-gray-50 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:emerald-400 shrink-0">
                    <BookmarkIcon class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                        Enrolled Courses
                    </h3>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">
                        Active study schedules
                    </p>
                </div>
            </div>

            <div v-if="student.enrollments.filter(e => e.status === 'enrolled').length === 0" class="p-8 text-center">
                <p class="text-sm text-gray-400 italic">No enrolled courses for this semester.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider w-16">No.</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Course Name</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Credits</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Grade</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-750">
                        <tr
                            v-for="(enrollment, index) in student.enrollments.filter(e => e.status === 'enrolled')"
                            :key="enrollment.id"
                            class="hover:bg-gray-50/50 dark:hover:bg-gray-700/10 transition"
                        >
                            <td class="px-6 py-4 text-xs font-bold text-gray-400">{{ index + 1 }}</td>
                            <td class="px-6 py-4">
                                <Link
                                    :href="route('courses.show', { course: enrollment.course.id })"
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400"
                                >
                                    {{ enrollment.course.name }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400">{{ enrollment.course.code }}</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-900 dark:text-gray-100">{{ enrollment.course.credit_hours }} cr</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-500">
                                {{ student.grades?.find(g => g.course_id === enrollment.course.id)?.grade_letter ?? "—" }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    ENROLLED
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="dropEnrollment(enrollment.id)"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 dark:text-rose-400 hover:text-rose-800 transition"
                                >
                                    <MinusCircleIcon class="w-4 h-4" /> Drop
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Dropped Courses ─────────────────────────────────────── -->
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 flex items-center gap-3 border-b border-gray-50 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
                    <XCircleIcon class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                        Dropped Courses
                    </h3>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">
                        Cancelled or suspended enrollments
                    </p>
                </div>
            </div>

            <div v-if="student.enrollments.filter(e => e.status === 'dropped').length === 0" class="p-8 text-center">
                <p class="text-sm text-gray-400 italic">No dropped courses recorded.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider w-16">No.</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Course Name</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Credits</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Grade</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-750">
                        <tr
                            v-for="(enrollment, index) in student.enrollments.filter(e => e.status === 'dropped')"
                            :key="enrollment.id"
                            class="hover:bg-gray-50/50 dark:hover:bg-gray-700/10 transition"
                        >
                            <td class="px-6 py-4 text-xs font-bold text-gray-400">{{ index + 1 }}</td>
                            <td class="px-6 py-4">
                                <Link
                                    :href="route('courses.show', { course: enrollment.course.id })"
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400"
                                >
                                    {{ enrollment.course.name }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400">{{ enrollment.course.code }}</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-900 dark:text-gray-100">{{ enrollment.course.credit_hours }} cr</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-500">
                                {{ student.grades?.find(g => g.course_id === enrollment.course.id)?.grade_letter ?? "—" }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400">
                                    DROPPED
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="addEnrollmentBack(enrollment)"
                                    class="inline-flex items-center gap-1 text-xs font-bold text-green-600 dark:text-green-400 hover:text-green-800 transition"
                                >
                                    <PlusCircleIcon class="w-4 h-4" /> Add Back
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Completed Courses ───────────────────────────────────── -->
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 flex items-center gap-3 border-b border-gray-50 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                    <CheckCircleIcon class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                        Completed Courses
                    </h3>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">
                        Courses fully taken and graded
                    </p>
                </div>
            </div>

            <div v-if="student.enrollments.filter(e => e.status === 'completed').length === 0" class="p-8 text-center">
                <p class="text-sm text-gray-400 italic">No completed courses recorded.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider w-16">No.</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Course Name</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Credits</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Grade</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-750">
                        <tr
                            v-for="(enrollment, index) in student.enrollments.filter(e => e.status === 'completed')"
                            :key="enrollment.id"
                            class="hover:bg-gray-50/50 dark:hover:bg-gray-700/10 transition"
                        >
                            <td class="px-6 py-4 text-xs font-bold text-gray-400">{{ index + 1 }}</td>
                            <td class="px-6 py-4">
                                <Link
                                    :href="route('courses.show', { course: enrollment.course.id })"
                                    class="text-sm font-bold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400"
                                >
                                    {{ enrollment.course.name }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400">{{ enrollment.course.code }}</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-900 dark:text-gray-100">{{ enrollment.course.credit_hours }} cr</td>
                            <td class="px-6 py-4 text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                {{ student.grades?.find(g => g.course_id === enrollment.course.id)?.grade_letter ?? "—" }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">
                                    COMPLETED
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div v-else class="p-8 text-center text-gray-500">Unavailable!</div>

    <!-- ── Add Course Modal ────────────────────────────────────── -->
    <Modal
        :show="showAddModal"
        @close="showAddModal = false"
        :max-width="'2xl'"
    >
        <div class="p-6 space-y-6 bg-gradient-to-br from-white via-indigo-50/20 to-indigo-100/10 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
            <div>
                <h3 class="text-lg font-extrabold text-gray-900 dark:text-white">
                    Add New Enrollment
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Select a course and allocate a study mode and class section for {{ student.firstName }} {{ student.lastName }}.
                </p>
            </div>

            <div class="space-y-4">
                <p v-if="addForm.errors.section_id" class="text-xs text-red-500">{{ addForm.errors.section_id }}</p>
                <p v-if="addForm.errors.course_id" class="text-xs text-red-500">{{ addForm.errors.course_id }}</p>
                
                <!-- Course Selection -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                        Course Selection
                    </label>
                    <Listbox
                        v-model="addForm.course_id"
                        :options="filteredCoursesList"
                        checkmark
                        optionLabel="name"
                        option-value="id"
                        filter
                        placeholder="Choose a Course..."
                        class="w-full border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden focus:ring focus:ring-indigo-500"
                    />
                </div>

                <!-- Study Mode Selection -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                        Study Mode
                    </label>
                    <Select
                        v-model="selectedStudyModeId"
                        :options="props.studyModes"
                        placeholder="Select Attendance Mode"
                        option-label="name"
                        append-to="self"
                        option-value="id"
                        class="w-full px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-150 transition"
                    />
                </div>

                <!-- Section Selection -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                        Class Section
                    </label>
                    <Select
                        v-model="addForm.section_id"
                        :options="sectionOptions"
                        :placeholder="`Available Sections (${sectionOptions.length})`"
                        empty-message="No available sections for selection"
                        option-label="name"
                        append-to="self"
                        option-value="id"
                        class="w-full px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-150 transition"
                    >
                        <template #option="slotProps">
                            <span class="text-sm font-semibold">{{ slotProps.option.name }}</span>
                            <span class="text-xs text-gray-400"> ({{ slotProps.option.program?.name }} - {{ slotProps.option.track?.name || 'No Track' }})</span>
                        </template>
                    </Select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button
                    @click="showAddModal = false"
                    class="px-4 py-2 text-xs font-bold bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl text-gray-700 dark:text-gray-200 transition"
                >
                    Cancel
                </button>
                <button
                    @click="submitAdd"
                    :disabled="addForm.processing"
                    class="inline-flex items-center px-4 py-2 text-xs font-bold bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-sm transition disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <svg v-if="addForm.processing" class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    Enroll Student
                </button>
            </div>
        </div>
    </Modal>
</template>
