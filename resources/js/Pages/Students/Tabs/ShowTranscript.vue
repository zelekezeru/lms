<script setup>
import { ref, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

// PDF
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";

// Props
const props = defineProps({
  student: {
    type: Object,
    required: true,
  },
  semesters: {
      type: Object,
      required: true,
    },
});

function getGradePointFromLetter(point) {
  point = parseFloat(point);
  if (isNaN(point)) return 0;
  if (point >= 94) return 4.0;   // A
  if (point >= 90) return 3.7;   // A-
  if (point >= 87) return 3.3;   // B+
  if (point >= 84) return 3.0;   // B
  if (point >= 80) return 2.7;   // B-
  if (point >= 77) return 2.3;   // C+
  if (point >= 74) return 2.0;   // C
  if (point >= 70) return 1.7;   // C-
  if (point >= 67) return 1.3;   // D+
  if (point >= 64) return 1.0;   // D
  if (point >= 60) return 0.7;   // D-
  return 0.0;                    // F
}

// Calculate GPA
function calculateGPA(grades, studentId) {
  const filtered = grades.filter(g => g.student_id === studentId);

  let totalPoints = 0;
  let totalCredits = 0;

  filtered.forEach((g) => {
    const numericScore = parseFloat(g.grade_point || 0);
    const credit = parseFloat(g.course?.credit_hours || 0);
    const gradePoint = getGradePointFromLetter(numericScore);

    totalPoints += gradePoint * credit;
    totalCredits += credit;
  });

  return totalCredits > 0 ? (totalPoints / totalCredits).toFixed(2) : "0.00";
}

// Calculate Commulative GPA
function calculateCumulativeGPA(semesters, studentId) {
  const semesterGPAs = [];

  semesters.forEach((semester) => {
    const grades = semester.grades.filter(g => g.student_id === studentId);

    if (grades.length > 0) {
      const gpa = calculateGPA(grades, studentId); // uses your existing GPA function
      semesterGPAs.push(parseFloat(gpa));
    }
  });

  if (semesterGPAs.length === 0) return "0.00";

  const total = semesterGPAs.reduce((sum, gpa) => sum + gpa, 0);
  return (total / semesterGPAs.length).toFixed(2);
}


// Export PDF
function exportPDF() {
  const doc = new jsPDF({
    orientation: 'landscape',
    unit: 'mm',
    format: 'a4'
  });

  const student = props.student;
  const semesters = props.semesters;
  const pageWidth = doc.internal.pageSize.getWidth();
  const pageHeight = doc.internal.pageSize.getHeight();

  const footerY = pageHeight - 30;
  const summaryY = footerY - 20; // space above the footer for the two rows

  doc.setFontSize(10);
  const blockWidth = 60;
  const marginRight = 14;

  // === Header ===
  doc.setFontSize(16);
  doc.text("Shiloh International Theological Seminary", pageWidth / 2, 20, { align: "center" });
  doc.setFontSize(14);
  doc.text("Student Record", pageWidth / 2, 28, { align: "center" });

  // === Student Info ===
  doc.setFontSize(11);
  let y = 36;
  doc.text(`Name: ${student.firstName} ${student.midleName} ${student.lastName}`, 14, y);
  doc.text(`Student ID: ${student.idNo}`, 14, y + 6);
  doc.text(`Birth Date: ${student.dateOfBirth || 'N/A'}`, 14, y + 12);
  doc.text(`Program of Study: ${student.program?.name || 'N/A'}`, 105, y);
  doc.text(`Track: ${student.track?.name || 'N/A'}`, 105, y + 6);
  doc.text(`Study Mode: ${student.studyMode?.name || 'N/A'}`, 105, y + 12);
  doc.text(`Graduation Date: ${student.graduationDate || 'N/A'}`, 105, y + 18);

  y += 30;

const cumulativeGPA = calculateCumulativeGPA(semesters, student.id);

  // === Iterate through each semester ===
  for (const semester of semesters) {
    const grades = semester.grades.filter(g => g.student_id === student.id);
    if (grades.length === 0) continue;

    doc.setFontSize(12);
    doc.text(`Semester: ${semester.year?.name ?? 'Unknown Year'} - ${semester.name ?? 'Unknown Semester'}`, 14, y);

    const tableData = grades.map((g) => {
      const credit = parseFloat(g.course?.credit_hours || 0);
      const numericScore = parseFloat(g.grade_point || 0);
      const gradePoint = getGradePointFromLetter(numericScore);
      const points = (gradePoint * credit).toFixed(2);
      return [
        g.course?.code || 'N/A',
        g.course?.name || 'N/A',
        credit.toString(),
        g.grade_letter || 'N/A',
        points
      ];
    });

    autoTable(doc, {
      head: [['Course', 'Course Name', 'Credit', 'Grade', 'Points']],
      body: tableData,
      startY: y + 6,
      theme: 'grid',
      styles: { fontSize: 10 },
      headStyles: { fillColor: [41, 128, 185], textColor: 255 },
      margin: { left: 14, right: 10 },
      didDrawPage: (data) => {
        y = data.cursor.y + 4;
      }
    });

    const semGPA = calculateGPA(grades, student.id);
    const totalCredits = grades.reduce((acc, g) => acc + parseFloat(g.course?.credit_hours || 0), 0);
    const totalPoints = grades.reduce((acc, g) => {
      const gp = getGradePointFromLetter(parseFloat(g.grade_point));
      const credit = parseFloat(g.course?.credit_hours || 0);
      return acc + gp * credit;
    }, 0);

    doc.setFontSize(11);
    doc.text(`Semester GPA: ${semGPA}`, pageWidth - (blockWidth * 2 + marginRight), y + 2);
    doc.text(`Total Credits: ${totalCredits}`, pageWidth - (blockWidth + marginRight), y + 2);

    doc.text(`Total Points: ${totalPoints.toFixed(2)}`, pageWidth - (blockWidth * 2 + marginRight), y + 10);
    doc.text(`Cumulative GPA: ${cumulativeGPA}`, pageWidth - (blockWidth + marginRight), y + 10);

    y = y + 18; // update y for next content

    if (y > pageHeight - 40) {
      doc.addPage();
      y = 20;
    }
  }

  // === Footer ===
  doc.setDrawColor(200);
  doc.line(14, footerY - 2, pageWidth - 14, footerY);
  doc.text("Registrar Signature: ____________________", 14, footerY + 2);

  const today = new Date().toLocaleDateString();
  doc.text("Date: " + today, pageWidth - 50, footerY + 7, { align: "right" });
  doc.text("Email: registerar@sits.edu.et", pageWidth - 50, footerY + 16, { align: "right" });

  doc.text("Grading Scale: A = 4.0, A- = 3.7, B+ = 3.3, B = 3.0, B- = 2.7, etc.", 14, footerY + 10);
  doc.text("This is an official transcript issued by Shiloh International Theological Seminary.", 14, footerY + 16);

  doc.save(`${student.firstName}_${student.midleName}_${student.lastName}_Transcript.pdf`);
}

</script>


<template>
    <Head title="Student Transcript" />
      <h1 class="text-2xl font-bold mb-2">
        <div class="flex flex-col sm:flex-row sm:items-end justify-center gap-2">
          <span class="text-gray-900 dark:text-gray-100">Shiloh International Theological Seminary</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-end justify-center gap-2">
          <span class="text-base font-normal sm:ml-4 text-gray-700 dark:text-gray-300">Student Official Transcript</span>
        </div>
      </h1>

      <Link as="button" type="button"
        @click="exportPDF"
        class="inline-flex items-center rounded-md bg-purple-600 dark:bg-purple-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-yellow-700 dark:hover:bg-yellow-800 focus:bg-yellow-700 dark:focus:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 mb-4"
      >
        Export PDF
      </Link>

      <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1">
          <p><strong>Student:</strong> {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}</p>
          <p><strong>ID:</strong> {{ student.idNo }}</p>
          <p><strong>Program:</strong> {{ student.program?.name }}</p>
          <p><strong>Track:</strong> {{ student.track?.name }}</p>
          <p><strong>Class of:</strong> {{ student.year?.name }}</p>
          <p><strong>Semester:</strong> {{ student.semester?.name }}</p>
          <p><strong>Study Mode:</strong> {{ student.studyMode?.name }}</p>
        </div>
      </div>

      <div v-for="(semester, index) in semesters" :key="index" class="mb-8">
        <h2 class="text-lg font-semibold mb-2 text-green-700 dark:text-green-400">
          {{ semester.year?.name ?? 'Unknown Year' }} - {{ semester.name ?? 'Unknown Semester' }}
        </h2>

        <div v-if="semester.grades.length > 0">
          <table class="min-w-full border rounded shadow bg-white dark:bg-gray-800">
            <thead class="bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-800 dark:text-gray-200">
              <tr>
                <th class="px-4 py-2 text-left">Course Code</th>
                <th class="px-4 py-2 text-left">Course Name</th>
                <th class="px-4 py-2 text-left">Credit</th>
                <th class="px-4 py-2 text-left">Grade Letter</th>
                <th class="px-4 py-2 text-left">Grade Point</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(grade, i) in semester.grades.filter(g => g.student_id === student.id)"
                :key="grade.id"
                :class="i % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'"
                class="text-sm text-gray-900 dark:text-gray-100 border-b dark:border-gray-700"
              >
                <td class="px-4 py-2">{{ grade.course?.code }}</td>
                <td class="px-4 py-2">{{ grade.course?.name }}</td>
                <td class="px-4 py-2">{{ grade.course?.credit_hours }}</td>
                <td class="px-4 py-2">{{ grade.grade_letter }}</td>
                <td class="px-4 py-2">{{ grade.grade_point }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-gray-50 dark:bg-gray-800 text-sm font-semibold">
                <td class="px-4 py-2 text-right" colspan="3"></td>
                <td class="px-4 py-2 text-green-700 dark:text-green-400">
                  <div class="flex flex-col sm:flex-row gap-2">
                    <div class="flex-1">
                      <p class="font-semibold">Semester GPA: {{ calculateGPA(semester.grades, student.id) }}</p>
                      <p class="mt-4 font-semibold">Cumulative GPA: {{ calculateGPA(semester.grades, student.id) }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-2 text-green-700 dark:text-green-400">
                  <div class="flex flex-col sm:flex-row gap-2">
                    <div class="flex-1">
                      <p class="font-semibold">Total Points: {{ semester.grades.filter(g => g.student_id === student.id).reduce((acc, g) => acc + (getGradePointFromLetter(g.grade_point) * parseFloat(g.course?.credit_hours || 0)), 0).toFixed(2) }}</p>
                      <p class="mt-4 font-semibold">Total Credits: {{ semester.grades.filter(g => g.student_id === student.id).reduce((acc, g) => acc + parseFloat(g.course?.credit_hours || 0), 0) }}</p>
                    </div>
                  </div>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div v-else class="text-gray-500 dark:text-gray-400 italic p-2">
          No grades available for this semester.
        </div>
      </div>
</template>
