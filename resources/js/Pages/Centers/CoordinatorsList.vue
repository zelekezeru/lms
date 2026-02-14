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
  PlusCircleIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import Thead from "@/Components/Thead.vue";

defineProps({
  coordinators: Object,
});

const { coordinators, search: serverSearch } = usePage().props;
const search = ref(serverSearch || "");
const refreshing = ref(false);

// Refresh data
const refreshData = () => {
  refreshing.value = true;
  router.visit(route("coordinators.index"), {
    only: ["coordinators"],
    onFinish: () => (refreshing.value = false),
  });
};

// Delete coordinator
const deleteCoordinator = (id) => {
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
      router.delete(route("coordinators.destroy", { coordinator: id }), {
        onSuccess: () => {
          Swal.fire("Deleted!", "Coordinator has been removed.", "success");
        },
      });
    }
  });
};

// Search coordinators
const searchCoordinators = () => {
  router.get(route("coordinators.index"), { search: search.value }, { preserveState: true });
};
</script>

<template>
  <AppLayout>
    <!-- Page Title -->
    <div class="my-6 text-center">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ $t("coordinators.title") || "Coordinators" }}
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
          :placeholder="$t('coordinators.search') || 'Search coordinators...'"
          class="pl-10 p-2 border rounded-lg text-gray-900 dark:text-white dark:bg-gray-700"
          @input="searchCoordinators"
        />
      </div>

      <!-- Buttons -->
      <div class="flex space-x-4">
        
        <button
          @click="refreshData"
          class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition hover:bg-blue-700"
        >
          <ArrowPathIcon class="w-5 h-5 mr-2" :class="{ 'animate-spin': refreshing }" />
          {{ $t('common.refresh') || 'Refresh' }}
        </button>
      </div>
    </div>

    <!-- Coordinators Table -->
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
      <Table>
        <TableHeader>
          <tr>
            <Thead>{{ $t("coordinators.no") || "No" }}</Thead>
            <Thead>{{ $t("coordinators.name") || "Name" }}</Thead>
            <Thead>{{ $t("coordinators.email") || "Email" }}</Thead>
            <Thead>{{ $t("coordinators.phone") || "Phone" }}</Thead>
            <Thead>{{ $t("coordinators.status") || "Status" }}</Thead>
            <Thead>{{ $t("coordinators.action") || "Actions" }}</Thead>
          </tr>
        </TableHeader>
        <tbody>
          <tr
            v-for="(coordinator, index) in coordinators.data"
            :key="coordinator.id"
            class="even:bg-gray-50 border-b dark:bg-gray-800 dark:border-gray-700"
          >
            <!-- No -->
            <td class="px-6 py-4">
              {{ index + 1 + ((coordinators.meta.current_page - 1) * coordinators.meta.per_page) }}
            </td>
            <!-- Name -->
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap flex items-center gap-2">
              
              <span>{{ coordinator.user.name }}</span>
            </td>
            <!-- Email -->
            <td class="px-6 py-4 text-gray-800 dark:text-gray-100">
              {{ coordinator.user.email }}
            </td>
            <!-- Phone -->
            <td class="px-6 py-4 text-gray-800 dark:text-gray-100">
              {{ coordinator.user.phone || 'N/A' }}
            </td>
            <!-- Status -->
            <td class="px-6 py-4">
              <span
                class="text-sm px-2 py-1 rounded font-semibold"
                :class="coordinator.user.status === 'Active'
                  ? 'bg-green-100 text-green-800 dark:bg-green-600 dark:text-white'
                  : 'bg-gray-300 text-gray-800 dark:bg-gray-600 dark:text-white'"
              >
                {{ coordinator.status === 'Active' ? ($t("status.active") || "Active") : ($t("status.inactive") || "Inactive") }}
              </span>
            </td>
            <!-- Actions -->
            <td class="px-6 py-4 flex items-center gap-3">
              <Link
                v-if="coordinator.id"
                :href="route('coordinators.show', { coordinator: coordinator.id })"
                class="text-blue-500 hover:text-blue-700"
                title="View"
              >
                <EyeIcon class="w-5 h-5" />
              </Link>
            </td>
          </tr>
        </tbody>
      </Table>
      <!-- Pagination -->
      <div v-if="coordinators.meta && coordinators.meta.links" class="mt-4">
        <nav class="flex justify-center">
          <ul class="inline-flex -space-x-px">
            <li v-for="link in coordinators.meta.links" :key="link.label">
              <Link
                v-if="link.url"
                :href="link.url"
                class="px-3 py-1 border rounded-l-lg text-sm"
                :class="{
                  'bg-blue-600 text-white': link.active,
                  'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700': !link.active
                }"
                v-html="link.label"
              />
              <span
                v-else
                class="px-3 py-1 border rounded-l-lg text-sm text-gray-400"
                v-html="link.label"
              />
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </AppLayout>
</template>