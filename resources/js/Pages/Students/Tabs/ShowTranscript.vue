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

// Sort semesters by year name and then by semester name
const sortedSemesters = computed(() => {
    // A mapping to ensure 'First Semester' comes before 'Second Semester'
    const semesterOrder = {
        "First Semester": 1,
        "Second Semester": 2,
    };

    return [...props.semesters].sort((a, b) => {
        // Compare by year name (e.g., '1st Year' comes before '2nd Year')
        const yearA = a.year?.name || "";
        const yearB = b.year?.name || "";
        if (yearA < yearB) return -1;
        if (yearA > yearB) return 1;

        // If years are the same, compare by semester name
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

// PDF Export
function exportPDF() {
    // NOTE: Switched to PORTRAIT to better fit the two-column vertical stack,
    // which looks like the provided image.
    const doc = new jsPDF({
        orientation: "portrait", 
        unit: "mm",
        format: "a4",
    });
    const student = props.student;
    const semesters = sortedSemesters.value;
    const pageWidth = doc.internal.pageSize.getWidth(); // A4 Portrait width (~210mm)
    const pageHeight = doc.internal.pageSize.getHeight(); // A4 Portrait height (~297mm)
    
    // Margins and Layout
    const marginLeft = 10;
    const marginRight = 10;
    const innerWidth = pageWidth - marginLeft - marginRight; // ~190mm
    const colWidth = (innerWidth / 2) - 3; // Column width (2 columns + 3mm gap)
    
    // Fixed vertical starting point for content after header
    const contentStartY = 40; 
    let y = contentStartY; 
    let semesterCounter = 0; // Tracks which column we are in (0 for left, 1 for right)

    // --- HELPER FUNCTION: Draw Header and Footer ---
    const drawPageBordersAndInfo = () => {
        // Get the current number of pages for the footer page count
        const totalPages = doc.internal.getNumberOfPages();
        const currentPage = doc.internal.getCurrentPageInfo().pageNumber;

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

        // 2. STUDENT INFO BLOCK (Tightly packed 2-column)
        let infoY = 20;
        doc.setFontSize(8);
        doc.setFont('times', 'normal'); // Use a standard font for text
        const infoCol1 = marginLeft;
        const infoCol2 = marginLeft + (innerWidth / 2);

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
        doc.line(marginLeft, infoY, pageWidth - marginRight, infoY); // Separator line
        
        // 3. FOOTER
        const footerY = pageHeight - 15;
        doc.setFontSize(7);
        doc.line(marginLeft, footerY - 2, pageWidth - marginRight, footerY - 2);
        
        // Grading Scale Block
        const gradingScaleText = `Grading: A = 4.0, A- = 3.7, B+ = 3.3, B = 3.0, B- = 2.7, C+ = 2.3, C = 2.0, C- = 1.7, D+ = 1.3, D = 1.0, F = 0.0`;
        doc.text(gradingScaleText, marginLeft, footerY);

        // Signature Block
        const signatureBlockX = pageWidth / 2;
        doc.text(`Registrar's Signature: ____________________`, signatureBlockX - 30, footerY + 4);
        doc.text(`Date: ${new Date().toLocaleDateString()}`, pageWidth - 40, footerY + 4);
        
        // Use placeholder for page count during loop, final update is separate
        doc.text(`Page ${currentPage} of ${totalPages}`, pageWidth - marginRight, pageHeight - 5, { align: 'right' });
    };

    // Initial Header/Footer draw
    drawPageBordersAndInfo();

    // --- MAIN SEMESTER LOOP ---
    // Use an external cumulative tracker
    let totalCumulativePoints = 0;
    let totalCumulativeCredits = 0;
    let columnLeftY = contentStartY; // Y position for the left column
    let columnRightY = contentStartY; // Y position for the right column

    semesters.forEach((semester, index) => {
        const grades = semester.grades.filter((g) => g.student_id == student.id);
        if (grades.length === 0) return;

        // Determine current column position
        const isLeftColumn = (semesterCounter % 2 === 0);
        let startX = isLeftColumn ? marginLeft : marginLeft + colWidth + 6; // 6mm gap
        let currentY = isLeftColumn ? columnLeftY : columnRightY;

        // 1. Calculate Semester Statistics
        let semesterPoints = 0;
        let semesterCredits = 0;
        grades.forEach((g) => {
            // Note: getGradePointFromLetter must exist in the scope where this function is called
            const gp = getGradePointFromLetter(parseFloat(g.grade_point) || 0);
            const credit = parseFloat(g.course?.credit_hours || 0);
            semesterPoints += gp * credit;
            semesterCredits += credit;
        });

        const semGPA = semesterCredits > 0 ? (semesterPoints / semesterCredits).toFixed(2) : "0.00";
        totalCumulativePoints += semesterPoints;
        totalCumulativeCredits += semesterCredits;
        const cumGPA = totalCumulativeCredits > 0 ? (totalCumulativePoints / totalCumulativeCredits).toFixed(2) : "0.00";

        // 2. Add Semester Title
        doc.setFontSize(8);
        doc.setFont('times', 'bold');
        doc.text(
            `${semester.year?.name ?? "Unknown Year"} - ${semester.name ?? "Unknown Semester"}`,
            startX,
            currentY
        );
        currentY += 4;
        doc.setFont('times', 'normal');

        // 3. AutoTable for Courses
        const tableData = grades.map((g) => [
            g.course?.code || "N/A",
            g.course?.name || "N/A",
            `${g.course?.credit_hours || 0}`,
            (
                getGradePointFromLetter(parseFloat(g.grade_point) || 0) *
                parseFloat(g.course?.credit_hours || 0)
            ).toFixed(1), // Points: Reduced decimals for space
            g.grade_letter || "N/A",
        ]);

        autoTable(doc, {
            head: [["Course", "Course Name", "CRD", "Points", "Grade"]], // Simplified headers
            body: tableData,
            startY: currentY,
            theme: "grid",
            styles: { fontSize: 7, cellPadding: 0.5 }, // Highly condensed style
            headStyles: { fontSize: 7, fillColor: [200, 200, 200], textColor: 0, lineWidth: 0.1 },
            columnStyles: {
                0: { cellWidth: 15 }, 
                1: { cellWidth: colWidth - 56 }, // Course Name takes remaining space
                2: { cellWidth: 12, halign: 'center' }, // CRD (Credit)
                3: { cellWidth: 15, halign: 'right' }, // Points
                4: { cellWidth: 14, halign: 'center' }, // Grade
            },
            margin: { left: startX, right: (pageWidth - startX - colWidth) }, // Dynamic margin
            didDrawPage: (data) => {
                currentY = data.cursor.y; 
            },
            didParseCell: (data) => {
                if (data.column.index === 1) {
                    data.cell.styles.cellWidth = colWidth - 56;
                }
            }
        });

        // Get final Y position after table
        currentY = doc.lastAutoTable.finalY + 1; 

        // --- 4. CORRECTED SEMESTER SUMMARY FOOTER ---
        doc.setFontSize(7);
        doc.setFont('times', 'bold');
        
        // Calculate the absolute X-end positions for CRD and Points columns
        const courseCodeWidth = 15;
        const courseNameWidth = colWidth - 56;
        const crdWidth = 12;
        const pointsWidth = 15;

        // CRD Column X-End Position (Start of 'CRD' col + width of 'CRD' col)
        const crdColXEnd = startX + courseCodeWidth + courseNameWidth + crdWidth; 
        // Points Column X-End Position 
        const pointsColXEnd = crdColXEnd + pointsWidth; 
        
        const creditValue = semesterCredits.toFixed(0); 
        const pointsValue = semesterPoints.toFixed(1);
        const cumPointsValue = totalCumulativePoints.toFixed(1);
        const cumCreditValue = totalCumulativeCredits.toFixed(0);

        // --- ROW 1: SEMESTER STATS (aligned to CRD and Points columns) ---
        
        // Semester GPA text (Left aligned)
        doc.text(`Semester GPA: ${semGPA}`, startX, currentY + 3);

        // Sem Totals label (Aligned right, just before the CRD column starts)
        doc.text(`Sem Totals`, crdColXEnd - crdWidth, currentY + 3, { align: 'right' }); 
        
        // SEMESTER CREDITS VALUE (Right aligned under CRD column)
        doc.text(`${creditValue}`, crdColXEnd, currentY + 3, { align: 'right' }); 
        
        // SEMESTER POINTS VALUE (Right aligned under Points column)
        doc.text(`${pointsValue}`, pointsColXEnd, currentY + 3, { align: 'right' }); 

        // --- ROW 2: CUMULATIVE STATS (aligned to CRD and Points columns) ---
        
        // Cumulative GPA text (Left aligned)
        doc.text(`Cumulative GPA: ${cumGPA}`, startX, currentY + 7);
        
        // Cum Totals label (Aligned right, just before the CRD column starts)
        doc.text(`Cum Totals`, crdColXEnd - crdWidth, currentY + 7, { align: 'right' });
        
        // CUMULATIVE CREDITS VALUE (Right aligned under CRD column)
        doc.text(`${cumCreditValue}`, crdColXEnd, currentY + 7, { align: 'right' }); 
        
        // CUMULATIVE POINTS VALUE (Right aligned under Points column)
        doc.text(`${cumPointsValue}`, pointsColXEnd, currentY + 7, { align: 'right' }); 
        
        doc.setFont('times', 'normal'); 
        
        currentY += 10; 

        // 5. Update Column Y positions
        if (isLeftColumn) {
            columnLeftY = currentY;
        } else {
            columnRightY = currentY;
        }
        
        semesterCounter++;

        // 6. Page Break Logic (Handles reaching the bottom of the right column)
        const breakPoint = pageHeight - 50; 
        if (semesterCounter % 2 === 0 && currentY > breakPoint) {
            doc.addPage();
            drawPageBordersAndInfo(); // Draw header/footer on the new page
            
            // Reset Y positions for the new page
            columnLeftY = contentStartY;
            columnRightY = contentStartY;
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
