<script setup>
import { ref, computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    hasGrades: { type: [Array, Object], required: true }, // collection of students with grades
});

// normalize to array
const students = computed(() => {
    if (Array.isArray(props.hasGrades)) return props.hasGrades;
    if (!props.hasGrades) return [];
    return Object.values(props.hasGrades);
});

const studentsCount = computed(() => students.value.length);

function gradesCount(student) {
    return (student.grades || []).length;
}

function semestersCount(student) {
    return (student.semesters || []).length;
}

// UI state for modern view
const search = ref("");
const page = ref(1);
const perPage = ref(10);
const sortField = ref("lastName");
const sortDir = ref("asc");

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return students.value;
    return students.value.filter(s => {
        const fullName = `${s.firstName || ""} ${s.middleName || ""} ${s.lastName || ""}`.toLowerCase();
        const idNo = (s.idNo || s.id || "").toString().toLowerCase();
        return fullName.includes(q) || idNo.includes(q);
    });
});

const sortedStudents = computed(() => {
    return [...filtered.value].sort((a, b) => {
        const aVal = (a[sortField.value] || "").toString().toLowerCase();
        const bVal = (b[sortField.value] || "").toString().toLowerCase();
        if (aVal === bVal) return 0;
        return sortDir.value === "asc" ? (aVal < bVal ? -1 : 1) : (aVal > bVal ? -1 : 1);
    });
});

const totalPages = computed(() => Math.max(1, Math.ceil(sortedStudents.value.length / perPage.value)));

const paginated = computed(() => {
    const start = (page.value - 1) * perPage.value;
    return sortedStudents.value.slice(start, start + perPage.value);
});

function setSort(field) {
    if (sortField.value === field) {
        sortDir.value = sortDir.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDir.value = "asc";
    }
    page.value = 1;
}

function goPrev() {
    if (page.value > 1) page.value--;
}
function goNext() {
    if (page.value < totalPages.value) page.value++;
}
</script>

<template>
  <Head title="Students With Grades" />

  <div class="max-w-6xl mx-auto p-6">
    <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold">Students With Grades</h1>
        <p class="text-sm text-gray-600 mt-1">
          Total:
          <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            {{ studentsCount }}
          </span>
        </p>
      </div>

      <div class="flex items-center gap-3 w-full md:w-auto">
        <div class="relative flex-1 md:flex-none">
          <input v-model="search" type="search" placeholder="Search student name or ID..." class="w-full md:w-80 px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        </div>

        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Per page</label>
          <select v-model.number="perPage" class="px-2 py-1 border rounded">
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="25">25</option>
          </select>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
      <table class="min-w-full table-fixed">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-4 py-3 w-12 text-left">#</th>
            <th @click="setSort('lastName')" class="px-4 py-3 cursor-pointer">Student
              <span class="text-xs text-gray-400 ml-1" v-if="sortField==='lastName'">{{ sortDir==='asc' ? '↑' : '↓' }}</span>
            </th>
            <th @click="setSort('idNo')" class="px-4 py-3 w-36 cursor-pointer">ID
              <span class="text-xs text-gray-400 ml-1" v-if="sortField==='idNo'">{{ sortDir==='asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="px-4 py-3 w-40">Study Mode</th>
            <th class="px-4 py-3 w-28 text-center">Semesters</th>
            <th class="px-4 py-3 w-28 text-center">Grades</th>
            <th class="px-4 py-3 w-36 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(student, idx) in paginated" :key="student.id" :class="idx % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'">
            <td class="px-4 py-3 text-sm">{{ (page-1)*perPage + idx + 1 }}</td>
            <td class="px-4 py-3 flex items-center gap-3">
              <div class="flex items-center justify-center w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-medium">
                {{ (student.firstName || '').charAt(0) }}{{ (student.lastName || '').charAt(0) }}
              </div>
              <div>
                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ student.firstName }} {{ student.middleName || '' }} {{ student.lastName }}
                </div>
                <div class="text-xs text-gray-500">{{ student.program?.name || '' }}</div>
              </div>
            </td>
            <td class="px-4 py-3 text-sm">{{ student.idNo || student.id }}</td>
            <td class="px-4 py-3 text-sm">{{ student.studyMode?.name || 'N/A' }}</td>
            <td class="px-4 py-3 text-center">
              <span class="px-2 py-0.5 rounded-full bg-gray-100 text-sm">{{ semestersCount(student) }}</span>
            </td>
            <td class="px-4 py-3 text-center">
              <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-sm">{{ gradesCount(student) }}</span>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="inline-flex gap-2">
                <Link :href="route('students.transcript', student.id)" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded">Transcript</Link>
                <Link :href="route('students.profile', student.id)" class="px-3 py-1 text-sm border rounded">Profile</Link>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="px-4 py-3 flex items-center justify-between border-t bg-gray-50 dark:bg-gray-700">
        <div class="text-sm text-gray-600">
          Showing <span class="font-medium">{{ ((page-1)*perPage)+1 }}</span> to <span class="font-medium">{{ Math.min(page*perPage, sortedStudents.length) }}</span> of <span class="font-medium">{{ sortedStudents.length }}</span> results
        </div>
        <div class="flex items-center gap-2">
          <button @click="goPrev" :disabled="page<=1" class="px-3 py-1 border rounded disabled:opacity-50">Prev</button>
          <div class="text-sm">Page {{ page }} / {{ totalPages }}</div>
          <button @click="goNext" :disabled="page>=totalPages" class="px-3 py-1 border rounded disabled:opacity-50">Next</button>
        </div>
      </div>
    </div>
  </div>
</template>
