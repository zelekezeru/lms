<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { defineProps, onMounted, ref, watch } from "vue";
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
    activeSemester: Object,
    showVerifyModal: Boolean,
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
        label: "Documents",
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
</script>

<template>
    <Head title="Student Details" />
    <AppLayout>
        <div class="max-w-8xl mx-auto p-6 relative">
            <!-- 🔘 Drawer Toggle Button (mobile only) -->
            <div class="flex justify-end mb-4 md:hidden">
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

            <!-- 💻 Desktop Tab Nav (hidden on mobile) -->
            <nav
                ref="tabNav"
                class="hidden md:flex overflow-x-auto pb-2 mb-6 border-b border-gray-200 dark:border-gray-700"
                :class="isOverflowing ? 'justify-start' : 'justify-center'"
            >
                <div class="flex space-x-4 min-w-max">
                    <template v-for="tab in tabs" :key="tab.key">
                        <button
                            v-if="userCan(tab.permission)"
                            @click="selectedTab = tab.key"
                            type="button"
                            class="flex-shrink-0 flex items-center px-4 py-2 space-x-2 text-sm font-medium transition whitespace-nowrap"
                            :class="
                                selectedTab === tab.key
                                    ? 'border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400'
                                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200'
                            "
                        >
                            <component :is="tab.icon" class="w-5 h-5" />
                            <span>{{ tab.label }}</span>
                        </button>
                    </template>
                </div>
            </nav>

            <!-- 📝 Page Title -->
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ student.firstName }} {{ student.middleName }}
                {{ student.lastName }}
            </h1>
            
            <!-- 🌐 Main Content -->
            <transition
                mode="out-in"
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 scale-75"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-75"
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
