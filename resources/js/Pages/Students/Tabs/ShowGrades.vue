<script setup>
import { defineProps, ref, watch } from "vue";
import { usePage, Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    EyeIcon,
    TrashIcon,
    PencilSquareIcon,
    ArrowPathIcon,
    Squares2X2Icon,
    TableCellsIcon,
    CalendarIcon,
} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";

// Props
const props = defineProps({
  student: {
    type: Object,
    required: true,
  },
  grades: {
    type: Array,
    default: () => [],
  },
});

// Grade Edit Modal State
const showGradeEditModal = ref(false);
const editingGrade = ref(null);
const gradeForm = useForm({
    changed_letter: "",
    changed_grade: "",
    grade_comment: "",
});

function openGradeEditModal(grade) {
    editingGrade.value = grade;
    gradeForm.changed_letter = grade.changed_letter || "";
    gradeForm.changed_grade = grade.changed_grade || "";
    gradeForm.grade_comment = grade.grade_comment || "";
    gradeForm.id = grade.id;
    gradeForm.processing = false;
    gradeForm.reset();
    gradeForm.clearErrors();
    showGradeEditModal.value = true;
}

function closeGradeEditModal() {
    showGradeEditModal.value = false;
    editingGrade.value = null;
    gradeForm.reset();
}

function submitGradeUpdate() {
    if (!editingGrade.value) return;
    gradeForm.put(route('grades.update', { grade: editingGrade.value.id }), {
        onSuccess: () => {
            Swal.fire("Success", "Grade updated successfully.", "success");
            closeGradeEditModal();
        },
        onError: () => {
            Swal.fire("Error", "Failed to update grade.", "error");
        }
    });
}

watch(() => gradeForm.changed_grade, (newVal) => {
    const totalPoint = parseFloat(newVal);
    let letter = "NG";
    if (isNaN(totalPoint)) letter = "NG";
    else if (totalPoint >= 94) letter = "A";
    else if (totalPoint >= 90) letter = "A-";
    else if (totalPoint >= 87) letter = "B+";
    else if (totalPoint >= 84) letter = "B";
    else if (totalPoint >= 80) letter = "B-";
    else if (totalPoint >= 77) letter = "C+";
    else if (totalPoint >= 74) letter = "C";
    else if (totalPoint >= 70) letter = "C-";
    else if (totalPoint >= 67) letter = "D+";
    else if (totalPoint >= 64) letter = "D";
    else if (totalPoint >= 60) letter = "D-";
    else if (totalPoint >= 0) letter = "F";
    else letter = "NG";
    gradeForm.changed_letter = letter;
});
</script>

<template>
  <div class="max-w-5xl mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-100">
      Academic Grade Report for
      {{ props.student.firstName }} {{ props.student.middleName }} {{ props.student.lastName }}
    </h2>

    <div
      v-if="grades && grades.length > 0"
      class="overflow-x-auto"
    >
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 shadow rounded-lg">
        <thead class="bg-gray-100 dark:bg-gray-800">
          <tr>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">#</th>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Course</th>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Semester</th>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Grade Letter</th>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Grade Point</th>
            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
          <tr
            v-for="(grade, index) in grades"
            :key="`${grade.course?.id}-${grade.semester?.id}-${index}`"
            class="hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ index + 1 }}</td>
            <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ grade.course?.name || 'N/A' }}</td>
            <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ grade.semester?.name || 'N/A' }}</td>
            <td class="px-4 py-2 text-green-600 dark:text-green-400 font-semibold">{{ grade.grade_letter || '-' }}</td>
            <td class="px-4 py-2 text-blue-600 dark:text-blue-400 font-semibold">{{ grade.grade_point || '-' }}</td>
            <td class="px-4 py-2">
              <button
                v-if="grade.id"
                :key="`edit-${grade.id}`"
                class="text-blue-600 dark:text-blue-400 hover:underline ml-2 flex items-center"
                @click="openGradeEditModal(grade)"
              >
                <PencilSquareIcon class="w-5 h-5 mr-1 inline" />
                Edit
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="text-center text-gray-500 dark:text-gray-400 mt-10">
      No grade records found for this student.
    </div>
  </div>

  <Modal :show="showGradeEditModal" @close="closeGradeEditModal">
    <div class="p-6 space-y-6 bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-xl shadow-xl">
      <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-300 flex items-center gap-2">
        <PencilSquareIcon class="w-6 h-6 text-blue-500 dark:text-blue-300" />
        Edit Grade
      </h2>

      <p class="text-gray-600 dark:text-gray-300">
        Update the grade details for
        <strong class="text-blue-700 dark:text-blue-300">
          {{ editingGrade?.course?.name || 'N/A' }} - {{ student?.firstName || 'N/A' }} {{ student?.lastName || 'N/A' }}
        </strong>

        <!-- Existing point and Letter -->
        <div class="flex flex-col sm:flex-row gap-2 mt-2">
          <span v-if="editingGrade?.grade_point" class="text-sm text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
            Current Grade Point: <strong>{{ editingGrade.grade_point }}</strong>
          </span>
          <span v-if="editingGrade?.grade_letter" class="text-sm text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
            Current Grade Letter: <strong>{{ editingGrade.grade_letter }}</strong>
          </span>
        </div>
        <input type="hidden" v-model="gradeForm.id" />
      </p>
      <form @submit.prevent="submitGradeUpdate" class="space-y-4">
        <div>
        <div class="flex gap-4">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Change Grade Point</label>
            <input
              v-model="gradeForm.changed_grade"
              type="number"
              step="0.01"
              min="0"
              max="100"
              class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              required
            />
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Grade Letter</label>
            <input
              v-model="gradeForm.changed_letter"
              type="text"
              maxlength="2"
              class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              required
              readonly
            />
          </div>
        </div>
        </div>
        
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Comments</label>
          <textarea
            v-model="gradeForm.grade_comment"
            rows="3"
            class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
            placeholder="Enter any comments or feedback here..."
          ></textarea>
        </div>
        <div class="flex justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="closeGradeEditModal"
            class="px-5 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="gradeForm.processing"
            class="px-5 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-400 text-white font-semibold shadow hover:from-blue-700 hover:to-blue-500 transition disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <span v-if="gradeForm.processing" class="animate-spin mr-2">
              <svg class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
              </svg>
            </span>
            Save
          </button>
        </div>
      </form>
    </div>
  </Modal>
</template>
