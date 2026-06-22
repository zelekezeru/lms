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

async function exportPDF() {
    // Load the logo
    let logoBase64 = null;
    try {
        const logoResponse = await fetch('/img/logo.png');
        const logoBlob = await logoResponse.blob();
        logoBase64 = await new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.readAsDataURL(logoBlob);
        });
    } catch (e) {
        console.warn('Failed to load logo:', e);
    }

    // Initialize jsPDF (Portrait)
    const doc = new jsPDF({
        orientation: "portrait",
        unit: "mm",
        format: "a4",
    });

    try {
        doc.setFont("nyala", "normal");
    } catch (e) {
        doc.setFont("times", "normal"); // fallback
    }

    const student = props.student;
    const semesters = sortedSemesters.value;
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();

    // Margins and layout
    const marginLeft = 10;
    const marginRight = 10;
    const innerWidth = pageWidth - marginLeft - marginRight;
    const colWidth = innerWidth / 2 - 3;

    const contentStartY = 47;
    let semesterCounter = 0;
    let columnLeftY = contentStartY;
    let columnRightY = contentStartY;

    // --- HELPER FUNCTION: Draw Header and Footer ---
    const drawPageBordersAndInfo = () => {
        const totalPages = doc.internal.getNumberOfPages();
        const currentPage = doc.internal.getCurrentPageInfo().pageNumber;

        // ✅ Logo
        if (logoBase64) {
            doc.addImage(logoBase64, "PNG", marginLeft + 5, 5, 18, 18);
        }

        // ✅ Header
        doc.setFontSize(14);
        doc.setFont(undefined, "bold");
        doc.text("Shiloh International Theological Seminary", pageWidth / 2, 12, {
            align: "center",
        });

        doc.setFont(undefined, "bold");
        doc.setFontSize(11);
        doc.text("Student Record", pageWidth / 2, 17, {
            align: "center",
        });

        // ✅ Student Info Block
        let infoY = 22;
        const infoCol2 = marginLeft + innerWidth / 2 + 5;

        doc.setFontSize(8);
        
        // Left Column
        doc.setFont(undefined, "bold");
        doc.text("Name:", marginLeft + 5, infoY);
        doc.setFont(undefined, "normal");
        doc.text(`${student.firstName} ${student.middleName} ${student.lastName}`, marginLeft + 25, infoY);
        
        doc.setFont(undefined, "bold");
        doc.text("Student ID:", marginLeft + 5, infoY + 4);
        doc.setFont(undefined, "normal");
        doc.text(`${student.idNo}`, marginLeft + 25, infoY + 4);
        
        doc.setFont(undefined, "bold");
        doc.text("Sex:", marginLeft + 5, infoY + 8);
        doc.setFont(undefined, "normal");
        doc.text(`${student.sex || ""}`, marginLeft + 25, infoY + 8);
        
        doc.setFont(undefined, "bold");
        doc.text("Birth Date:", marginLeft + 5, infoY + 12);
        doc.setFont(undefined, "normal");
        doc.text(`${student.dateOfBirth || ""}`, marginLeft + 25, infoY + 12);

        // Right Column
        doc.setFont(undefined, "bold");
        doc.text("Program of Study:", infoCol2, infoY);
        doc.setFont(undefined, "normal");
        doc.text(`${student.program?.name || "N/A"}`, infoCol2 + 30, infoY);
        
        doc.setFont(undefined, "bold");
        doc.text("Major:", infoCol2, infoY + 4);
        doc.setFont(undefined, "normal");
        doc.text(`${student.track?.name || "N/A"}`, infoCol2 + 30, infoY + 4);
        
        doc.setFont(undefined, "bold");
        doc.text("Graduation Date:", infoCol2, infoY + 8);
        doc.setFont(undefined, "normal");
        doc.text(`${student.graduationDate || "N/A"}`, infoCol2 + 30, infoY + 8);

        // Top line
        doc.setDrawColor(0);
        doc.setLineWidth(0.15);
        doc.line(marginLeft, infoY + 16, pageWidth - marginRight, infoY + 16);

        // Center dividing line
        doc.line(pageWidth / 2, infoY + 16, pageWidth / 2, pageHeight - 30);

        // ✅ Footer
        const footerY = pageHeight - 25;
        doc.line(marginLeft, footerY - 5, pageWidth - marginRight, footerY - 5);

        doc.setFont(undefined, "bold");
        doc.setFontSize(6.5);
        doc.text("Registrar's Signature: ______________________________", marginLeft, footerY);
        doc.setFont(undefined, "normal");

        doc.text(
            "This record is valid only with the registrar's signature and the Shiloh International Theological Seminary Grade Points: A = 4.0, A- = 3.7, B+ = 3.3, B = 3.0,",
            marginLeft,
            footerY + 4
        );
        doc.text(
            "B- = 2.7, C+ = 2.3, C = 2.0, C- = 1.7, D+ = 1.3, D = 1.0, F = 0.0",
            marginLeft,
            footerY + 7
        );
        doc.text(
            "Grading: A = excellent, B = above average, C = satisfactory, D = less than average, F = failing, WP = withdraw pass, WF = withdraw failing, I = incomplete",
            marginLeft,
            footerY + 10
        );

        doc.setFontSize(7);
        doc.text(`Office of the Registrar`, pageWidth - 80, footerY, { align: "left" });
        doc.text(`P.O Box 1185, Hawassa, Ethiopia`, pageWidth - 80, footerY + 3, { align: "left" });
        doc.text(`Email: registrar@sits.edu.et`, pageWidth - 80, footerY + 6, { align: "left" });
        doc.text(
            `Date of Issue: ${new Date().toLocaleDateString("en-US", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
            })}`,
            pageWidth - 80,
            footerY + 9,
            { align: "left" }
        );
    };

    drawPageBordersAndInfo();

    // --- MAIN SEMESTER LOOP ---
    let totalCumulativePoints = 0;
    let totalCumulativeCredits = 0;

    // Draw Transfer Credits table if available
    if (student.transferCredits && student.transferCredits !== "N/A" && parseFloat(student.transferCredits) > 0) {
        doc.setFontSize(8);
        doc.setFont(undefined, "bold");
        doc.text("Credits Transferred from Other Schools", marginLeft, columnLeftY);
        doc.setFont(undefined, "normal");
        columnLeftY += 4;

        // Line above table
        doc.setDrawColor(0);
        doc.setLineWidth(0.1);
        doc.line(marginLeft, columnLeftY, marginLeft + colWidth, columnLeftY);

        autoTable(doc, {
            head: [["Course", "Course Name", "Credits", "Grade", "Points"]],
            body: [["", "", `${student.transferCredits}`, "--", "--"]],
            startY: columnLeftY,
            theme: "horizontal",
            styles: { fontSize: 7, cellPadding: 0.5, lineColor: [0, 0, 0], lineWidth: 0.1 },
            headStyles: { fillColor: false, textColor: 0, fontStyle: "bold" },
            columnStyles: {
                0: { cellWidth: 12 },
                1: { cellWidth: colWidth - 42 },
                2: { cellWidth: 10, halign: "center" },
                3: { cellWidth: 10, halign: "center" },
                4: { cellWidth: 10, halign: "right" },
            },
            margin: { left: marginLeft },
            didDrawPage: (data) => {
                columnLeftY = data.cursor.y;
            },
        });
        
        columnLeftY = doc.lastAutoTable.finalY;
        doc.line(marginLeft, columnLeftY, marginLeft + colWidth, columnLeftY);
        columnLeftY += 8; // Spacing after transfer credits
    }

    semesters.forEach((semester) => {
        const grades = semester.grades.filter((g) => g.student_id == student.id);
        if (!grades.length) return;

        // Semester calculations
        let semesterPoints = 0;
        let semesterCredits = 0;

        grades.forEach((g) => {
            const gp = getGradePoint(parseFloat(g.grade_point) || 0);
            const credit = parseFloat(g.course?.credit_hours || 0);
            semesterPoints += gp * credit;
            semesterCredits += credit;
        });

        const semGPA = semesterCredits > 0 ? (semesterPoints / semesterCredits).toFixed(2) : "0.00";
        totalCumulativePoints += semesterPoints;
        totalCumulativeCredits += semesterCredits;
        const cumGPA =
            totalCumulativeCredits > 0
                ? (totalCumulativePoints / totalCumulativeCredits).toFixed(2)
                : "0.00";

        const tableData = grades.map((g) => [
            g.course?.code || "N/A",
            g.course?.name || "N/A",
            `${g.course?.credit_hours || 0}`,
            g.grade_letter || "N/A",
            (
                getGradePoint(parseFloat(g.grade_point) || 0) *
                parseFloat(g.course?.credit_hours || 0)
            ).toFixed(1),
        ]);

        const cellHeight = 4.2;
        const estimatedHeight = 4 + 2 + (tableData.length + 1) * cellHeight + 10 + 6;
        const limitY = pageHeight - 30;

        const isLeftColumn = semesterCounter % 2 === 0;
        let startX = isLeftColumn ? marginLeft : marginLeft + colWidth + 6;
        let currentY = isLeftColumn ? columnLeftY : columnRightY;

        if (currentY + estimatedHeight > limitY) {
            doc.addPage();
            drawPageBordersAndInfo();
            columnLeftY = contentStartY;
            columnRightY = contentStartY;
            semesterCounter = 0; // reset to left column on new page
            startX = marginLeft;
            currentY = contentStartY;
        }

        // Semester Header
        doc.setFontSize(8);
        doc.setFont(undefined, "bold");
        const semesterTitle = semester.year?.name
            ? `${semester.year.name} ${semester.name ?? ""}`
            : `${semester.name ?? "Unknown Semester"}`;
        doc.text(semesterTitle, startX, currentY);
        doc.setFont(undefined, "normal");
        currentY += 4;

        // Line above table
        doc.setDrawColor(0);
        doc.setLineWidth(0.1);
        doc.line(startX, currentY, startX + colWidth, currentY);

        autoTable(doc, {
            head: [["Course", "Course Name", "Credits", "Grade", "Points"]],
            body: tableData,
            startY: currentY,
            theme: "horizontal",
            styles: { fontSize: 7, cellPadding: 0.5, lineColor: [0, 0, 0], lineWidth: 0.1 },
            headStyles: { fillColor: false, textColor: 0, fontStyle: "bold" },
            columnStyles: {
                0: { cellWidth: 12 },
                1: { cellWidth: colWidth - 42 },
                2: { cellWidth: 10, halign: "center" },
                3: { cellWidth: 10, halign: "center" },
                4: { cellWidth: 10, halign: "right" },
            },
            margin: { left: startX },
            didDrawPage: (data) => {
                currentY = data.cursor.y;
            },
        });

        currentY = doc.lastAutoTable.finalY;

        // Draw Semester GPA and Sem Totals
        doc.setFontSize(7);
        doc.setFont(undefined, "normal");
        doc.text(`Semester GPA: ${semGPA}`, startX, currentY + 4);
        doc.text(`Sem Totals`, startX + colWidth - 34, currentY + 4, { align: "right" });
        doc.text(`${semesterCredits}`, startX + colWidth - 25, currentY + 4, { align: "center" });
        doc.text(`--`, startX + colWidth - 15, currentY + 4, { align: "center" });
        doc.text(`${semesterPoints.toFixed(1)}`, startX + colWidth, currentY + 4, { align: "right" });

        // Draw Cumulative GPA and Cum Totals
        doc.text(`Cumulative GPA: ${cumGPA}`, startX, currentY + 8);
        doc.text(`Cum Totals`, startX + colWidth - 34, currentY + 8, { align: "right" });
        doc.text(`${totalCumulativeCredits}`, startX + colWidth - 25, currentY + 8, { align: "center" });
        doc.text(`--`, startX + colWidth - 15, currentY + 8, { align: "center" });
        doc.text(`${totalCumulativePoints.toFixed(1)}`, startX + colWidth, currentY + 8, { align: "right" });

        // Draw line below Cum Totals
        doc.setDrawColor(0);
        doc.setLineWidth(0.1);
        doc.line(startX, currentY + 10, startX + colWidth, currentY + 10);

        currentY += 16;

        // Track column
        if (semesterCounter % 2 === 0) columnLeftY = currentY;
        else columnRightY = currentY;

        semesterCounter++;
    });

    // ✅ Update page numbers on all pages
    const finalPageCount = doc.internal.getNumberOfPages();
    const footerY = pageHeight - 25;
    for (let i = 1; i <= finalPageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(7);
        doc.setFont(undefined, "normal");
        doc.text(`Page ${i} of ${finalPageCount}`, pageWidth - 80, footerY + 12, {
            align: "left",
        });
    }

    // ✅ Download
    doc.save(`${student.firstName}_${student.middleName}_${student.lastName}_Transcript.pdf`);
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
