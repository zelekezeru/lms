<script setup>
import { ref, computed } from "vue";
import * as XLSX from "xlsx";
import { 
  ArrowDownTrayIcon, 
  MagnifyingGlassIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon,
  CalendarDaysIcon
} from "@heroicons/vue/24/solid";

const props = defineProps({
  course: {
    type: Object,
    required: true,
  },
  section: {
    type: Object,
    required: true,
  },
  students: {
    type: Array,
    required: true,
  },
  classSessions: {
    type: Array,
    required: true,
    default: () => [],
  },
  activeSemester: {
    type: Object,
    required: true,
  },
});

const searchQuery = ref("");

// Filter sessions that have attendance records
const sessionsWithAttendance = computed(() => {
  return props.classSessions.filter(
    (session) => session.attendances && session.attendances.length > 0
  );
});

// Calculate student attendance totals and rates
const studentAttendanceSummary = computed(() => {
  const sessions = sessionsWithAttendance.value;
  return props.students.map((student) => {
    let present = 0;
    let absent = 0;
    let excused = 0;
    let total = 0;

    const history = {};

    sessions.forEach((session) => {
      const record = session.attendances.find(
        (att) => (att.studentId || att.student_id) === student.id
      );
      if (record) {
        history[session.id] = record.status;
        total++;
        if (record.status === "present") present++;
        else if (record.status === "absent") absent++;
        else if (record.status === "permission" || record.status === "excused") excused++;
      } else {
        history[session.id] = "-";
      }
    });

    const rate = total > 0 ? ((present / total) * 100).toFixed(0) : "100";

    return {
      id: student.id,
      name: `${student.firstName} ${student.middleName} ${student.lastName}`,
      idNo: student.idNo,
      present,
      absent,
      excused,
      total,
      rate,
      history,
    };
  });
});

// Search filter
const filteredStudents = computed(() => {
  if (!searchQuery.value) return studentAttendanceSummary.value;
  const q = searchQuery.value.toLowerCase();
  return studentAttendanceSummary.value.filter(
    (s) => s.name.toLowerCase().includes(q) || s.idNo.toLowerCase().includes(q)
  );
});

// Class attendance statistics
const stats = computed(() => {
  const summary = studentAttendanceSummary.value;
  const totalSessions = sessionsWithAttendance.value.length;
  if (summary.length === 0 || totalSessions === 0) {
    return { avgRate: 0, totalP: 0, totalA: 0, totalE: 0 };
  }

  let totalPresent = 0;
  let totalAbsent = 0;
  let totalExcused = 0;
  let totalRecords = 0;

  summary.forEach((s) => {
    totalPresent += s.present;
    totalAbsent += s.absent;
    totalExcused += s.excused;
    totalRecords += s.total;
  });

  const avgRate = totalRecords > 0 ? ((totalPresent / totalRecords) * 100).toFixed(0) : 0;

  return {
    avgRate,
    totalP: totalPresent,
    totalA: totalAbsent,
    totalE: totalExcused,
  };
});

// Excel Export
const exportToExcel = () => {
  const excelData = props.students.map((student, idx) => {
    const summary = studentAttendanceSummary.value.find((s) => s.id === student.id);
    const row = {
      "#": idx + 1,
      "Student Name": `${student.firstName} ${student.middleName} ${student.lastName}`,
      "ID Number": student.idNo,
    };

    // Add column for each session date
    sessionsWithAttendance.value.forEach((session) => {
      row[session.date] = summary?.history[session.id] || "-";
    });

    row["Total Present"] = summary?.present ?? 0;
    row["Total Absent"] = summary?.absent ?? 0;
    row["Total Excused"] = summary?.excused ?? 0;
    row["Attendance Rate (%)"] = `${summary?.rate ?? 100}%`;

    return row;
  });

  const worksheet = XLSX.utils.json_to_sheet(excelData);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Attendance Worksheet");

  // Adjust column widths
  const maxProps = [{ wch: 4 }, { wch: 30 }, { wch: 15 }];
  sessionsWithAttendance.value.forEach(() => maxProps.push({ wch: 12 }));
  maxProps.push({ wch: 15 }, { wch: 15 }, { wch: 15 }, { wch: 20 });
  worksheet["!cols"] = maxProps;

  XLSX.writeFile(
    workbook,
    `${props.course.code}_${props.section.name}_Attendance.xlsx`
  );
};

