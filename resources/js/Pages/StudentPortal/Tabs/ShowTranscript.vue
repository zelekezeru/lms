<script setup>
import { ref, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";
import {
    ChevronDownIcon,
    DocumentArrowDownIcon,
    AcademicCapIcon,
    ClipboardDocumentListIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    student: { type: Object, required: true },
    semesters: { type: Object, required: true },
});

const openSemester = ref(null);

const semesterOrder = { "First Semester": 1, "Second Semester": 2 };
const sortedSemesters = computed(() =>
    [...props.semesters].sort((a, b) => {
        const ya = a.year?.name ?? "";
        const yb = b.year?.name ?? "";
        if (ya < yb) return -1;
        if (ya > yb) return 1;
        return (semesterOrder[a.name] ?? 99) - (semesterOrder[b.name] ?? 99);
    })
);

function getGradePoint(point) {
    const p = parseFloat(point);
    if (isNaN(p)) return 0;
    if (p >= 94) return 4.0;
    if (p >= 90) return 3.7;
    if (p >= 87) return 3.3;
    if (p >= 84) return 3.0;
    if (p >= 80) return 2.7;
    if (p >= 77) return 2.3;
    if (p >= 74) return 2.0;
    if (p >= 70) return 1.7;
    if (p >= 67) return 1.3;
    if (p >= 64) return 1.0;
    if (p >= 60) return 0.7;
    return 0.0;
}

function calcGPA(grades, studentId) {
    const g = grades.filter((g) => g.student_id == studentId);
    let pts = 0, creds = 0;
    g.forEach((gr) => {
        pts += getGradePoint(parseFloat(gr.grade_point) || 0) * parseFloat(gr.course?.credit_hours || 0);
        creds += parseFloat(gr.course?.credit_hours || 0);
    });
    return creds > 0 ? (pts / creds).toFixed(2) : "0.00";
}

const cumulativeGPAs = computed(() => {
    let pts = 0, creds = 0;
    return sortedSemesters.value.map((sem) => {
        sem.grades.filter((g) => g.student_id == props.student.id).forEach((g) => {
            pts += getGradePoint(parseFloat(g.grade_point) || 0) * parseFloat(g.course?.credit_hours || 0);
            creds += parseFloat(g.course?.credit_hours || 0);
        });
        return creds > 0 ? (pts / creds).toFixed(2) : "0.00";
    });
});

const gpaColor = (gpa) => {
    const n = parseFloat(gpa);
    if (n >= 3.5) return "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400";
    if (n >= 2.5) return "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400";
    if (n >= 1.5) return "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400";
    return "bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400";
};

const gradeLetterColor = (letter) => {
    if (!letter) return "text-gray-400";
    const l = letter.toUpperCase();
    if (l === "A" || l === "A+") return "text-emerald-600 dark:text-emerald-400 font-bold";
    if (l.startsWith("A")) return "text-green-600 dark:text-green-400 font-bold";
    if (l.startsWith("B")) return "text-blue-600 dark:text-blue-400 font-semibold";
    if (l.startsWith("C")) return "text-amber-600 dark:text-amber-400 font-semibold";
    return "text-rose-600 dark:text-rose-400 font-semibold";
};

