<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

// Props from parent (must be passed from the backend)
const props = defineProps({
  section: { type: Object, required: true },        // Section includes students and grades
  course: { type: Object, required: true },
  semester: { type: Object, required: true },
  weights: { type: Array, required: true },         // Grade weights (e.g., Exam 60%, Quiz 20%)
  instructor: { type: Object, required: true },
  studentsList: { type: Object, required: true }, // List of students in the section
});

const authUser = ref({ id: 1 }); // Replace with actual auth user logic if needed

const sumOfWeightPoints = computed(() =>
  props.weights.reduce((sum, weight) => sum + (parseFloat(weight.point) || 0), 0)
);

const gradeForm = useForm({
  grades: [],
});

const getStudentTotalPoints = (studentId) => {
  let total = 0;
  props.weights.forEach((weight) => {
    const result = weight.results.find(r => r.student_id === studentId);
    total += parseFloat(result?.point || 0);
  });
  return total.toFixed(2);
};

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
  return "F";
};

// Function to generate grades for all students in the section
const generateGrades = () => {
  const gradesPayload = props.studentsList.map((student) => {
    const totalPoint = getStudentTotalPoints(student.id);
    return {
      student_id: student.id,
      grade_point: totalPoint,
      grade_letter: getGradeLetter(totalPoint),
      grade_description: "",
      grade_scale: sumOfWeightPoints.value.toString(),
      grade_complaint: false,
      grade_comment: "",
      changed_grade: null,
      grade_status: "Pending",
      changed_by: null,
      user_id: authUser.value.id,
      year_id: props.semester.year_id,
      semester_id: props.semester.id,
      section_id: props.section.id,
      course_id: props.course.id,
    };
  });

  gradeForm.grades = gradesPayload;

  gradeForm.post(route("grades.store"), {
    onSuccess: () => {
      Swal.fire("Success", "Grades successfully submitted!", "success");
      gradeForm.reset();
    },
    onError: () => {
      Swal.fire("Error", "Failed to submit grades.", "error");
    },
  });
};

// Function to check if the weights have results
const allWeightsHaveValues = computed(() => {
  return props.studentsList.every((student) => {
    return props.weights.every((weight) => {
      return weight.results?.some(result => result.student_id === student.id && result.point !== null && result.point !== undefined);
    });
  });
});


// Function to get the grade for a specific student
const getStudentGrade = (studentId) =>
  props.section.grades.find(g => g.student_id === studentId);

</script>

<template>
  <div class="overflow-x-auto px-4 py-6">
    <!-- Grades not yet submitted: show grade generation table -->
    <div v-if="!props.section.grades || props.section.grades.filter(grade => grade.course_id === props.course.id).length === 0">
      <h2 class="text-xl font-bold mb-4">Grade Generation Form</h2>
      <table class="min-w-full border rounded shadow bg-white dark:bg-gray-900">
        <thead class="bg-gray-100 dark:bg-gray-800 text-sm font-semibold">
          <tr>
            <th class="px-4 py-2">#</th>
            <th class="px-4 py-2 text-left">Student Name</th>
            <th class="px-4 py-2">Total ({{ sumOfWeightPoints }} pt)</th>
            <th class="px-4 py-2">Grade</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(student, index) in props.studentsList"
            :key="student.id"
            :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
            class="text-sm border-b"
          >
            <td class="px-4 py-2">{{ index + 1 }}</td>
            <td class="px-4 py-2">{{ student.first_name }} {{ student.middle_name }}</td>
            <td class="px-4 py-2 font-semibold">{{ getStudentTotalPoints(student.id) }}</td>
            <td class="px-4 py-2">{{ getGradeLetter(getStudentTotalPoints(student.id)) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="100%" class="text-center py-4 bg-gray-50 dark:bg-gray-800">
                <!-- Only show the button if all weights have values -->
                <button
                    v-if="allWeightsHaveValues"
                    @click="generateGrades"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded"
                >
                    Submit Grades
                </button>
                <p v-else class="text-sm text-red-600 dark:text-red-400">
                    Please enter all required weight values before submitting grades.
                </p>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Grades already submitted: display only -->
    <div v-else>
      <h2 class="text-xl font-bold mb-4">Submitted Grades</h2>
      <table class="min-w-full border rounded shadow bg-white dark:bg-gray-900">
        <thead class="bg-gray-100 dark:bg-gray-800 text-sm font-semibold">
          <tr>
            <th class="px-4 py-2">#</th>
            <th class="px-4 py-2 text-left">Student Name</th>
            <th class="px-4 py-2">Total ({{ sumOfWeightPoints }} pt)</th>
            <th class="px-4 py-2">Grade</th>
          </tr>
        </thead>
        <tbody>
            <tr
                v-for="(student, index) in props.studentsList"
                :key="student.id"
                :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                class="text-sm border-b"
                >
                <td class="px-4 py-2">{{ index + 1 }}</td>
                <td class="px-4 py-2">{{ student.first_name }} {{ student.middle_name }}</td>

                <!-- Fetch the grade that matches the current student -->
                <td class="px-4 py-2 font-semibold">
                    {{ getStudentGrade(student.id)?.grade_point ?? "N/A" }}
                </td>
                <td class="px-4 py-2">
                    {{ getStudentGrade(student.id)?.grade_letter ?? "N/A" }}
                </td>
            </tr>

        </tbody>
      </table>
    </div>
  </div>
</template>
