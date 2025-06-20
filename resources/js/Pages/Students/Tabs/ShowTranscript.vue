<script setup>
import { ref, computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";

// Props
const props = defineProps({
  student: { type: Object, required: true },
  semesters: { type: Object, required: true },
});

// Grade Conversion
function getGradePointFromLetter(point) {
  point = parseFloat(point);
  if (isNaN(point)) return 0;
  if (point >= 94) return 4.0;
  if (point >= 90) return 3.7;
  if (point >= 87) return 3.3;
  if (point >= 84) return 3.0;
  if (point >= 80) return 2.7;
  if (point >= 77) return 2.3;
  if (point >= 74) return 2.0;
  if (point >= 70) return 1.7;
  if (point >= 67) return 1.3;
  if (point >= 64) return 1.0;
  if (point >= 60) return 0.7;
  return 0.0;
}

// GPA Calculations
function calculateGPA(grades, studentId) {
  const filtered = grades.filter(g => g.student_id === studentId);
  let totalPoints = 0;
  let totalCredits = 0;
  filtered.forEach((g) => {
    const gradePoint = getGradePointFromLetter(parseFloat(g.grade_point));
    const credit = parseFloat(g.course?.credit_hours || 0);
    totalPoints += gradePoint * credit;
    totalCredits += credit;
  });
  return totalCredits > 0 ? (totalPoints / totalCredits).toFixed(2) : "0.00";
}

// Progressive Cumulative GPA
const cumulativeGPAList = computed(() => {
  let list = [];
  let totalGPA = 0;
  let count = 0;
  props.semesters.forEach((semester, index) => {
    const grades = semester.grades.filter(g => g.student_id === props.student.id);
    if (grades.length > 0) {
      const gpa = parseFloat(calculateGPA(grades, props.student.id));
      totalGPA += gpa;
      count++;
      list.push({
        semesterIndex: index,
        cumulativeGPA: (totalGPA / count).toFixed(2)
      });
    } else {
      list.push({ semesterIndex: index, cumulativeGPA: count > 0 ? (totalGPA / count).toFixed(2) : "0.00" });
    }
  });
  return list;
});

// PDF Export
function exportPDF() {
  const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
  const student = props.student;
  const semesters = props.semesters;
  const pageWidth = doc.internal.pageSize.getWidth();
  const pageHeight = doc.internal.pageSize.getHeight();
  const footerY = pageHeight - 30;
  const blockWidth = 60;
  const marginRight = 14;
  let y = 36;

  doc.setFontSize(16);
  doc.text("Shiloh International Theological Seminary", pageWidth / 2, 20, { align: "center" });
  doc.setFontSize(14);
  doc.text("Student Record", pageWidth / 2, 28, { align: "center" });

  doc.setFontSize(11);
  doc.text(`Name: ${student.firstName} ${student.middleName} ${student.lastName}`, 14, y);
  doc.text(`Student ID: ${student.idNo}`, 14, y + 6);
  doc.text(`Birth Date: ${student.dateOfBirth || 'N/A'}`, 14, y + 12);
  doc.text(`Program of Study: ${student.program?.name || 'N/A'}`, 105, y);
  doc.text(`Track: ${student.track?.name || 'N/A'}`, 105, y + 6);
  doc.text(`Study Mode: ${student.studyMode?.name || 'N/A'}`, 105, y + 12);
  doc.text(`Graduation Date: ${student.graduationDate || 'N/A'}`, 105, y + 18);
  y += 30;

  props.semesters.forEach((semester, index) => {
    const grades = semester.grades.filter(g => g.student_id === student.id);
    if (grades.length === 0) return;

    const semGPA = calculateGPA(grades, student.id);
    const cumGPA = cumulativeGPAList.value[index]?.cumulativeGPA || "0.00";
    const totalCredits = grades.reduce((acc, g) => acc + parseFloat(g.course?.credit_hours || 0), 0);
    const totalPoints = grades.reduce((acc, g) => {
      const gp = getGradePointFromLetter(parseFloat(g.grade_point));
      const credit = parseFloat(g.course?.credit_hours || 0);
      return acc + gp * credit;
    }, 0);

    doc.setFontSize(12);
    doc.text(`Semester: ${semester.year?.name ?? 'Unknown Year'} - ${semester.name ?? 'Unknown Semester'}`, 14, y);

    const tableData = grades.map((g) => [
      g.course?.code || 'N/A',
      g.course?.name || 'N/A',
      `${g.course?.credit_hours || 0}`,
      g.grade_letter || 'N/A',
      (getGradePointFromLetter(parseFloat(g.grade_point)) * parseFloat(g.course?.credit_hours || 0)).toFixed(2),
    ]);

    autoTable(doc, {
      head: [['Course', 'Course Name', 'Credit', 'Grade', 'Points']],
      body: tableData,
      startY: y + 6,
      theme: 'grid',
      styles: { fontSize: 10 },
      headStyles: { fillColor: [41, 128, 185], textColor: 255 },
      margin: { left: 14, right: 10 },
      didDrawPage: (data) => { y = data.cursor.y + 4; }
    });

    doc.setFontSize(11);
    doc.text(`Semester GPA: ${semGPA}`, pageWidth - (blockWidth * 2 + marginRight), y + 2);
    doc.text(`Total Credits: ${totalCredits}`, pageWidth - (blockWidth + marginRight), y + 2);
    doc.text(`Total Points: ${totalPoints.toFixed(2)}`, pageWidth - (blockWidth * 2 + marginRight), y + 10);
    doc.text(`Cumulative GPA: ${cumGPA}`, pageWidth - (blockWidth + marginRight), y + 10);

    y += 18;
    if (y > pageHeight - 40) { doc.addPage(); y = 20; }
  });

  doc.setDrawColor(200);
  doc.line(14, footerY - 2, pageWidth - 14, footerY);
  doc.text("Registrar Signature: ____________________", 14, footerY + 2);
  doc.text("Date: " + new Date().toLocaleDateString(), pageWidth - 50, footerY + 7, { align: "right" });
  doc.text("Email: registerar@sits.edu.et", pageWidth - 50, footerY + 16, { align: "right" });
  doc.text("Grading Scale: A = 4.0, A- = 3.7, B+ = 3.3, B = 3.0, B- = 2.7, etc.", 14, footerY + 10);
  doc.text("This is an official transcript issued by Shiloh International Theological Seminary.", 14, footerY + 16);

  doc.save(`${student.firstName}_${student.middleName}_${student.lastName}_Transcript.pdf`);
}
</script>

<template>
  <Head title="Student Transcript" />
  <h1 class="text-2xl font-bold mb-2 text-center">
    <div>Shiloh International Theological Seminary</div>
    <div class="text-base font-normal text-gray-700 dark:text-gray-300">Student Official Transcript</div>
  </h1>

  <Link as="button" @click="exportPDF" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
    Export PDF
  </Link>

  <div class="mb-4 text-sm text-gray-600 dark:text-gray-300 grid sm:grid-cols-2 gap-x-8 gap-y-1">
    <p><strong>Student:</strong> {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}</p>
    <p><strong>ID:</strong> {{ student.idNo }}</p>
    <p><strong>Program:</strong> {{ student.program?.name }}</p>
    <p><strong>Track:</strong> {{ student.track?.name }}</p>
    <p><strong>Class of:</strong> {{ student.year?.name }}</p>
    <p><strong>Semester:</strong> {{ student.semester?.name }}</p>
    <p><strong>Study Mode:</strong> {{ student.studyMode?.name }}</p>
  </div>

  <div v-for="(semester, index) in semesters" :key="index" class="mb-8">
    <h2 class="text-lg font-semibold text-green-700 dark:text-green-400 mb-2">
      {{ semester.year?.name ?? 'Unknown Year' }} - {{ semester.name ?? 'Unknown Semester' }}
    </h2>

    <div v-if="semester.grades.length > 0">
      <table class="min-w-full border rounded bg-white dark:bg-gray-800 shadow">
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
            class="text-sm text-gray-900 dark:text-gray-100"
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
              <div>
                <p>Semester GPA: {{ calculateGPA(semester.grades, student.id) }}</p>
                <p>Cumulative GPA: {{ cumulativeGPAList[index]?.cumulativeGPA || '0.00' }}</p>
              </div>
            </td>
            <td class="px-4 py-2 text-green-700 dark:text-green-400">
              <div>
                <p>Total Points:
                  {{
                    semester.grades.filter(g => g.student_id === student.id)
                      .reduce((acc, g) => acc + getGradePointFromLetter(g.grade_point) * parseFloat(g.course?.credit_hours || 0), 0).toFixed(2)
                  }}
                </p>
                <p>Total Credits:
                  {{
                    semester.grades.filter(g => g.student_id === student.id)
                      .reduce((acc, g) => acc + parseFloat(g.course?.credit_hours || 0), 0)
                  }}
                </p>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
    <div v-else class="italic text-gray-500 dark:text-gray-400">No grades available for this semester.</div>
  </div>
</template>