function exportPDF() {
    const doc = new jsPDF({ orientation: "landscape", unit: "mm", format: "a4" });
    const student = props.student;
    const semesters = sortedSemesters.value;
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const marginLeft = 14, marginRight = 10;
    const innerWidth = pageWidth - marginLeft - marginRight;
    const contentStartY = 60;
    let y = contentStartY;
    let totalCumulativePoints = 0, totalCumulativeCredits = 0;

    const drawHeader = () => {
        doc.setFont("times", "normal");
        doc.setFontSize(14);
        doc.text("Shiloh International Theological Seminary", pageWidth / 2, 10, { align: "center" });
        doc.setFontSize(10);
        doc.text("Student Official Transcript Record", pageWidth / 2, 15, { align: "center" });
        doc.setDrawColor(150);
        doc.line(marginLeft, 17, pageWidth - marginRight, 17);
        let infoY = 22;
        doc.setFontSize(9);
        const col1 = marginLeft, col2 = marginLeft + innerWidth / 2 + 5;
        doc.text(`Name: ${student.firstName} ${student.middleName} ${student.lastName}`, col1, infoY);
        doc.text(`Program: ${student.program?.name ?? "N/A"}`, col2, infoY); infoY += 5;
        doc.text(`ID: ${student.idNo}`, col1, infoY);
        doc.text(`Track: ${student.track?.name ?? "N/A"}`, col2, infoY); infoY += 5;
        doc.text(`Study Mode: ${student.studyMode?.name ?? "N/A"}`, col2, infoY);
        doc.line(marginLeft, infoY + 2, pageWidth - marginRight, infoY + 2);
        const footerY = pageHeight - 20;
        doc.setFontSize(7);
        doc.line(marginLeft, footerY - 2, pageWidth - marginRight, footerY - 2);
        doc.text("Grading Scale: A=4.0, A-=3.7, B+=3.3, B=3.0, B-=2.7, C+=2.3, C=2.0, C-=1.7, D+=1.3, D=1.0, F=0.0", marginLeft, footerY);
        doc.text("Registrar Signature: ____________________", marginLeft, footerY + 5);
        doc.text(`Date: ${new Date().toLocaleDateString()}`, pageWidth - marginRight, footerY + 5, { align: "right" });
    };

    drawHeader();

    semesters.forEach((semester) => {
        const grades = semester.grades.filter((g) => g.student_id == student.id);
        if (!grades.length) return;
        let semPts = 0, semCreds = 0;
        grades.forEach((g) => {
            const gp = getGradePoint(parseFloat(g.grade_point) || 0);
            const cr = parseFloat(g.course?.credit_hours || 0);
            semPts += gp * cr; semCreds += cr;
            totalCumulativePoints += gp * cr; totalCumulativeCredits += cr;
        });
        const semGPA = semCreds > 0 ? (semPts / semCreds).toFixed(2) : "0.00";
        const cumGPA = totalCumulativeCredits > 0 ? (totalCumulativePoints / totalCumulativeCredits).toFixed(2) : "0.00";
        if (y > pageHeight - 50) { doc.addPage(); drawHeader(); y = contentStartY; }
        doc.setFontSize(10); doc.setFont("times", "bold");
        doc.text(`${semester.year?.name ?? "Unknown Year"} — ${semester.name ?? "Unknown Semester"}`, marginLeft, y);
        doc.setFont("times", "normal"); y += 4;
        const tableData = grades.map((g) => [
            g.course?.code ?? "N/A", g.course?.name ?? "N/A", `${g.course?.credit_hours ?? 0}`, g.grade_letter ?? "N/A",
            (getGradePoint(parseFloat(g.grade_point) || 0) * parseFloat(g.course?.credit_hours || 0)).toFixed(2),
        ]);
        autoTable(doc, {
            head: [["Code", "Course Name", "CR", "Grade", "Points"]],
            body: tableData, startY: y, theme: "grid",
            styles: { fontSize: 8, cellPadding: 1 },
            headStyles: { fillColor: [79, 70, 229], textColor: 255, fontSize: 9 },
            columnStyles: { 0: { cellWidth: 20 }, 1: { cellWidth: innerWidth - 75 }, 2: { cellWidth: 15, halign: "center" }, 3: { cellWidth: 20, halign: "center" }, 4: { cellWidth: 20, halign: "right" } },
            margin: { left: marginLeft, right: marginRight },
            didDrawPage: () => { y = doc.lastAutoTable.finalY; },
        });
        y = doc.lastAutoTable.finalY + 4;
        doc.setFontSize(8); doc.setFont("times", "bold");
        doc.text(`Sem GPA: ${semGPA}   Cum GPA: ${cumGPA}   Credits: ${semCreds}`, marginLeft, y);
        y += 10;
    });

    const pages = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pages; i++) {
        doc.setPage(i); doc.setFontSize(7);
        doc.text(`Page ${i} of ${pages}`, pageWidth - marginRight, pageHeight - 5, { align: "right" });
    }
    doc.save(`${student.firstName}_${student.lastName}_Transcript.pdf`);
}
</script>

