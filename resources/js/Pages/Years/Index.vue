<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
  ArrowPathIcon,
  TrashIcon,
  EyeIcon,
  PencilSquareIcon,
  MagnifyingGlassIcon,
  Squares2X2Icon,
  TableCellsIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import Thead from "@/Components/Thead.vue";

// Extract props
const { years, search: serverSearch } = usePage().props;

const search = ref(serverSearch || "");
const refreshing = ref(false);
const viewMode = ref("card"); // 'table' or 'card'

// Refresh data
const refreshData = () => {
  refreshing.value = true;
  router.visit(route("years.index"), {
    only: ["years"],
    onFinish: () => {
      refreshing.value = false;
    },
  });
};

// Delete year
const deleteYear = (id) => {
  Swal.fire({
    title: "Are you sure?",
    text: "This action cannot be undone!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route("years.destroy", { year: id }), {
        onSuccess: () => {
          Swal.fire("Deleted!", "Year has been removed.", "success");
        },
      });
    }
  });
};

// Search years
const searchYears = () => {
  router.get(route("years.index"), { search: search.value }, { preserveState: true });
};
</script>

<template>
  <AppLayout>
    <div class="my-6 text-center">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Years</h1>
    </div>

    <!-- Toolbar -->
    <div class="flex justify-between items-center mb-3 flex-wrap gap-3">
      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
          <MagnifyingGlassIcon class="w-5 h-5 text-gray-500 dark:text-gray-400" />
        </span>
        <input
          type="text"
          v-model="search"
          placeholder="Search..."
          class="pl-10 p-2 border rounded-lg text-gray-900 dark:text-white dark:bg-gray-700"
          @input="searchYears"
        />
      </div>

      <div class="flex space-x-4">
        <Link
          :href="route('years.create')"
          class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-green-700"
        >
          + Add Year
        </Link>

        <button
          @click="refreshData"
          class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-blue-700"
          title="Refresh Data"
        >
          <ArrowPathIcon class="w-5 h-5 mr-2" :class="{ 'animate-spin': refreshing }" />
          Refresh
        </button>

        <button
          @click="viewMode = viewMode === 'table' ? 'card' : 'table'"
          class="inline-flex items-center rounded-md bg-gray-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-gray-700"
          title="Toggle View"
        >
          <component :is="viewMode === 'table' ? Squares2X2Icon : TableCellsIcon" class="w-5 h-5" />
          
        </button>
      </div>
    </div>

    <!-- Table View -->
    <div v-if="years.data.length > 0 && viewMode === 'table'" class="overflow-x-auto shadow-md sm:rounded-lg">
      <Table>
        <TableHeader>
          <tr>
            <Thead>Year</Thead>
            <Thead>Status</Thead>
            <Thead>Action</Thead>
          </tr>
        </TableHeader>
        <tbody>
          <tr
            v-for="year in years.data"
            :key="year.id"
            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700"
          >
            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
              <Link :href="route('years.show', { year: year.id })" class="text-blue-500 hover:text-blue-700">
                {{ year.name }}
              </Link>
            </td>
            <td class="px-6 py-4">{{ year.status }}</td>
            <td class="flex items-center gap-3 px-6 py-4">
              <Link :href="route('years.show', { year: year.id })" class="text-blue-500 hover:text-blue-700">
                <EyeIcon class="w-5 h-5" />
              </Link>
              <Link :href="route('years.edit', { year: year.id })" class="text-green-500 hover:text-green-700">
                <PencilSquareIcon class="w-5 h-5" />
              </Link>
              <button @click="deleteYear(year.id)" class="text-red-500 hover:text-red-700">
                <TrashIcon class="w-5 h-5" />
              </button>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>

    <!-- Card View -->
    <div v-else-if="years.data.length > 0 && viewMode === 'card'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
      <div
        v-for="year in years.data"
        :key="year.id"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-5 border dark:border-gray-700"
      >
        <div class="flex justify-between items-center mb-2">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ year.name }}</h2>
          <span class="text-sm px-2 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
            {{ year.status }}
          </span>
        </div>
        <div class="flex justify-start items-center space-x-4 mt-3">
          <Link :href="route('years.show', { year: year.id })" class="text-blue-500 hover:text-blue-700">
            <EyeIcon class="w-5 h-5" />
          </Link>
          <Link :href="route('years.edit', { year: year.id })" class="text-green-500 hover:text-green-700">
            <PencilSquareIcon class="w-5 h-5" />
          </Link>
          <button @click="deleteYear(year.id)" class="text-red-500 hover:text-red-700">
            <TrashIcon class="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>

    <!-- No data -->
    <div v-else class="text-center text-gray-500 dark:text-gray-400 py-6">
      <p class="text-lg font-medium text-gray-700 dark:text-gray-300">No years found.</p>
    </div>
  </AppLayout>
</template>
