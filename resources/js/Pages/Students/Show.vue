<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { defineProps, onMounted, ref, watch, computed } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    PaperClipIcon,
    PlusCircleIcon,
    BookOpenIcon,
    HomeModernIcon,
    CurrencyDollarIcon,
} from "@heroicons/vue/24/solid";
import ShowDetails from "./Tabs/ShowDetails.vue";
import ShowCourses from "./Tabs/ShowCourses.vue";
import ShowAcademics from "./Tabs/ShowAcademics.vue";
import ShowRegistrations from "./Tabs/ShowRegistrations.vue";
import ShowPayments from "./Tabs/ShowPayments.vue";
import ShowChurches from "./Tabs/ShowChurches.vue";
import ShowDocuments from "./Tabs/ShowDocuments.vue";

const props = defineProps({
    student: Object,
    user: Object,
    documents: Array,
    status: Object,
    studyModes: Array,
    sections: Array,
    courses: Array,
    payments: Array,
    paymentMethods: Array,
    paymentTypes: Array,
    semesters: Object,
    grades: Object,
    deletedGrades: Object,
    activeSemester: Object,
    showVerifyModal: Boolean,
    availableSemesters: Array,
});

const selectedTab = ref("details");
const isDrawerOpen = ref(false);
const toggleDrawer = () => {
    isDrawerOpen.value = !isDrawerOpen.value;
};

const tabs = [
    {
        key: "details",
        label: "Details",
        icon: CogIcon,
        permission: "view-students",
    },
    {
        key: "academics",
        label: "Academics",
        icon: BookOpenIcon,
        permission: "view-students",
    },
    {
        key: "courses",
        label: "Courses",
        icon: AcademicCapIcon,
        permission: "create-students",
    },
    {
        key: "registrations",
        label: "Registration",
        icon: UsersIcon,
        permission: "create-students",
    },
    {
        key: "payments",
        label: "Payment",
        icon: CurrencyDollarIcon,
        permission: "view-payments",
    },
    {
        key: "church",
        label: "Church",
        icon: HomeModernIcon,
        permission: "view-students",
    },
    {
        key: "documents",
        label: "Transcript & Documents",
        icon: PaperClipIcon,
        permission: "view-students",
    },
];

const imageLoaded = ref(false);
const handleImageLoad = () => {
    imageLoaded.value = true;
};

const tabNav = ref(null); // Ref for nav element
const isOverflowing = ref(false); // State for overflow detection

// Function to check if tabs are overflowing
const checkOverflow = () => {
    if (!tabNav.value) return;
    const nav = tabNav.value;
    isOverflowing.value = nav.scrollWidth > nav.clientWidth; // Compare widths
};

// Watch for changes to tabs
watch(() => tabs, checkOverflow, { immediate: true });

// Run checkOverflow when component is mounted
onMounted(() => {
    checkOverflow();
    // Recheck on window resize
    window.addEventListener("resize", checkOverflow);
});

