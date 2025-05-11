<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
// Using the same icons as on the programs page for consistency.
import { PencilSquareIcon, EyeIcon, TrashIcon, ArrowPathIcon } from "@heroicons/vue/24/solid";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";

// Added the sortInfo prop to pass the sorting details from the controller.
defineProps({
  studyModes: {
    type: Object,
    required: true,
  },
  sortInfo: {
    type: Object,
    default: () => ({}),
  },
});

const refreshing = ref(false);
const search = ref(usePage().props.search || "");

const refreshData = () => {
  refreshing.value = true;
  router.flush("/studyModes", { method: "get" });
  router.visit(route("studyModes.index"), {
    only: ["studyModes"],
    onFinish: () => {
      refreshing.value = false;
    },
  });
};

const deleteStudyMode = (id) => {
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
      router.delete(route("studyModes.destroy", { studyMode: id }), {
        onSuccess: () => {
          Swal.fire("Deleted!", "The study mode has been deleted.", "success");
        },
      });
    }
  });
};

const searchStudyModes = () => {
  router.get(
    route("studyModes.index"),
    { ...route().params, search: search.value },
    { preserveState: true }
  );
};
</script>

<template>
  <AppLayout>
    <!-- Page Title -->
    <div class="my-6 text-center">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        Study Modes
      </h1>
    </div>

    <!-- Header Toolbar -->
    <div class="flex justify-between items-center mb-3">
      <!-- Search Bar wi  th Icon -->
      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
          <svg
            class="w-5 h-5 text-gray-500 dark:text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-4.35-4.35M9 17A8 8 0 109 1a8 8 0 000 16z"
            />
          </svg>
        </span>
        <input
          type="text"
          v-model="search"
          placeholder="Search Study Modes..."
          class="pl-10 p-2 border rounded-lg text-gray-900 dark:text-white dark:bg-gray-700"
          @input="searchStudyModes"
        />
      </div>

      <div class="flex space-x-2">
        <!-- Add New Study Mode Button -->
        <Link
          v-if="userCan('create-studyModes')"
          :href="route('studyModes.create')"
          class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-green-700 focus:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        >
          + Add Study Mode
        </Link>

        <!-- Refresh Button -->
        <button
          @click="refreshData"
          class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          title="Refresh Data"
        >
          <ArrowPathIcon
            class="w-5 h-5 mr-2"
            :class="{ 'animate-spin': refreshing }"
          />
          Refresh Data
        </button>
      </div>
    </div>

    <!-- Study Modes Table -->
    <Table>
      <TableHeader>
        <tr>
          <Thead :sortable="true" :sort-info="sortInfo" sortColumn="name">
            Name
          </Thead>
          <Thead scope="col" class="px-6 py-3">
            Actions
          </Thead>
        </tr>
      </TableHeader>
      <tbody>
        <TableZebraRows
          v-for="studyMode in studyModes.data"
          :key="studyMode.id"
        >
          <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
          >
            <Link :href="route('studyModes.show', { studyMode: studyMode.id })">
              {{ studyMode.name }}
            </Link>
          </th>
          <td class="px-6 py-4 flex justify-between">
            <Link
              v-if="userCan('view-studyModes')"
              :href="route('studyModes.show', { studyMode: studyMode.id })"
              class="text-blue-500 hover:text-blue-700"
            >
              <EyeIcon class="w-5 h-5" />
            </Link>
            <Link
              v-if="userCan('update-studyModes')"
              :href="route('studyModes.edit', { studyMode: studyMode.id })"
              class="text-green-500 hover:text-green-700"
            >
              <PencilSquareIcon class="w-5 h-5" />
            </Link>
            <!-- <button
              v-if="userCan('delete-studyModes')"
              @click="deleteStudyMode(studyMode.id)"
              class="text-red-500 hover:text-red-700"
            >
              <TrashIcon class="w-5 h-5" />
            </button> -->
          </td>
        </TableZebraRows>
      </tbody>
    </Table>

    <!-- Pagination Links -->
    <div class="mt-3 flex justify-center space-x-2">
      <Link
        v-for="link in studyModes.meta.links"
        :key="link.label"
        :href="link.url || '#'"
        class="p-2 px-4 text-sm font-medium rounded-lg transition-colors"
        :class="{
          'text-gray-700 dark:text-gray-400': true,
          'cursor-not-allowed opacity-50': !link.url,
          '!bg-gray-100 !dark:bg-gray-800': link.active,
        }"
        v-html="link.label"
      />
    </div>
  </AppLayout>
</template>
