<script setup>
import { ref, computed } from "vue";
import * as XLSX from "xlsx";
import { 
  ArrowDownTrayIcon, 
  MagnifyingGlassIcon,
  AcademicCapIcon,
  CheckBadgeIcon,
  ExclamationTriangleIcon,
  PresentationChartLineIcon
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
  semester: {
    type: Object,
    required: true,
  },
  instructor: {
    type: Object,
    required: true,
  },
  weights: {
    type: Array,
    required: true,
  },
  grades: {
    type: Array,
    required: true,
  },
  students: {
    type: Array,
    required: true,
  },
  studentResults: {
    type: Object,
    required: true,
  },
  courseOffering: {
    type: Object,
    required: true,
  },
});

const searchQuery = ref("");

// Total points sum of weights (typically 100)
const totalWeightPoints = computed(() =>
  props.weights.reduce((sum, w) => sum + (parseFloat(w.point) || 0), 0)
);

// Get student's total points from results
const getStudentTotalPoints = (studentId) => {
  let total = 0;
  props.weights.forEach((weight) => {
    const result = props.studentResults[studentId]?.[weight.id];
    if (result && result.point !== null && result.point !== undefined) {
      total += parseFloat(result.point);
    }
  });
  return parseFloat(total.toFixed(2));
};

// Check if final grade is submitted in the DB
const getSubmittedGrade = (studentId) => {
  return props.grades?.find((g) => g.student_id === studentId);
};

// Grade letters mapping
const getGradeLetter = (point) => {
  point = parseFloat(point);
  if (isNaN(point)) return "N/A";
  if (point >= 94) return "A";
  if (point >= 90) return "A-";
  if (point >= 87) return "B+";
  if (point >= 84) return "B";
  if (point >= 80) return "B-";
  if (point >= 77) return "C+";
  if (point >= 74) return "C";
  if (point >= 70) return "C-";
  if (point >= 67) return "D+";
  if (point >= 64) return "D";
  if (point >= 60) return "D-";
  if (point === 0) return "NG";
  return "F";
};

// Calculated student rows
const computedStudentGrades = computed(() => {
  return props.students.map((student) => {
    const totalPoints = getStudentTotalPoints(student.id);
    const dbGrade = getSubmittedGrade(student.id);
    const letter = dbGrade ? dbGrade.grade_letter : getGradeLetter(totalPoints);
    const score = dbGrade ? parseFloat(dbGrade.grade_point) : totalPoints;
    const isSubmitted = !!dbGrade;

    return {
      id: student.id,
      name: `${student.firstName} ${student.middleName} ${student.lastName}`,
      idNo: student.idNo,
      score,
      letter,
      isSubmitted,
      status: dbGrade ? dbGrade.grade_status : "Draft",
    };
  });
});

// Search filter
const filteredStudents = computed(() => {
  if (!searchQuery.value) return computedStudentGrades.value;
  const q = searchQuery.value.toLowerCase();
  return computedStudentGrades.value.filter(
    (s) => s.name.toLowerCase().includes(q) || s.idNo.toLowerCase().includes(q)
  );
});

// Class Statistics
const stats = computed(() => {
  const list = computedStudentGrades.value;
  if (list.length === 0) return { avg: 0, passRate: 0, highest: 0, dist: {} };

  const totalSum = list.reduce((sum, s) => sum + s.score, 0);
  const avg = (totalSum / list.length).toFixed(1);

  const passes = list.filter((s) => s.letter !== "F" && s.letter !== "NG").length;
  const passRate = ((passes / list.length) * 100).toFixed(0);

  const highest = Math.max(...list.map((s) => s.score)).toFixed(1);

  // Distribution counts
  const dist = { A: 0, B: 0, C: 0, D: 0, F: 0 };
  list.forEach((s) => {
    if (s.letter.startsWith("A")) dist.A++;
    else if (s.letter.startsWith("B")) dist.B++;
    else if (s.letter.startsWith("C")) dist.C++;
    else if (s.letter.startsWith("D")) dist.D++;
    else dist.F++;
  });

  return { avg, passRate, highest, dist };
});

