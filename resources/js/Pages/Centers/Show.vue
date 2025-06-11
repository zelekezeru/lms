<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import Thead from "@/Components/Thead.vue";
import Modal from "@/Components/Modal.vue";
import ShowDetails from "./Tabs/ShowDetails.vue";
import ShowCoordinators from "./Tabs/ShowCoordinators.vue";
import ShowStudents from "./Tabs/ShowStudents.vue";
import ShowExcels from "./Tabs/ShowExcels.vue";
import {
  ArrowPathIcon,
  TrashIcon,
  EyeIcon,
  PencilSquareIcon,
  MagnifyingGlassIcon,
  Squares2X2Icon,
  TableCellsIcon,
  UserGroupIcon,
  UsersIcon,
  CogIcon,
  ClipboardDocumentListIcon,
} from "@heroicons/vue/24/outline";

defineProps({
    center: {
        type: Object,
        required: true,
    },  
    coordinator: {
        type: Object,
        required: true,
    },
    students: {
        type: Array,
        required: true,
    },
    sortInfo: {
        type: Object,
        required: false,
    },
});

const selectedCenter = ref(null);
const showStudentsModal = ref(false);

const openStudentsModal = (center) => {
    selectedCenter.value = center;
    showStudentsModal.value = true;
};
const { coordinators, search: serverSearch } = usePage().props;


// Multi nav header options
const selectedTab = ref('details');

const tabs = [
    { key: 'details', label: 'Details', icon: CogIcon },
    { key: 'coordinators', label: 'Coordinators', icon: UserGroupIcon },
    { key: 'students', label: 'Students', icon: UsersIcon },
    { key: "excels", label: "Excel Managment", icon: ClipboardDocumentListIcon },
    
];

const search = ref(serverSearch || "");
const refreshing = ref(false);
const viewMode = ref("card");

const refreshData = () => {
  refreshing.value = true;
  router.visit(route("coordinators.index"), {
    only: ["coordinators"],
    onFinish: () => {
      refreshing.value = false;
    },
  });
};

const searchCoordinators = () => {
  router.get(route("coordinators.index"), { search: search.value }, { preserveState: true });
};

</script>

<template>
  <AppLayout>
      <!-- Center Heading -->
      <div class="mb-6 text-center">
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
              {{ center.name }} Center
          </h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">
              {{ center.code }}
          </p>
        </div>
        
        <nav
            class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700"
        >
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="selectedTab = tab.key"
                :class="[
                    'flex items-center px-4 py-2 space-x-2 text-sm font-medium transition',
                    selectedTab === tab.key
                        ? 'border-b-2 border-indigo-500 text-indigo-600'
                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
                ]"
            >
                <component :is="tab.icon" class="w-5 h-5" />
                <span>{{ tab.label }}</span>
            </button>
        </nav>
        
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
                <!-- Details Panel -->
                <ShowDetails
                    v-if="selectedTab == 'details'"
                    :center="center"
                    :coordinator="coordinator?.user"
                />

                <!-- Coordinators Panel -->
                <ShowCoordinators v-else-if="selectedTab == 'coordinators'" 
                    :coordinator="coordinator?.user"
                    :center="center"
                />
                
                <!-- Students Panel -->              
                <ShowStudents
                    v-else-if="selectedTab == 'students'"
                    :center="center"
                    :students="students"
                    :coordinator="coordinator?.user"
                    :sort-info="sortInfo"
                    @close="showStudentsModal = false"
                />

                <!-- Excel Management Panel -->
                <ShowExcels
                    v-else-if="selectedTab == 'excels'"
                    :center="center"
                    :coordinator="coordinator?.user"
                />
            </div>
        </transition>
  </AppLayout>
</template>