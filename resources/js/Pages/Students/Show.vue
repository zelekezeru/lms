<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, MultiSelect } from "primevue";
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
    student: {
        type: Object,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
    documents: {
        type: Array,
        required: true,
    },
    status: {
        type: Object,
        required: true,
    },
    studyModes: {
        type: Array,
        required: true,
    },
    sections: {
        type: Array,
        required: true,
    },
    courses: {
        type: Array,
        required: true,
    },
    payments: {
        type: Array,
        required: true,
    },
    paymentMethods: {
        type: Array,
        required: true,
    },
    paymentCategories: {
        type: Array,
        required: true,
    },
    paymentStatuses: {
        type: Array,
        required: true,
    },
    semesters: {
        type: Object,
        required: true,
    },
    activeSemester: {
        type: Object,
        required: true,
    },
    showVerifyModal: Boolean,
});

// Multi nav header options
const selectedTab = ref("details");

const tabs = [
    { key: "details", label: "Details", icon: CogIcon },
    { key: "academics", label: "Academics", icon: BookOpenIcon },
    { key: "courses", label: "Courses", icon: AcademicCapIcon },
    { key: "registrations", label: "Registration", icon: UsersIcon },
    { key: "payments", label: "Payment", icon: CurrencyDollarIcon },
    { key: "church", label: "Church", icon: HomeModernIcon },
    { key: "documents", label: "Documents", icon: PaperClipIcon },
];

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

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
        <div class="max-w-8xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ student.firstName }} {{ student.middleName }}
                {{ student.lastName }}
            </h1>

            <nav
                class="flex justify-center space-x-4 overflow-x-auto pb-2 mb-6 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    class="flex-shrink-0 flex items-center px-4 py-2 space-x-2 text-sm font-medium transition whitespace-nowrap"
                    :class="
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200'
                    "
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            <!-- Details Panel -->
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
                >
                    <ShowDetails
                        v-if="selectedTab === 'details'"
                        :student="student"
                    />
                    <!-- Academics Panel -->
                    <ShowAcademics
                        v-else-if="selectedTab === 'academics'"
                        :student="student"
                        :sections="sections"
                        :semesters="semesters"
                        :activeSemester="activeSemester"
                    />

                    <!-- Courses Panel -->
                    <ShowCourses
                        v-else-if="selectedTab === 'courses'"
                        :student="student"
                        :courses="courses"
                        :studyModes="studyModes"
                    />

                    <!-- Registrations Panel -->
                    <ShowRegistrations
                        v-else-if="selectedTab === 'registrations'"
                        :student="student"
                        :status="status"
                        :showVerifyModal="showVerifyModal"
                    />

                    <!-- Payments Panel -->
                    <ShowPayments
                        v-else-if="selectedTab === 'payments'"
                        :student="student"
                        :status="status"
                        :payments="payments"
                        ,
                        :paymentMethods="paymentMethods"
                        :paymentCategories="paymentCategories"
                        :paymentStatuses="paymentStatuses"
                        :showVerifyModal="showVerifyModal"
                    />

                    <!-- Church Panel -->
                    <ShowChurches
                        v-else-if="selectedTab === 'church'"
                        :church="student.church"
                    />

                    <!-- Documents Pannel -->
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