// Excel Export
const exportToExcel = () => {
  const excelData = props.students.map((student, idx) => {
    const row = {
      "#": idx + 1,
      "Student Name": `${student.firstName} ${student.middleName} ${student.lastName}`,
      "ID Number": student.idNo,
    };
    
    props.weights.forEach((w) => {
      row[`${w.name} (${w.point}pt)`] = props.studentResults[student.id]?.[w.id]?.point ?? "N/A";
    });

    const studentTotal = getStudentTotalPoints(student.id);
    const dbGrade = getSubmittedGrade(student.id);

    row["Total Score"] = dbGrade ? dbGrade.grade_point : studentTotal;
    row["Grade"] = dbGrade ? dbGrade.grade_letter : getGradeLetter(studentTotal);
    row["Status"] = dbGrade ? dbGrade.grade_status : "Draft";
    
    return row;
  });

  const worksheet = XLSX.utils.json_to_sheet(excelData);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Grade Sheet");
  
  // Auto-fit column widths
  const maxProps = [{ wch: 4 }, { wch: 30 }, { wch: 15 }];
  props.weights.forEach(() => maxProps.push({ wch: 18 }));
  maxProps.push({ wch: 12 }, { wch: 10 }, { wch: 12 });
  worksheet["!cols"] = maxProps;

  XLSX.writeFile(
    workbook, 
    `${props.course.code}_${props.section.name}_GradeBook.xlsx`
  );
};

const getBadgeClass = (letter) => {
  if (letter.startsWith("A")) return "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300";
  if (letter.startsWith("B")) return "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300";
  if (letter.startsWith("C")) return "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300";
  if (letter.startsWith("D")) return "bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300";
  return "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300";
};
</script>

<template>
  <div class="space-y-8">
    <!-- Gradebook Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border dark:border-gray-700 flex items-center space-x-4">
        <div class="p-3 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
          <AcademicCapIcon class="w-6 h-6" />
        </div>
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Class Average</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.avg }} / {{ totalWeightPoints }}</p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border dark:border-gray-700 flex items-center space-x-4">
        <div class="p-3 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
          <CheckBadgeIcon class="w-6 h-6" />
        </div>
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Pass Rate</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.passRate }}%</p>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border dark:border-gray-700 flex items-center space-x-4">
        <div class="p-3 rounded-lg bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400">
          <PresentationChartLineIcon class="w-6 h-6" />
        </div>
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Highest Score</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.highest }}</p>
        </div>
      </div>

      <!-- Grade Distribution Mini Panel -->
      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border dark:border-gray-700">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Grade Distribution</p>
        <div class="space-y-1.5 text-xs">
          <div v-for="g in ['A', 'B', 'C', 'D', 'F']" :key="g" class="flex items-center space-x-2">
            <span class="w-3 text-gray-600 dark:text-gray-400 font-medium">{{ g }}</span>
            <div class="flex-1 bg-gray-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
              <div 
                class="h-full rounded-full" 
                :class="g === 'F' ? 'bg-red-500' : g === 'A' ? 'bg-green-500' : 'bg-blue-500'"
                :style="{ width: `${computedStudentGrades.length ? (stats.dist[g] / computedStudentGrades.length) * 100 : 0}%` }"
              ></div>
            </div>
            <span class="w-4 text-right text-gray-500">{{ stats.dist[g] }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions & Filter -->
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
        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium shadow-sm transition space-x-2 w-full sm:w-auto"
      >
        <ArrowDownTrayIcon class="w-5 h-5" />
        <span>Export to Excel</span>
      </button>
    </div>

    <!-- Grade Book Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">#</th>
            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[200px]">Student Name</th>
            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-36">ID Number</th>
            <!-- Dynamically listing assessment weights -->
            <th 
              v-for="weight in weights" 
              :key="weight.id" 
              scope="col" 
              class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider"
            >
              {{ weight.name }}
              <span class="block text-[10px] text-gray-400 font-normal">({{ weight.point }} pts)</span>
            </th>
            <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">Total</th>
            <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">Letter</th>
            <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr 
            v-for="(student, index) in filteredStudents" 
            :key="student.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150"
          >
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 font-medium">{{ index + 1 }}</td>
            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ student.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ student.idNo }}</td>
            <!-- Student score for each weight -->
            <td 
              v-for="weight in weights" 
              :key="weight.id" 
              class="px-6 py-4 text-center text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              {{ studentResults[student.id]?.[weight.id]?.point ?? "N/A" }}
            </td>
            <!-- Calculated Total score -->
            <td class="px-6 py-4 text-center text-sm font-bold text-gray-900 dark:text-white">
              {{ student.score }}
            </td>
            <!-- Letter grade badge -->
            <td class="px-6 py-4 text-center whitespace-nowrap">
              <span 
                class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm"
                :class="getBadgeClass(student.letter)"
              >
                {{ student.letter }}
              </span>
            </td>
            <!-- Status (Draft vs Submitted) -->
            <td class="px-6 py-4 text-center whitespace-nowrap">
              <span 
                class="px-2 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-lg"
                :class="student.isSubmitted ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-900 dark:text-gray-400'"
              >
                {{ student.status }}
              </span>
            </td>
          </tr>
          <tr v-if="filteredStudents.length === 0">
            <td :colspan="6 + weights.length" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
              <ExclamationTriangleIcon class="w-8 h-8 mx-auto text-gray-400 mb-2" />
              No student records matched your search.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
