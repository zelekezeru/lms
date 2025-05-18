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

// Export PDF
// This function generates a PDF of the student's transcript
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

  const allGrades = [];

  // === Show 2 semesters per row ===
  for (let i = 0; i < semesters.length; i += 2) {
    const sem1 = semesters[i];
    const sem2 = semesters[i + 1];

    let maxY = y;

    // Helper function to draw a semester block
    const drawSemester = (semester, startX) => {
      if (!semester) return;

      const grades = semester.grades.filter(g => g.student_id === student.id);
      if (grades.length === 0) return;

      doc.setFontSize(12);
      doc.text(
        `Semester: ${semester.name || ''} ${semester.year?.name || ''} `,
        startX,
        y
      );

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
        startX: startX,
        theme: 'grid',
        styles: { fontSize: 10 },
        headStyles: { fillColor: [41, 128, 185], textColor: 255 },
        margin: { left: startX, right: 10 },
        didDrawPage: (data) => {
          if (data.cursor.y > maxY) maxY = data.cursor.y;
        }
      });

      const semGPA = calculateGPA(grades, student.id);
      allGrades.push(...grades);
      doc.setFontSize(11);
      // Right-align the Semester GPA at the far right end of the table
      doc.text(`Sem GPA: ${semGPA}`, pageWidth - 20, maxY + 6, { align: "right" });
    };

    // Draw both semesters side by side
    drawSemester(sem1, 14);              // Left table
    drawSemester(sem2, pageWidth / 2);   // Right table

    y = maxY + 16;
  }
// GPA 
  

  // === Cumulative GPA ===
  const cumulativeGPA = calculateGPA(allGrades, student.id);
  doc.setFontSize(12);
  doc.setTextColor(41, 128, 185); // Match the blue style used above
  doc.text(`Cum GPA: ${cumulativeGPA}`, pageWidth - 20, y, { align: "right" });
  y += 20;

  // === Footer Section ===
  const footerY = pageHeight - 30;
  doc.setFontSize(10);

  // Straight line to separate the footer
  doc.setDrawColor(200);
  doc.line(14, footerY - 2, pageWidth - 14, footerY);

  doc.text("Registrar Signature: ____________________", 14, footerY + 2);
  const today = new Date().toLocaleDateString();
  doc.text("Date: " + today, pageWidth - 50, footerY + 7, { align: "right" });
  doc.text("Email: registerar@sits.edu.et", pageWidth - 50, footerY + 16, { align: "right" });
  doc.text("Grading Scale: A = 4.0, A- = 3.7, B+ = 3.3, B = 3.0, B- = 2.7, etc.", 14, footerY + 10);
  doc.text("This is an official transcript issued by Shiloh International Theological Seminary.", 14, footerY + 16);

  doc.save(`${student.firstName}_${student.middleName}_${student.lastName}_Transcript.pdf`);
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
        <p><strong>Student:</strong> {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}</p>
        <p><strong>ID:</strong> {{ student.idNo }}</p>
        <p><strong>Program:</strong> {{ student.program?.name }}</p>
        <p><strong>Track:</strong> {{ student.track?.name }}</p>
        <p><strong>Class of:</strong> {{ student.year?.name }}</p>
        <p><strong>Semester:</strong> {{ student.semester?.name }}</p>
        <p><strong>Study Mode:</strong> {{ student.studyMode?.name }}</p>
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