const deleteStudent = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("students.destroy", { student: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The student has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};

// Helper grade point function
const getGradePoint = (point) => {
    const p = parseFloat(point);
    if (isNaN(p)) return 0;
    if (p >= 94) return 4.0;
    if (p >= 90) return 3.7;
    if (p >= 87) return 3.3;
    if (p >= 84) return 3.0;
    if (p >= 80) return 2.7;
    if (p >= 77) return 2.3;
    if (p >= 74) return 2.0;
    if (p >= 70) return 1.7;
    if (p >= 67) return 1.3;
    if (p >= 64) return 1.0;
    if (p >= 60) return 0.7;
    return 0.0;
};

// Computed statistics
const cumulativeGPA = computed(() => {
    let totalPoints = 0;
    let totalCredits = 0;
    const studentGrades = props.student.grades || props.grades || [];
    studentGrades.forEach((g) => {
        const gp = getGradePoint(parseFloat(g.grade_point) || 0);
        const credit = parseFloat(g.course?.credit_hours || 0);
        totalPoints += gp * credit;
        totalCredits += credit;
    });
    return totalCredits > 0 ? (totalPoints / totalCredits).toFixed(2) : "0.00";
});

const completedCredits = computed(() => {
    return (props.student.enrollments || [])
        .filter(e => e.status === 'completed')
        .reduce((acc, e) => acc + (parseFloat(e.course?.credit_hours) || 0), 0);
});

const pendingPaymentsCount = computed(() => {
    return (props.payments || []).filter(p => p.status === 'pending').length;
});
</script>

<template>
    <Head :title="`${student.firstName} ${student.lastName} - Student Details`" />
    <AppLayout>
        <div class="max-w-7xl mx-auto p-4 md:p-6 space-y-6 relative">
            
            <!-- ── Profile Header Card ─────────────────────────── -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Cover Banner Gradient -->
                <div class="h-32 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 relative">
                    <!-- Subtle abstract overlays -->
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10 blur-xl"></div>
                </div>

                <!-- Profile Info Row -->
                <div class="px-6 pb-6 relative">
                    <!-- Overlapping Profile Image -->
                    <div class="-mt-16 mb-4 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div class="flex flex-col sm:flex-row items-center sm:items-end gap-4 text-center sm:text-left">
                            <div class="relative group">
                                <img
                                    v-if="student.profileImg"
                                    :src="student.profileImg"
                                    class="w-28 h-28 rounded-2xl object-cover border-4 border-white dark:border-gray-800 shadow-lg bg-gray-100 dark:bg-gray-700 transform transition duration-300 hover:scale-105"
                                    :alt="student.firstName"
                                />
                                <div
                                    v-else
                                    class="w-28 h-28 rounded-2xl border-4 border-white dark:border-gray-800 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg"
                                >
                                    <span class="text-white font-bold text-3xl">
                                        {{ (student.firstName?.[0] || "") + (student.lastName?.[0] || "") }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                    <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">
                                        {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
                                    </h1>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        Active
                                    </span>
                                </div>
                                <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                    Student ID: {{ student.idNo }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ student.program?.name }} • {{ student.track?.name || 'No Track' }}
                                </p>
                            </div>
                        </div>

                        <!-- Header Quick Action buttons -->
                        <div class="flex gap-2 self-center sm:self-end">
                            <Link
                                v-if="userCan('update-students')"
                                :href="route('students.edit', { student: student.id })"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-sm"
                            >
                                <PencilIcon class="w-3.5 h-3.5 mr-1" />
                                Edit Student
                            </Link>
                            <button
                                v-if="userCan('delete-students')"
                                @click="deleteStudent(student.id)"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/40 transition shadow-sm"
                            >
                                <TrashIcon class="w-3.5 h-3.5 mr-1" />
                                Delete
                            </button>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-100 dark:border-gray-700/60 my-5"></div>

                    <!-- Quick Stats Grid -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                                <AcademicCapIcon class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Cum. GPA</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ cumulativeGPA }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                                <BookOpenIcon class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Completed Credits</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ completedCredits }} cr</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center text-sky-600 dark:text-sky-400 shrink-0">
                                <UsersIcon class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Academic Period</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white leading-snug">
                                    {{ student.year?.name || 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 rounded-2xl p-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
                                <CurrencyDollarIcon class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">Pending Payments</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ pendingPaymentsCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🔘 Drawer Toggle Button (mobile only) -->
            <div class="flex justify-end md:hidden mb-4">
                <button
                    @click="toggleDrawer"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                >
                    <CogIcon class="w-5 h-5 mr-2" />
                    Tabs
                </button>
            </div>

            <!--  Mobile Drawer -->
            <div
                class="fixed top-0 right-0 h-full w-72 bg-white dark:bg-gray-900 shadow-lg z-50 transform transition-transform duration-300 md:hidden"
                :class="{
                    'translate-x-0': isDrawerOpen,
                    'translate-x-full': !isDrawerOpen,
                }"
            >
                <div
                    class="p-4 border-b dark:border-gray-700 flex justify-between items-center"
                >
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Navigation
                    </h2>
                    <button
                        @click="toggleDrawer"
                        class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                    >
                        ✕
                    </button>
                </div>

                <nav class="flex flex-col px-4 py-2 space-y-2">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="
                            () => {
                                selectedTab = tab.key;
                                toggleDrawer();
                            }
                        "
                        class="flex items-center space-x-2 px-3 py-2 rounded-lg transition"
                        :class="
                            selectedTab === tab.key
                                ? 'bg-indigo-100 dark:bg-indigo-800 text-indigo-700 dark:text-white'
                                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'
                        "
                    >
                        <component :is="tab.icon" class="w-5 h-5" />
                        <span>{{ tab.label }}</span>
                    </button>
                </nav>
            </div>

            <!-- 🟤 Overlay (mobile only) -->
            <div
                v-if="isDrawerOpen"
                @click="toggleDrawer"
                class="fixed inset-0 bg-black bg-opacity-25 z-40 md:hidden"
            ></div>

            <!-- 💻 Desktop Tab Nav (segmented control) -->
            <nav
                ref="tabNav"
                class="hidden md:flex overflow-x-auto p-1.5 mb-6 bg-gray-100/85 dark:bg-gray-900/50 backdrop-blur-md rounded-2xl border border-gray-200/50 dark:border-gray-800/50"
                :class="isOverflowing ? 'justify-start' : 'justify-center'"
            >
                <div class="flex space-x-1 min-w-max">
                    <template v-for="tab in tabs" :key="tab.key">
                        <button
                            v-if="userCan(tab.permission)"
                            @click="selectedTab = tab.key"
                            type="button"
                            class="flex-shrink-0 flex items-center px-4 py-2.5 space-x-2 text-xs font-bold rounded-xl transition-all duration-300 whitespace-nowrap"
                            :class="
                                selectedTab === tab.key
                                    ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-sm border border-gray-200/25 dark:border-gray-700/50'
                                    : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200'
                            "
                        >
                            <component :is="tab.icon" class="w-4 h-4 shrink-0 transition-transform duration-300" :class="selectedTab === tab.key ? 'scale-110 text-indigo-500' : ''" />
                            <span>{{ tab.label }}</span>
                        </button>
                    </template>
                </div>
            </nav>
            
            <!-- 🌐 Main Content -->
            <transition
                mode="out-in"
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-1"
            >
                <div
                    :key="selectedTab"
                    class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
                    :class="{
                        'max-w-5xl mx-auto': selectedTab == 'payments',
                    }"
                >
                    <ShowDetails
                        v-if="selectedTab === 'details'"
                        :student="student"
                    />

                    <ShowAcademics
                        v-else-if="selectedTab === 'academics'"
                        :student="student"
                        :sections="sections"
                        :semesters="semesters"
                        :grades="grades"
                        :deletedGrades="deletedGrades"
                        :activeSemester="activeSemester"
                    />

                    <ShowCourses
                        v-else-if="selectedTab === 'courses'"
                        :activeSemester="activeSemester"
                        :student="student"
                        :courses="courses"
                        :studyModes="studyModes"
                    />

                    <ShowRegistrations
                        v-else-if="selectedTab === 'registrations'"
                        :student="student"
                        :status="status"
                        :semesters="semesters"
                        :activeSemester="activeSemester"
                        :availableSemesters="availableSemesters"
                    />

                    <ShowPayments
                        v-else-if="selectedTab === 'payments'"
                        :student="student"
                        :semesters="semesters"
                        :payments="payments"
                        :active-semester="activeSemester"
                        :paymentMethods="paymentMethods"
                        :paymentTypes="paymentTypes"
                    />

                    <ShowChurches
                        v-else-if="selectedTab === 'church'"
                        :church="student.church"
                    />

                    <ShowDocuments
                        v-else-if="selectedTab === 'documents'"
                        :student="student"
                        :documents="documents"
                        :semesters="semesters"
                    />
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
