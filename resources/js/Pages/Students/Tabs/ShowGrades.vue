<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

// Props
const props = defineProps({
  section: { type: Object, required: true },
  course: { type: Object, required: true },
  semester: { type: Object, required: true },
  weights: { type: Array, required: true },
  instructor: { type: Object, required: true },
  student: { type: Object, required: true }, // ✅ Single student
});

const authUser = ref({ id: 1 }); // Replace with actual user logic

const sumOfWeightPoints = computed(() =>
  props.weights.reduce((sum, weight) => sum + (parseFloat(weight.point) || 0), 0)
);

const gradeForm = useForm({
  grade: {
    student_id: props.student.id,
    grade_point: "",
    grade_letter: "",
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
  },
});

const getStudentTotalPoints = () => {
  let total = 0;
  props.weights.forEach((weight) => {
    const result = weight.results.find(r => r.student_id === props.student.id);
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

const generateGrade = () => {
  const totalPoint = getStudentTotalPoints();
  gradeForm.grade.grade_point = totalPoint;
  gradeForm.grade.grade_letter = getGradeLetter(totalPoint);

  gradeForm.post(route("grades.storeOne"), {
    onSuccess: () => {
      Swal.fire("Success", "Grade successfully submitted!", "success");
      gradeForm.reset();
    },
    onError: () => {
      Swal.fire("Error", "Failed to submit grade.", "error");
    },
  });
};

const hasAllWeightValues = computed(() => {
  return props.weights.every((weight) => {
    return weight.results?.some(result =>
      result.student_id === props.student.id &&
      result.point !== null &&
      result.point !== undefined
    );
  });
});

const getStudentGrade = () =>
  props.section.grades.find(g => g.student_id === props.student.id);
</script>

<template>
  <div class="px-4 py-6">
    <!-- If grade not submitted -->
    <div v-if="!getStudentGrade()">
      <h2 class="text-xl font-bold mb-4">Generate Grade for {{ props.student.first_name }} {{ props.student.middle_name }}</h2>
      <div class="bg-white dark:bg-gray-900 rounded shadow p-4">
        <p><strong>Total Score ({{ sumOfWeightPoints }} pt):</strong> {{ getStudentTotalPoints() }}</p>
        <p><strong>Grade:</strong> {{ getGradeLetter(getStudentTotalPoints()) }}</p>

        <div class="mt-4">
          <button
            v-if="hasAllWeightValues"
            @click="generateGrade"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded"
          >
            Submit Grade
          </button>
          <p v-else class="text-sm text-red-600">
            Please complete all weight entries for this student.
          </p>
        </div>
      </div>
    </div>

    <!-- If grade already submitted -->
    <div v-else>
      <h2 class="text-xl font-bold mb-4">Submitted Grade for {{ props.student.first_name }} {{ props.student.middle_name }}</h2>
      <div class="bg-white dark:bg-gray-900 rounded shadow p-4">
        <p><strong>Total Score:</strong> {{ getStudentGrade().grade_point }}</p>
        <p><strong>Grade:</strong> {{ getStudentGrade().grade_letter }}</p>
      </div>
    </div>
  </div>
</template>
