<script setup>
import { computed, ref, watch } from "vue";
import { usePage, Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

// Props
const page = usePage();
const section = computed(() => page.props.section);
const students = computed(() => page.props.students || []);
const importReport = computed(() => page.props.import_report || {});
const success = computed(() => importReport.value.success || page.props.success);
const error = computed(() => importReport.value.error || page.props.error);

// Search with debounce
const search = ref("");
const searchQuery = ref("");

let debounceTimeout;
watch(search, (value) => {
  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(() => {
    searchQuery.value = value.trim();
  }, 300);
});

// Filter logic
const filteredStudents = computed(() => {
  if (!searchQuery.value) return students.value;
  const term = searchQuery.value.toLowerCase();
  return students.value.filter(student =>
    (student.name && student.name.toLowerCase().includes(term)) ||
    (student.email && student.email.toLowerCase().includes(term)) ||
    (student.phone && student.phone.includes(term)) ||
    (student.mobile_phone && student.mobile_phone.includes(term))
  );
});

function route(name, id) {
  return `/students/${id}`;
}
</script>

<template>
  <Head title="Imported Students" />
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 py-6">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
          Imported Students for Section: <span class="text-blue-600 dark:text-blue-400">{{ section.name }}</span>
        </h1>
      </div>

      <div v-if="success" class="mb-4 px-4 py-3 rounded bg-green-100 text-green-700 border border-green-200 dark:bg-green-900 dark:text-green-200 dark:border-green-700">
        ✅ {{ success }}
      </div>
      <div v-if="error" class="mb-4 px-4 py-3 rounded bg-red-100 text-red-700 border border-red-200 dark:bg-red-900 dark:text-red-200 dark:border-red-700">
        ⚠️ {{ error }}
      </div>

    <div class="bg-gradient-to-r from-blue-100 via-purple-100 to-pink-100 border-2 border-blue-400 rounded-lg p-5 mb-6 shadow-lg dark:bg-gradient-to-r dark:from-blue-900 dark:via-purple-900 dark:to-pink-900 dark:border-blue-600 flex flex-col items-center text-center">
        <h2 class="text-lg font-bold mb-3 text-blue-800 dark:text-blue-200 flex items-center gap-2">
            <span class="text-2xl">📊</span> Import Report
        </h2>
        <ul class="text-lg flex flex-wrap gap-6">
            <li>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded bg-green-200 text-green-900 font-bold dark:bg-green-700 dark:text-green-100 text-xl">
                ✔️ Registered: <span>{{ importReport.registeredCount || 0 }}</span>
                </span>
            </li>
            <li>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded bg-red-200 text-red-900 font-bold dark:bg-red-700 dark:text-red-100 text-xl">
                ❌ Not Registered: <span>{{ importReport.notRegisteredCount || 0 }}</span>
                </span>
            </li>
            <li>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded bg-yellow-200 text-yellow-900 font-bold dark:bg-yellow-700 dark:text-yellow-100 text-xl">
                ⚡ Duplicate Data: <span>{{ importReport.duplicateData || 0 }}</span>
                </span>
            </li>
            <!-- Go to section link -->
            <li>
                <a
                  :href="route('sections.show', section.id)"
                  class="inline-flex items-center gap-2 px-4 py-2 rounded bg-blue-200 text-blue-900 font-bold dark:bg-blue-700 dark:text-blue-100 text-xl hover:bg-blue-300 dark:hover:bg-blue-600 transition"
                >
                  🔗 Go to Section
                </a>
            </li>
        </ul>
    </div>

      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">👥 Imported Students</h1>
      </div>

      <div class="overflow-x-auto rounded shadow">
        <table class="w-full text-sm text-left text-gray-700 dark:text-gray-200">
          <thead class="bg-gray-100 text-xs uppercase text-gray-600 dark:bg-gray-700 dark:text-gray-300">
            <tr>
              <th class="px-4 py-2">#</th>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Id Number</th>
              <th class="px-4 py-2">Phone</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(student, idx) in filteredStudents"
              :key="student.id"
              :class=" [
                idx % 2 === 0
                  ? 'bg-white dark:bg-gray-900'
                  : 'bg-gray-50 dark:bg-gray-800',
                'hover:bg-blue-50 dark:hover:bg-blue-900 transition'
              ]"
            >
              <td class="px-4 py-2">{{ idx + 1 }}</td>
              <td class="px-4 py-2">
                <a :href="route('students.show', student.id)" class="text-blue-600 hover:underline dark:text-blue-400">
                  {{ student.first_name }} {{ student.middle_name ? student.middle_name + ' ' : '' }} {{ student.last_name }}
                </a>
              </td>
              <td class="px-4 py-2">{{ student.id_no }}</td>
              <td class="px-4 py-2">{{ student.phone || student.mobile_phone || '-' }}</td>
              <td class="px-4 py-2">
                <a
                  :href="route('students.show', student.id)"
                  class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                  title="View student details"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="inline w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 
                      7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </a>
              </td>
            </tr>
            <tr v-if="filteredStudents.length === 0">
              <td colspan="6" class="text-center px-4 py-6 text-gray-500 dark:text-gray-400">
                No students found. Try adjusting your search.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
