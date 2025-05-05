<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { EyeIcon, TrashIcon, ArrowPathIcon } from "@heroicons/vue/24/solid";
import { Cog6ToothIcon, BookOpenIcon, AcademicCapIcon, UsersIcon, CurrencyDollarIcon, BuildingLibraryIcon, InformationCircleIcon, BanknotesIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";
import ShowDetails from "./Tabs/showDetails.vue";
import ShowPayments from "./Tabs/ShowPayments.vue";
import ShowCategories from "./Tabs/ShowCategories.vue";
import ShowMethods from "./Tabs/ShowMethods.vue";
import ShowTypes from "./Tabs/ShowTypes.vue";
import ShowStatus from "./Tabs/ShowStatus.vue";
import ShowTransactions from "./Tabs/ShowTransactions.vue";
import Modal from "@/Components/Modal.vue";

defineProps({
    payments: {
        type: Object,
        required: true,
    },
    paymentCategories: {
        type: Array,
        required: true,
    },
    paymentMethods: {
        type: Object,
        required: true,
    },
    paymentTypes: {
        type: Array,
        required: true,
    },
    studyModes: {
        type: Array,
        required: true,
    },
    status: {
        type: Object,
        required: true,
    },
    transactions: {
        type: Object,
        required: true,
    },
    sortInfo: {
        type: Object,
        required: false,
    },
    userCan: {
        type: Function,
        required: true,
    },
    showVerifyModal: Boolean,
});

const refreshing = ref(false);

const refreshData = () => {
    refreshing.value = true;
    router.flush("/payments", { method: "get" });

    router.visit(route("payments.index"), {
        only: ["payments"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

const search = ref(usePage().props.search || "");
// Search function
const searchpayments = () => {
    router.get(
        route("payments.index"),
        { ...route().params, search: search.value },
        { preserveState: true },
    );
};

// Multi nav header options
const selectedTab = ref('details');

const tabs = [
    { key: 'details', label: 'Details', icon: Cog6ToothIcon },
    { key: 'payments', label: 'Payments', icon: CurrencyDollarIcon },
    { key: 'categories', label: 'Categories', icon: BookOpenIcon },
    { key: 'methods', label: 'Methods', icon: Cog6ToothIcon },
    { key: 'types', label: 'Types', icon: BanknotesIcon },
    { key: 'status', label: 'Status', icon: InformationCircleIcon },
    { key: 'transactions', label: 'Transactions', icon: BuildingLibraryIcon },
];

const deletePayment = (id) => {
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
            router.delete(route("payments.destroy", { payment: id }), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "The payment has been deleted.", "success");
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>

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
                :payments="payments"
                :paymentCategories="paymentCategories"
                :paymentMethods="paymentMethods"
                />
            <ShowPayments
                v-if="selectedTab === 'payments'"
                :payments="payments"
            />
            <ShowCategories
                v-else-if="selectedTab === 'categories'"
                :paymentCategories="paymentCategories"
            />
            <ShowMethods
                v-else-if="selectedTab === 'methods'"
                :paymentMethods="paymentMethods"
            />
            <ShowTypes
                v-else-if="selectedTab === 'types'"
                :paymentTypes="paymentTypes"
                :studyModes="studyModes"
                :paymentCategories="paymentCategories"
            />
            <ShowStatus
                v-else-if="selectedTab === 'status'"
                :payments="payments"
            />
            <ShowTransactions
                v-else-if="selectedTab === 'transactions'"
                :payments="payments"
            />
        </div>
            
        </transition>


    </AppLayout>
</template>