<template>
    <AppLayout>
        <Head title="Transcript" />
        <div class="pb-24 md:pb-8 max-w-3xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Header -->
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                        <ClipboardDocumentListIcon class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Transcript</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Official academic record</p>
                    </div>
                </div>
                <button
                    @click="exportPDF"
                    class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm transition active:scale-95"
                >
                    <DocumentArrowDownIcon class="w-4 h-4" />
                    <span class="hidden sm:inline">Export PDF</span>
                </button>
            </div>

            <!-- Student info banner -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    <div>
                        <p class="text-gray-400 mb-0.5">Student</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ student.firstName }} {{ student.middleName }} {{ student.lastName }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 mb-0.5">ID</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ student.idNo }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 mb-0.5">Program</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ student.program?.name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 mb-0.5">Track</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ student.track?.name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 mb-0.5">Study Mode</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ student.studyMode?.name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 mb-0.5">Class of</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ student.year?.name ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Semester accordion -->
            <div class="space-y-3">
                <div
                    v-for="(semester, idx) in sortedSemesters"
                    :key="idx"
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden"
                >
                    <!-- Semester header -->
                    <button
                        @click="openSemester = openSemester === idx ? null : idx"
                        class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                    >
                        <div class="flex items-center gap-3">
                            <AcademicCapIcon class="w-4 h-4 text-indigo-500 shrink-0" />
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ semester.year?.name ?? 'Unknown Year' }} — {{ semester.name }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ semester.grades.filter(g => g.student_id == student.id).length }} courses
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <span :class="['text-xs font-bold px-2.5 py-1 rounded-lg mr-2', gpaColor(calcGPA(semester.grades, student.id))]">
                                    GPA {{ calcGPA(semester.grades, student.id) }}
                                </span>
                                <span :class="['text-xs font-bold px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400']">
                                    CUM {{ cumulativeGPAs[idx] }}
                                </span>
                            </div>
                            <ChevronDownIcon
                                :class="['w-4 h-4 text-gray-400 transition-transform duration-200', openSemester === idx ? 'rotate-180' : '']"
                            />
                        </div>
                    </button>

                    <!-- GPA badges on mobile (below header) -->
                    <div v-if="openSemester !== idx" class="sm:hidden px-5 pb-3 flex gap-2">
                        <span :class="['text-xs font-bold px-2.5 py-1 rounded-lg', gpaColor(calcGPA(semester.grades, student.id))]">
                            GPA {{ calcGPA(semester.grades, student.id) }}
                        </span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                            CUM {{ cumulativeGPAs[idx] }}
                        </span>
                    </div>

                    <!-- Courses table -->
                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition duration-100"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="openSemester === idx" class="border-t border-gray-100 dark:border-gray-700">
                            <div
                                v-if="semester.grades.filter(g => g.student_id == student.id).length"
                                class="overflow-x-auto"
                            >
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Course</th>
                                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">CR</th>
                                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade</th>
                                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Points</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <tr
                                            v-for="grade in semester.grades.filter(g => g.student_id == student.id)"
                                            :key="grade.id"
                                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                                        >
                                            <td class="px-4 py-3">
                                                <p class="font-medium text-gray-900 dark:text-white text-sm line-clamp-1">{{ grade.course?.name }}</p>
                                                <p class="text-xs text-gray-400">{{ grade.course?.code }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400 font-medium">
                                                {{ grade.course?.credit_hours }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span :class="gradeLetterColor(grade.grade_letter)">{{ grade.grade_letter ?? '—' }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">
                                                {{ (getGradePoint(parseFloat(grade.grade_point) || 0) * parseFloat(grade.course?.credit_hours || 0)).toFixed(2) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                    <!-- Semester totals -->
                                    <tfoot class="border-t-2 border-gray-200 dark:border-gray-600">
                                        <tr class="bg-gray-50 dark:bg-gray-700/30">
                                            <td class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300" colspan="2">
                                                Semester Totals
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span :class="['text-xs font-bold px-2 py-0.5 rounded-lg', gpaColor(calcGPA(semester.grades, student.id))]">
                                                    GPA {{ calcGPA(semester.grades, student.id) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm font-bold text-gray-900 dark:text-white">
                                                {{
                                                    semester.grades.filter(g => g.student_id == student.id)
                                                        .reduce((a, g) => a + getGradePoint(parseFloat(g.grade_point) || 0) * parseFloat(g.course?.credit_hours || 0), 0)
                                                        .toFixed(2)
                                                }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <p v-else class="px-5 py-4 text-sm text-gray-400 italic">No grades recorded for this semester.</p>
                        </div>
                    </transition>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
