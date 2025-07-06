<script setup>
import { ref, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
  section: Object,
  course: Object,
  semester: Object,
  weights: Array,
  instructor: Object,
  studentsList: Array,
  studentResults: Object, // <-- This comes from your controller
});

const sumOfWeightPoints = computed(() =>
  props.weights.reduce((sum, weight) => sum + (parseFloat(weight.point) || 0), 0)
);

const authUser = ref({ id: props.instructor.id });

const gradeForm = useForm({ grades: [] });

// ✅ Get total for a student from your `studentResults`
const getStudentTotalPoints = (studentId) => {
  let total = 0;
  props.weights.forEach((weight) => {
    const result = props.studentResults[studentId][weight.id];
    if (result && result.point) {
      total += parseFloat(result.point);
    }
  });
  return total.toFixed(2);
};

// ✅ Get grade letter
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

// ✅ Create grades payload
const generateGrades = () => {
  const gradesPayload = props.studentsList.map((student) => {
    const totalPoint = getStudentTotalPoints(student.id);
    return {
      student_id: student.id,
      grade_point: totalPoint,
      grade_letter: getGradeLetter(totalPoint),
      grade_scale: sumOfWeightPoints.value.toString(),
      grade_status: "Submitted",
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

// ✅ Ensure all weights are filled
const allWeightsHaveValues = computed(() =>
  props.studentsList.every((student) =>
    props.weights.every(
      (weight) =>
        props.studentResults[student.id][weight.id]?.point !== null &&
        props.studentResults[student.id][weight.id]?.point !== undefined
    )
  )
);

// ✅ Get submitted grade
const getStudentGrade = (studentId) =>
  props.section.grades.find((g) => g.student_id === studentId);

// ✅ Import grades file
const fileInput = ref(null);

const submitImport = () => {
  const formData = new FormData();
  formData.append("grades_file", fileInput.value.files[0]);
  formData.append("course_id", props.course.id);
  formData.append("section_id", props.section.id);
  formData.append("semester_id", props.semester.id);
  formData.append("year_id", props.semester.year_id);

  router.post(route("sectionGrades.import"), formData, {
    forceFormData: true,
  });
};
</script>


<template>
  <div class="overflow-x-auto px-4 py-6">
    <!-- Show grade generation if grades not submitted -->
    <div v-if="!props.section.grades || props.section.grades.filter(g => g.course_id === props.course.id).length === 0">
      <h2 class="text-xl font-bold mb-4">Grade Generation Form</h2>

      <!-- Import file -->
      <form @submit.prevent="submitImport" enctype="multipart/form-data" class="mb-4">
        <input type="file" ref="fileInput" accept=".csv,.xlsx,.xls" class="border p-2 rounded text-sm" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded ml-2">Import</button>
      </form>

      <table class="min-w-full border rounded shadow bg-white dark:bg-gray-900">
        <thead class="bg-gray-100 dark:bg-gray-800 text-sm">
          <tr>
            <th class="px-4 py-2">#</th>
            <th class="px-4 py-2 text-left">Student</th>
            <th v-for="weight in props.weights" :key="weight.id" class="px-4 py-2 text-center">
              {{ weight.name }} ({{ weight.point }}pt)
            </th>
            <th class="px-4 py-2">Total ({{ sumOfWeightPoints }}pt)</th>
            <th class="px-4 py-2">Grade</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(student, index) in props.studentsList" :key="student.id" class="text-sm">
            <td class="px-4 py-2">{{ index + 1 }}</td>
            <td class="px-4 py-2">{{ student.firstName }} {{ student.middleName }}</td>
            <td v-for="weight in props.weights" :key="weight.id" class="px-4 py-2 text-center">
              {{
                props.studentResults[student.id][weight.id]?.point ??
                "N/A"
              }}
            </td>
            <td class="px-4 py-2 font-semibold">{{ getStudentTotalPoints(student.id) }}</td>
            <td class="px-4 py-2">{{ getGradeLetter(getStudentTotalPoints(student.id)) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="100%" class="text-center py-4">
              <button
                v-if="allWeightsHaveValues"
                @click="generateGrades"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded"
              >
                Submit Grades
              </button>
              <p v-else class="text-sm text-red-600">
                Please enter all required weight values before submitting.
              </p>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Already submitted grades -->
    <div v-else>
      <h2 class="text-xl font-bold mb-4">Submitted Grades</h2>
      <table class="min-w-full border rounded shadow bg-white dark:bg-gray-900">
        <thead class="bg-gray-100 dark:bg-gray-800 text-sm">
          <tr>
            <th class="px-4 py-2">#</th>
            <th class="px-4 py-2 text-left">Student</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2">Grade</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(student, index) in props.studentsList" :key="student.id" class="text-sm">
            <td class="px-4 py-2">{{ index + 1 }}</td>
            <td class="px-4 py-2">{{ student.firstName }} {{ student.middleName }}</td>
            <td class="px-4 py-2">{{ getStudentGrade(student.id)?.grade_point ?? "N/A" }}</td>
            <td class="px-4 py-2">{{ getStudentGrade(student.id)?.grade_letter ?? "N/A" }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>