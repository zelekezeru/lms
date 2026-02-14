<script setup>
import { ref, computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";
import AppLayout from "@/Layouts/AppLayout.vue";

// Props
const props = defineProps({
    student: { type: Object, required: true },
    semesters: { type: Object, required: true },
});

// Sort semesters by year name and then by semester name
const sortedSemesters = computed(() => {
    const semesterOrder = {
        "First Semester": 1,
        "Second Semester": 2,
    };
    return [...props.semesters].sort((a, b) => {
        const yearA = a.year?.name || "";
        const yearB = b.year?.name || "";
        if (yearA < yearB) return -1;
        if (yearA > yearB) return 1;
        const semA = a.name || "";
        const semB = b.name || "";
        return (semesterOrder[semA] || 0) - (semesterOrder[semB] || 0);
    });
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

// GPA for a single semester
function calculateGPA(grades, studentId) {
    const filtered = (grades || []).filter((g) => g.student_id == studentId);
    let totalPoints = 0;
    let totalCredits = 0;
    filtered.forEach((g) => {
        const gp = getGradePointFromLetter(parseFloat(g.grade_point) || 0);
        const credit = parseFloat(g.course?.credit_hours || 0);
        totalPoints += gp * credit;
        totalCredits += credit;
    });
    return totalCredits > 0 ? (totalPoints / totalCredits).toFixed(2) : "0.00";
}

// Weighted Progressive Cumulative GPA
const cumulativeGPAList = computed(() => {
    let totalPoints = 0;
    let totalCredits = 0;
    // Iterate over the sorted semesters
    return sortedSemesters.value.map((semester, index) => {
        const grades = semester.grades.filter(
            (g) => g.student_id == props.student.id
        );
        grades.forEach((g) => {
            const gp = getGradePointFromLetter(parseFloat(g.grade_point) || 0);
            const credit = parseFloat(g.course?.credit_hours || 0);
            totalPoints += gp * credit;
            totalCredits += credit;
        });
        return {
            semesterIndex: index,
            cumulativeGPA:
                totalCredits > 0
                    ? (totalPoints / totalCredits).toFixed(2)
                    : "0.00",
        };
    });
});

async function exportPDF() {
    // ✅ 1. Initialize jsPDF FIRST
    const doc = new jsPDF({
        orientation: "landscape",
        unit: "mm",
        format: "a4",
    });

    // ✅ 2. Register or set font for Amharic text (if available)
    // If you’ve loaded “Nyala” or “NotoEthiopic” via jsPDF.addFileToVFS / addFont elsewhere,
    // make sure it’s available before calling this function
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

    const contentStartY = 40;
    let semesterCounter = 0;
    let columnLeftY = contentStartY;
    let columnRightY = contentStartY;

    // --- HELPER FUNCTION: Draw Header and Footer ---
    const drawPageBordersAndInfo = () => {
        const totalPages = doc.internal.getNumberOfPages();
        const currentPage = doc.internal.getCurrentPageInfo().pageNumber;

        // ✅ Logo (base64 or URL)
        // const logoBase64 = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABM4AAATUCAMAAACnGruaAAAACXBIWXMAAAsSAAALEgHS3X78AAADAFBMVEVHcEzaHyLXubRAAAAAElFTkSuQmCC";
        // doc.addImage(logoBase64, "PNG", marginLeft + 20, 5, 25, 25);

        // ✅ Header
        doc.setFontSize(16);
        doc.setFont(undefined, "bold");
        doc.text("Shiloh International Theological Seminary", pageWidth / 2, 12, {
            align: "center",
        });

        doc.setFont(undefined, "normal");
        doc.setFontSize(10);
        doc.text("Student Official Transcript Record", pageWidth / 2, 18, {
            align: "center",
        });

        // ✅ Student Info Block
        let infoY = 25;
        const infoCol1 = marginLeft + 50;
        const infoCol2 = marginLeft + innerWidth / 2 + 10;

        doc.setFontSize(8);
        doc.text(`Name: ${student.firstName} ${student.middleName} ${student.lastName}`, infoCol1, infoY);
        doc.text(`Program of Study: ${student.program?.name || "N/A"}`, infoCol2, infoY);
        infoY += 4;
        doc.text(`Student ID: ${student.idNo}`, infoCol1, infoY);
        doc.text(`Major: ${student.track?.name || "N/A"}`, infoCol2, infoY);
        infoY += 4;
        doc.text(`Birth Date: ${student.dateOfBirth || "N/A"}`, infoCol1, infoY);
        doc.text(`Graduation Date: ${student.graduationDate || "N/A"}`, infoCol2, infoY);
        infoY += 4;

        doc.setDrawColor(150);
        doc.line(marginLeft, infoY, pageWidth - marginRight, infoY);

        // ✅ Footer
        const footerY = pageHeight - 25;
        doc.setFontSize(7);
        doc.line(marginLeft, footerY - 5, pageWidth - marginRight, footerY - 5);

        doc.setFont(undefined, "bold");
        doc.text("Registrar's Signature: ______________________________", marginLeft, footerY);
        doc.setFont(undefined, "normal");

        const gradingScaleText =
            "Grading: A = 4.0, A- = 3.7, B+ = 3.3, B = 3.0, B- = 2.7, C+ = 2.3, C = 2.0, C- = 1.7, D+ = 1.3, D = 1.0, F = 0.0";
        doc.text(gradingScaleText, marginLeft, footerY + 6);

        doc.text(`Office of the Registrar`, pageWidth - 80, footerY, { align: "left" });
        doc.text(`P.O Box 1185, Hawassa, Ethiopia`, pageWidth - 80, footerY + 4, { align: "left" });
        doc.text(`Email: registrar@sits.edu.et`, pageWidth - 80, footerY + 8, { align: "left" });
        doc.text(
            `Date of Issue: ${new Date().toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric",
            })}`,
            pageWidth - 80,
            footerY + 12,
            { align: "left" }
        );

        doc.text(`Page ${currentPage} of ${totalPages}`, pageWidth - marginRight, pageHeight - 5, {
            align: "right",
        });
    };

    drawPageBordersAndInfo();

    // --- MAIN SEMESTER LOOP ---
    let totalCumulativePoints = 0;
    let totalCumulativeCredits = 0;

    semesters.forEach((semester) => {
        const grades = semester.grades.filter((g) => g.student_id == student.id);
        if (!grades.length) return;

        const isLeftColumn = semesterCounter % 2 === 0;
        const startX = isLeftColumn ? marginLeft : marginLeft + colWidth + 6;
        let currentY = isLeftColumn ? columnLeftY : columnRightY;

        // Semester calculations
        let semesterPoints = 0;
        let semesterCredits = 0;

        grades.forEach((g) => {
            const gp = getGradePointFromLetter(parseFloat(g.grade_point) || 0);
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

        // Semester Header
        doc.setFontSize(8);
        doc.setFont(undefined, "bold");
        doc.text(
            `${semester.year?.name ?? "Unknown Year"} - ${semester.name ?? "Unknown Semester"}`,
            startX,
            currentY
        );
        doc.setFont(undefined, "normal");
        currentY += 4;

        // Table
        const tableData = grades.map((g) => [
            g.course?.code || "N/A",
            g.course?.name || "N/A",
            `${g.course?.credit_hours || 0}`,
            (
                getGradePointFromLetter(parseFloat(g.grade_point) || 0) *
                parseFloat(g.course?.credit_hours || 0)
            ).toFixed(1),
            g.grade_letter || "N/A",
        ]);

        autoTable(doc, {
            head: [["Course", "Course Name", "CRD", "Points", "Grade"]],
            body: tableData,
            startY: currentY,
            theme: "grid",
            styles: { fontSize: 7, cellPadding: 0.5 },
            headStyles: { fillColor: [200, 200, 200], textColor: 0 },
            columnStyles: {
                0: { cellWidth: 15 },
                1: { cellWidth: colWidth - 56 },
                2: { cellWidth: 12, halign: "center" },
                3: { cellWidth: 15, halign: "right" },
                4: { cellWidth: 14, halign: "center" },
            },
            margin: { left: startX },
            didDrawPage: (data) => {
                currentY = data.cursor.y;
            },
        });

        currentY = doc.lastAutoTable.finalY + 1;

        // Semester Summary
        doc.setFontSize(7);
        doc.text(`Semester GPA: ${semGPA}`, startX, currentY + 3);
        doc.text(`Cumulative GPA: ${cumGPA}`, startX, currentY + 7);

        currentY += 10;

        // Track column
        if (isLeftColumn) columnLeftY = currentY;
        else columnRightY = currentY;

        semesterCounter++;

        // Page break
        const breakPoint = pageHeight - 60;
        if (semesterCounter % 2 === 0 && currentY > breakPoint) {
            doc.addPage();
            drawPageBordersAndInfo();
            columnLeftY = contentStartY;
            columnRightY = contentStartY;
        }
    });

    // ✅ Update page numbers on all pages
    const finalPageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= finalPageCount; i++) {
        doc.setPage(i);
        doc.text(`Page ${i} of ${finalPageCount}`, pageWidth - marginRight, pageHeight - 5, {
            align: "right",
        });
    }

    // ✅ Download
    doc.save(`${student.firstName}_${student.middleName}_${student.lastName}_Transcript.pdf`);
}


const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};
</script>

<template>
    
    <!-- student Image -->
    <div class="flex flex-col items-center mb-8 relative">
        
        <img class="rounded-full w-44 h-44 object-contain bg-gray-400" src="/img/logo.png" alt="School Logo"/>
        
    </div>


    <Head title="Student Transcript" />
    <h1 class="text-2xl font-bold mb-2 text-center">
        <div>Shiloh International Theological Seminary</div>
        <div class="text-base font-normal text-gray-700 dark:text-gray-300">
            Student Official Transcript
        </div>
    </h1>

    <Link
        as="button"
        @click="exportPDF"
        class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-yellow-700"
    >
        Export PDF
    </Link>

    <div
        class="mb-4 text-sm text-gray-600 dark:text-gray-300 grid sm:grid-cols-2 gap-x-8 gap-y-1"
    >
        <p>
            <strong>Student:</strong> {{ student.firstName }}
            {{ student.middleName }} {{ student.lastName }}
        </p>
        <p><strong>ID:</strong> {{ student.idNo }}</p>
        <p><strong>Program:</strong> {{ student.program?.name }}</p>
        <p><strong>Track:</strong> {{ student.track?.name }}</p>
        <p><strong>Class of:</strong> {{ student.year?.name }}</p>
        <p><strong>Semester:</strong> {{ student.semester?.name }}</p>
        <p><strong>Study Mode:</strong> {{ student.studyMode?.name }}</p>
    </div>

    <div v-for="(semester, index) in sortedSemesters" :key="index" class="mb-8">
        <h2
            class="text-lg font-semibold text-green-700 dark:text-green-400 mb-2"
        >
            {{ semester.year?.name ?? "Unknown Year" }} -
            {{ semester.name ?? "Unknown Semester" }}
        </h2>

        <div v-if="semester.grades.length > 0">
            <table
                class="min-w-full border rounded bg-white dark:bg-gray-800 shadow"
            >
                <thead
                    class="bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-800 dark:text-gray-200"
                >
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
                        v-for="(grade, i) in semester.grades.filter(
                            (g) => g.student_id == student.id
                        )"
                        :key="grade.id"
                        :class="
                            i % 2 == 0
                                ? 'bg-white dark:bg-gray-800'
                                : 'bg-gray-50 dark:bg-gray-700'
                        "
                        class="text-sm text-gray-900 dark:text-gray-100"
                    >
                        <td class="px-4 py-2">{{ grade.course?.code }}</td>
                        <td class="px-4 py-2">{{ grade.course?.name }}</td>
                        <td class="px-4 py-2">
                            {{ grade.course?.credit_hours }}
                        </td>
                        <td class="px-4 py-2">{{ grade.grade_letter }}</td>
                        <td class="px-4 py-2">{{ grade.grade_point }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr
                        class="bg-gray-50 dark:bg-gray-800 text-sm font-semibold"
                    >
                        <td class="px-4 py-2 text-right" colspan="3"></td>
                        <td
                            class="px-4 py-2 text-green-700 dark:text-green-400"
                        >
                            <div>
                                <p>
                                    Semester GPA:
                                    {{
                                        calculateGPA(
                                            semester.grades,
                                            student.id
                                        )
                                    }}
                                </p>
                                <p>
                                    Cumulative GPA:
                                    {{
                                        cumulativeGPAList[index]
                                            ?.cumulativeGPA || "0.00"
                                    }}
                                </p>
                            </div>
                        </td>
                        <td
                            class="px-4 py-2 text-green-700 dark:text-green-400"
                        >
                            <div>
                                <p>
                                    Total Points:
                                    {{
                                        semester.grades
                                            .filter(
                                                (g) =>
                                                    g.student_id == student.id
                                            )
                                            .reduce(
                                                (acc, g) =>
                                                    acc +
                                                    getGradePointFromLetter(
                                                        parseFloat(
                                                            g.grade_point
                                                        ) || 0
                                                    ) *
                                                        parseFloat(
                                                            g.course
                                                                ?.credit_hours ||
                                                                0
                                                        ),
                                                0
                                            )
                                            .toFixed(2)
                                    }}
                                </p>
                                <p>
                                    Total Credits:
                                    {{
                                        semester.grades
                                            .filter(
                                                (g) =>
                                                    g.student_id == student.id
                                            )
                                            .reduce(
                                                (acc, g) =>
                                                    acc +
                                                    parseFloat(
                                                        g.course
                                                            ?.credit_hours || 0
                                                    ),
                                                0
                                            )
                                    }}
                                </p>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div v-else class="italic text-gray-500 dark:text-gray-400">
            No grades available for this semester.
        </div>
    </div>
</template>
