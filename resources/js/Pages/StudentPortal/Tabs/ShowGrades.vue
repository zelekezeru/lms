<script setup>
import { ref } from "vue"
import Button from "primevue/button"
import PrimaryButton from "@/Components/PrimaryButton.vue"
import Modal from "@/Components/Modal.vue"
import { AcademicCapIcon } from "@heroicons/vue/24/outline"
import axios from "axios"
import Swal from "sweetalert2"
import jsPDF from "jspdf"
import autoTable from "jspdf-autotable"

const props = defineProps({
    student: { type: Object, required: true },
    grades: { type: Array, required: true },
    results: { type: Array, default: () => [] }
})

const showMobileNav = ref(false)
const showGradesModal = ref(false)

function openModal() {
    showGradesModal.value = true
}
function closeModal() {
    showGradesModal.value = false
}

// PDF Export function for grades
const exportGradesPDF = () => {
    const doc = new jsPDF({
        orientation: "landscape",
        unit: "mm",
        format: "a4",
    })
    const student = props.student
    const grades = props.grades
    const pageWidth = doc.internal.pageSize.getWidth()
    let y = 20

    doc.setFontSize(16)
    doc.text("Student Grades Report", pageWidth / 2, y, { align: "center" })
    y += 10

    doc.setFontSize(12)
    doc.text(
        `Name: ${student.firstName} ${student.lastName}`,
        14,
        y
    )
    y += 7
    doc.text(
        `Student ID: ${student.idNo || student.id}`,
        14,
        y
    )
    y += 10

    // Table data
    const tableData = grades.map((g, i) => [
        i + 1,
        g.course?.name || "N/A",
        g.course?.credit_hours || "N/A",
        g.grade_letter || "N/A",
        g.semester?.name || "N/A"
    ])

    autoTable(doc, {
        head: [["#", "Course Name", "Credit Hours", "Grade", "Semester"]],
        body: tableData,
        startY: y,
        theme: "grid",
        styles: { fontSize: 10 },
        headStyles: { fillColor: [41, 128, 185], textColor: 255 },
        margin: { left: 14, right: 10 },
    })

    doc.save(
        `${student.firstName}_${student.lastName}_Grades.pdf`
    )
}
</script>


<template>
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Grades for {{ student.firstName }} {{ student.lastName }}</h1>
            <Button label="Download Grades" icon="pi pi-download" @click="downloadGradesPdf" />
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-300 dark:border-gray-600">
            <h2 class="text-xl font-bold mb-4">Grades</h2>
            <table class="w-full min">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="text-left px-3 py-2 font-medium text-xs w-8 text-gray-800 dark:text-gray-200">#</th>
                        <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Course Name</th>
                        <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Credit Hours</th>
                        <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Grade</th>
                        <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Semester</th>
                        <th class="text-left px-4 py-2 font-medium text-sm text-gray-800 dark:text-gray-200">Action</th>
                    </tr>
                </thead>
                <tbody v-if="grades.length > 0">
                    <tr
                        v-if="props.grades.length > 0"
                        v-for="(grade, index) in props.grades"
                        :key="grade.id"
                        :class="[
                            index % 2 === 0
                                ? 'bg-white dark:bg-gray-800'
                                : 'bg-gray-50 dark:bg-gray-700',
                            'border-b border-gray-200 dark:border-gray-600',
                        ]"
                    >
                        <td class="px-3 py-3 text-sm text-gray-700 dark:text-gray-300">{{ index + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ grade.course.name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ grade.course.credit_hours }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-gray-700 dark:text-gray-300">{{ grade.grade_letter }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ grade.semester.name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            <PrimaryButton size="sm" @click="openModal('grades')">View</PrimaryButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <Modal :show="showGradesModal" @close="closeModal('grades')" :max-width="'lg'">
        <div class="p-6">
            <h2 class="text-xl font-bold mb-4">Grade Details</h2>
            <p>Grade details and actions go here.</p>
            <div class="mt-4 flex justify-end">
                <PrimaryButton @click="closeModal('grades')">Close</PrimaryButton>
            </div>
        </div>
    </Modal>
    <div class="flex justify-center mt-8">
        <PrimaryButton @click="showMobileNav = !showMobileNav">
            <AcademicCapIcon class="w-5 h-5 mr-2" />
            {{ showMobileNav ? "Close" : "Course Navigation" }}
        </PrimaryButton>
    </div>
</template>