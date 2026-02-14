<script setup>
import { ref, computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";
import AppLayout from "@/Layouts/AppLayout.vue";

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
        return semesterOrder[semA] - semesterOrder[semB];
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
    const filtered = grades.filter((g) => g.student_id == studentId);
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

// PDF Export
function exportPDF() {
    // 1. Initialize PDF in landscape mode for better multi-semester flow
    const doc = new jsPDF({
        orientation: "landscape", 
        unit: "mm",
        format: "a4",
    });
    const student = props.student;
    const semesters = sortedSemeters.value;
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const marginLeft = 14;
    const marginRight = 10;
    const innerWidth = pageWidth - marginLeft - marginRight; // ~186mm

    // Fixed vertical starting point for content after header
    const contentStartY = 60; 
    let y = contentStartY; 

    // Cumulative trackers (outside the loop)
    let totalCumulativePoints = 0;
    let totalCumulativeCredits = 0;

    // --- HELPER FUNCTION: Draw Header and Footer ---
    const drawPageBordersAndInfo = () => {
        doc.setFont('times', 'normal');
        
        // 1. HEADER
        doc.setFontSize(14);
        doc.text("Shiloh International Theological Seminary", pageWidth / 2, 10, {
            align: "center",
        });
        doc.setFontSize(10);
        doc.text("Student Official Transcript Record", pageWidth / 2, 15, {
            align: "center",
        });
        doc.setDrawColor(150);
        doc.line(marginLeft, 17, pageWidth - marginRight, 17);

        // 2. STUDENT INFO BLOCK 
        let infoY = 22;
        doc.setFontSize(9);
        const infoCol1 = marginLeft;
        const infoCol2 = marginLeft + (innerWidth / 2) + 5; // Second column offset

        doc.text(`Name: ${student.firstName} ${student.middleName} ${student.lastName}`, infoCol1, infoY);
        doc.text(`Program of Study: ${student.program?.name || "N/A"}`, infoCol2, infoY);
        infoY += 5;
        
        doc.text(`Student ID: ${student.idNo}`, infoCol1, infoY);
        doc.text(`Major: ${student.track?.name || "N/A"}`, infoCol2, infoY);
        infoY += 5;
        
        doc.text(`Birth Date: ${student.dateOfBirth || "N/A"}`, infoCol1, infoY);
        doc.text(`Study Mode: ${student.studyMode?.name || "N/A"}`, infoCol2, infoY);
        infoY += 5;
        
        doc.text(`Class of: ${student.year?.name || "N/A"}`, infoCol1, infoY);
        doc.text(`Graduation Date: ${student.graduationDate || "N/A"}`, infoCol2, infoY);
        infoY += 5;

        doc.setDrawColor(150);
        doc.line(marginLeft, infoY, pageWidth - marginRight, infoY); // Separator line
        
        // 3. FOOTER
        const footerY = pageHeight - 20;
        doc.setFontSize(7);
        doc.line(marginLeft, footerY - 2, pageWidth - marginRight, footerY - 2);
        
        // Grading Scale Block
        const gradingScaleText = `Grading Scale: A = 4.0, A- = 3.7, B+ = 3.3, B = 3.0, B- = 2.7, C+ = 2.3, C = 2.0, C- = 1.7, D+ = 1.3, D = 1.0, F = 0.0`;
        doc.text(gradingScaleText, marginLeft, footerY);
        
        // Signature Block
        doc.text("Registrar Signature: ____________________", marginLeft, footerY + 5);
        
        // Date and Page Count
        const dateText = `Date: ${new Date().toLocaleDateString()}`;
        doc.text(dateText, pageWidth - marginRight, footerY + 5, { align: 'right' }); 
        
        const totalPages = doc.internal.getNumberOfPages();
        const currentPage = doc.internal.getCurrentPageInfo().pageNumber;
        doc.text(`Page ${currentPage} of ${totalPages}`, pageWidth - marginRight, pageHeight - 5, { align: 'right' });
    };

    // Initial Header/Footer draw
    drawPageBordersAndInfo();

    // --- MAIN SEMESTER LOOP ---
    semesters.forEach((semester, index) => {
        const grades = semester.grades.filter((g) => g.student_id == student.id);
        if (grades.length === 0) return;

        // 1. Calculate Semester Statistics
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
        const cumGPA = totalCumulativeCredits > 0 ? (totalCumulativePoints / totalCumulativeCredits).toFixed(2) : "0.00";

        // Check for page break BEFORE drawing the next semester block
        if (y > pageHeight - 50) { 
            doc.addPage();
            drawPageBordersAndInfo();
            y = contentStartY;
        }

        // 2. Add Semester Title
        doc.setFontSize(10);
        doc.setFont('times', 'bold');
        doc.text(
            `${semester.year?.name ?? "Unknown Year"} - ${semester.name ?? "Unknown Semester"}`,
            marginLeft,
            y
        );
        doc.setFont('times', 'normal');
        y += 4;

        // 3. AutoTable for Courses
        const tableData = grades.map((g) => [
            g.course?.code || "N/A",
            g.course?.name || "N/A",
            `${g.course?.credit_hours || 0}`,
            g.grade_letter || "N/A",
            (
                getGradePointFromLetter(parseFloat(g.grade_point) || 0) *
                parseFloat(g.course?.credit_hours || 0)
            ).toFixed(2),
        ]);

        autoTable(doc, {
            head: [["Course", "Course Name", "CRD", "Grade", "Points"]],
            body: tableData,
            startY: y,
            theme: "grid",
            styles: { fontSize: 8, cellPadding: 1 }, 
            headStyles: { fillColor: [41, 128, 185], textColor: 255, fontSize: 9 },
            columnStyles: {
                0: { cellWidth: 20 },      // Course Code
                1: { cellWidth: innerWidth - 75 }, // Course Name takes most space
                2: { cellWidth: 15, halign: 'center' }, // Credit
                3: { cellWidth: 20, halign: 'center' }, // Grade Letter
                4: { cellWidth: 20, halign: 'right' },  // Points
            },
            margin: { left: marginLeft, right: marginRight },
            
            // Hook to add summary rows directly below the table
            didDrawCell: (data) => {
                if (data.section === 'body' && data.row.index === data.table.body.length - 1 && data.column.index === 4) {
                    y = doc.lastAutoTable.finalY;

                    doc.setFontSize(8);
                    doc.setFont('times', 'bold');
                    
                    // Column positions (must match columnStyles above)
                    const colCreditEnd = marginLeft + 20 + (innerWidth - 75) + 15; // Course + Course Name + Credit Width
                    const colPointsEnd = colCreditEnd + 20; // + Points Width

                    // Row 1: Semester Totals
                    doc.text(`Semester Total:`, colCreditEnd - 30, y + 4, { align: 'right' }); 
                    doc.text(`${semesterCredits.toFixed(0)}`, colCreditEnd, y + 4, { align: 'right' }); 
                    doc.text(`${semesterPoints.toFixed(2)}`, colPointsEnd, y + 4, { align: 'right' }); 
                    
                    // Row 2: Cumulative Totals
                    doc.text(`Cumulative Total:`, colCreditEnd - 30, y + 8, { align: 'right' }); 
                    doc.text(`${totalCumulativeCredits.toFixed(0)}`, colCreditEnd, y + 8, { align: 'right' }); 
                    doc.text(`${totalCumulativePoints.toFixed(2)}`, colPointsEnd, y + 8, { align: 'right' }); 
                }
            },
            
            // Hook to add GPA information below the summary totals
            didDrawPage: (data) => {
                y = doc.lastAutoTable.finalY; // Update Y position after the last table is drawn
                
                // Only draw GPA if table was drawn on this page
                if (data.cursor && data.cursor.y > contentStartY) { 
                    doc.setFontSize(8);
                    doc.setFont('times', 'bold');
                    doc.text(`Semester GPA: ${semGPA}`, marginLeft, y + 15);
                    doc.text(`Cumulative GPA: ${cumGPA}`, marginLeft + 50, y + 15);
                    y = y + 20; // Set new start position for next block
                }
                
                // Re-draw Header/Footer on new pages added by autoTable
                if (data.pageNumber > 1) {
                    drawPageBordersAndInfo();
                }
            }
        });

        // Update y after AutoTable and GPA summary (AutoTable hook already handled it)
        // If the semester was too short to trigger didDrawPage, y might not be updated correctly.
        // Ensure y is advanced for the next semester block.
        if (doc.lastAutoTable) {
            y = doc.lastAutoTable.finalY + 20;
        } else {
            y += 20; 
        }
    });

    // Final Page Count Update (Must be run at the end)
    const finalPageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= finalPageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(7);
        // Correctly updates the footer on every page now that finalPageCount is known
        doc.text(`Page ${i} of ${finalPageCount}`, pageWidth - marginRight, pageHeight - 5, { align: 'right' });
    }

    doc.save(
        `${student.firstName}_${student.middleName}_${student.lastName}_Transcript.pdf`
    );
}
</script>
<template>
    <AppLayout>
        <Head title="Student Transcript" />
        <h1 class="text-2xl font-bold mb-2 text-center">
            <div>Shiloh International Theological Seminary</div>
            <div class="text-base font-normal text-gray-700 dark:text-gray-300">
                Student Official Transcript
            </div>
        </h1>

        <!-- <Link
            as="button"
            @click="exportPDF"
            class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-yellow-700"
        >
            Export PDF
        </Link> -->

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
                            <th class="px-4 py-2 text-center">Credit</th>
                            <th class="px-4 py-2 text-center">Grade Letter</th>
                            <th class="px-4 py-2 text-right">Points</th>
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
                            <td class="px-4 py-2 text-center">
                                {{ grade.course?.credit_hours }}
                            </td>
                            <td class="px-4 py-2 text-center">{{ grade.grade_letter }}</td>
                            <td class="px-4 py-2 text-right">
                                {{
                                    (
                                        getGradePointFromLetter(
                                            parseFloat(grade.grade_point) || 0
                                        ) *
                                        parseFloat(grade.course?.credit_hours || 0)
                                    ).toFixed(2)
                                }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr
                            class="bg-gray-50 dark:bg-gray-800 text-sm font-semibold"
                        >
                            <td class="px-4 py-2 text-right" colspan="3">
                                <span class="text-gray-700 dark:text-gray-300">
                                    Semester Totals:
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <p class="text-gray-700 dark:text-gray-300">
                                    Total Credits:
                                    <span class="text-green-700 dark:text-green-400">
                                    {{
                                        semester.grades
                                            .filter((g) => g.student_id == student.id)
                                            .reduce(
                                                (acc, g) =>
                                                    acc + parseFloat(g.course?.credit_hours || 0),
                                                0
                                            )
                                    }}
                                    </span>
                                </p>
                                <p class="text-gray-700 dark:text-gray-300">
                                    Semester GPA:
                                    <span class="text-green-700 dark:text-green-400">
                                        {{ calculateGPA(semester.grades, student.id) }}
                                    </span>
                                </p>
                                
                            </td>
                            <td class="px-4 py-2 text-right">
                                <p class="text-gray-700 dark:text-gray-300">
                                    Total Points:
                                    <span class="text-green-700 dark:text-green-400">
                                    {{
                                        semester.grades
                                            .filter((g) => g.student_id == student.id)
                                            .reduce(
                                                (acc, g) =>
                                                    acc +
                                                    getGradePointFromLetter(
                                                        parseFloat(g.grade_point) || 0
                                                    ) *
                                                        parseFloat(g.course?.credit_hours || 0),
                                                0
                                            )
                                            .toFixed(2)
                                    }}
                                    </span>
                                </p>
                                <p class="text-gray-700 dark:text-gray-300">
                                    Cumulative GPA:
                                    <span class="text-green-700 dark:text-green-400">
                                    {{ cumulativeGPAList[index]?.cumulativeGPA || "0.00" }}
                                    </span>
                                </p>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div v-else class="italic text-gray-500 dark:text-gray-400">
                No grades available for this semester.
            </div>
        </div>

        </AppLayout>
</template>