const getStatusBadge = (status) => {
  if (status === "present") return "bg-green-100 text-green-800 dark:bg-green-950/20 dark:text-green-400";
  if (status === "absent") return "bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-400";
  if (status === "permission" || status === "excused") return "bg-yellow-100 text-yellow-800 dark:bg-yellow-950/20 dark:text-yellow-400";
  return "bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-600";
};

const getStatusLabel = (status) => {
  if (status === "present") return "P";
  if (status === "absent") return "A";
  if (status === "permission" || status === "excused") return "Ex";
  return "-";
};
</script>

<template>
  <div class="space-y-8">
    <!-- Attendance Summary Dashboards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border dark:border-gray-700 flex items-center space-x-4">
        <div class="p-3 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
          <CheckCircleIcon class="w-6 h-6" />
        </div>
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Average Attendance</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.avgRate }}%</p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border dark:border-gray-700 flex items-center space-x-4">
        <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
          <CalendarDaysIcon class="w-6 h-6" />
        </div>
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Sessions Recorded</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ sessionsWithAttendance.length }}</p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border dark:border-gray-700 flex items-center space-x-4">
        <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400">
          <XCircleIcon class="w-6 h-6" />
        </div>
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Absences</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.totalA }}</p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border dark:border-gray-700 flex items-center space-x-4">
        <div class="p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
          <ClockIcon class="w-6 h-6" />
        </div>
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Excused</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.totalE }}</p>
        </div>
      </div>
    </div>

    <!-- Filters & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border dark:border-gray-700">
      <div class="relative w-full sm:w-72">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
        </span>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search student or ID..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
        />
      </div>

      <button
        @click="exportToExcel"
        :disabled="sessionsWithAttendance.length === 0"
        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium shadow-sm transition space-x-2 w-full sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <ArrowDownTrayIcon class="w-5 h-5" />
        <span>Export Sheet</span>
      </button>
    </div>

    <!-- Attendance Worksheet Grid -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">#</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[180px]">Student</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-32">ID Number</th>
            <!-- Dynamically list class dates as columns -->
            <th 
              v-for="session in sessionsWithAttendance" 
              :key="session.id"
              scope="col" 
              class="px-3 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[70px]"
            >
              <div class="whitespace-nowrap">{{ session.date }}</div>
              <span class="text-[9px] text-gray-400 font-normal">({{ session.type }})</span>
            </th>
            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20">P</th>
            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20">A</th>
            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20">Ex</th>
            <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">Rate</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr 
            v-for="(student, index) in filteredStudents" 
            :key="student.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150"
          >
            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-medium">{{ index + 1 }}</td>
            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ student.name }}</td>
            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ student.idNo }}</td>
            
            <!-- Render badges for each session status -->
            <td 
              v-for="session in sessionsWithAttendance" 
              :key="session.id"
              class="px-3 py-3 text-center"
            >
              <span 
                class="inline-block w-8 py-1 rounded text-xs font-bold text-center"
                :class="getStatusBadge(student.history[session.id])"
              >
                {{ getStatusLabel(student.history[session.id]) }}
              </span>
            </td>

            <td class="px-4 py-3 text-center text-sm font-medium text-green-600 dark:text-green-400">{{ student.present }}</td>
            <td class="px-4 py-3 text-center text-sm font-medium text-red-600 dark:text-red-400">{{ student.absent }}</td>
            <td class="px-4 py-3 text-center text-sm font-medium text-yellow-600 dark:text-yellow-400">{{ student.excused }}</td>
            
            <td class="px-4 py-3 text-center whitespace-nowrap">
              <span 
                class="px-2 py-0.5 text-xs font-bold rounded-lg"
                :class="parseInt(student.rate) >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-950/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-950/20 dark:text-red-400'"
              >
                {{ student.rate }}%
              </span>
            </td>
          </tr>
          <tr v-if="filteredStudents.length === 0">
            <td :colspan="7 + sessionsWithAttendance.length" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
              No students matched your query.
            </td>
          </tr>
          <tr v-else-if="sessionsWithAttendance.length === 0">
            <td :colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
              No attendance records found yet. Go to the "Class Sessions" tab to take attendance for a session!
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
