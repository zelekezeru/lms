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
} from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import Thead from "@/Components/Thead.vue";

defineProps({
  centers: Object,
});

const { centers, search: serverSearch } = usePage().props;
const search = ref(serverSearch || "");
const refreshing = ref(false);
const viewMode = ref("table");

// Refresh data
const refreshData = () => {
  refreshing.value = true;
  router.visit(route("centers.index"), {
    only: ["centers"],
    onFinish: () => (refreshing.value = false),
  });
};

// Delete center
const deleteCenter = (id) => {
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
      router.delete(route("centers.destroy", { center: id }), {
        onSuccess: () => {
          Swal.fire("Deleted!", "Center has been removed.", "success");
        },
      });
    }
  });
};

// Search centers
const searchCenters = () => {
  router.get(route("centers.index"), { search: search.value }, { preserveState: true });
};
</script>

<template>
  <AppLayout>
    <!-- Page Title -->
    <div class="my-6 text-center">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ $t("centers.title") }}
      </h1>
    </div>

    <!-- Toolbar -->
    <div class="flex justify-between items-center mb-3 flex-wrap gap-3">
      <!-- Search Input -->
      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
          <MagnifyingGlassIcon class="w-5 h-5 text-gray-500 dark:text-gray-400" />
        </span>
        <input
          type="text"
          v-model="search"
          :placeholder="$t('centers.search')"
          class="pl-10 p-2 border rounded-lg text-gray-900 dark:text-white dark:bg-gray-700"
          @input="searchCenters"
        />
      </div>

      <!-- Buttons -->
      <div class="flex space-x-4">
        <Link
          :href="route('centers.create')"
          class="inline-flex items-center rounded-md bg-green-600 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-green-700"
        >
          + {{ $t('centers.add') }}
        </Link>

        <button
          @click="refreshData"
          class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-blue-700"
        >
          <ArrowPathIcon class="w-5 h-5 mr-2" :class="{ 'animate-spin': refreshing }" />
          {{ $t('common.refresh') }}
        </button>
      </div>
    </div>

    <!-- Center Table -->
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
      <Table>
        <TableHeader>
          <tr>
            <Thead>{{ $t("centers.no") }}</Thead>
            <Thead>{{ $t("centers.name") }}</Thead>
            <Thead>{{ $t("centers.code") }}</Thead>
            <Thead>{{ $t("centers.address") }}</Thead>
            <Thead>{{ $t("centers.coordinator") }}</Thead>
            <Thead>{{ $t("centers.num_students") }}</Thead>
            <Thead>{{ $t("centers.status") }}</Thead>
            <Thead>{{ $t("centers.action") }}</Thead>
          </tr>
        </TableHeader>
        <tbody>
            <tr
                v-for="(center, index) in centers.data"
                :key="center.id"
                class="even:bg-gray-50 border-b dark:bg-gray-800 dark:border-gray-700"
            >
                <!-- No -->                
                <td class="px-6 py-4">
                    {{ index + 1 + ((centers.meta.current_page - 1) * centers.meta.per_page) }}
                </td>
                <!-- Center Name -->
                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                <Link
                    v-if="center.id"
                    :href="route('centers.show', { center: center.id })"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                >
                    {{ center.name }}
                </Link>
                </td>

                <!-- Code -->
                <td class="px-6 py-4 text-gray-800 dark:text-gray-100">
                {{ center.code }}
                </td>

                <!-- Address -->
                <td class="px-6 py-4 text-gray-800 dark:text-gray-100">
                {{ center.address }}
                </td>

                <!-- Coordinator -->
                <td class="px-6 py-4 text-gray-800 dark:text-gray-100">
                {{ center.coordinator ? center.coordinator.name : $t("centers.noCoordinator") }}
                </td>

                <!-- Student Count -->
                <td class="px-6 py-4 text-gray-800 dark:text-gray-100">
                {{ center.students_count }}
                </td>

                <!-- Status -->
                <td class="px-6 py-4">
                <span
                    class="text-sm px-2 py-1 rounded font-semibold"
                    :class="center.status === 'Active'
                    ? 'bg-green-100 text-green-800 dark:bg-green-600 dark:text-white'
                    : 'bg-gray-300 text-gray-800 dark:bg-gray-600 dark:text-white'"
                >
                    {{ center.status === 'Active' ? $t("status.active") : $t("status.inactive") }}
                </span>
            </td>

            <td class="px-6 py-4 flex items-center gap-3">
              <Link
                v-if="center.id"
                :href="route('centers.show', { center: center.id })"
                class="text-blue-500 hover:text-blue-700"
              >
                <EyeIcon class="w-5 h-5" />
              </Link>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
  </AppLayout>
</template>
