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
import { PlusIcon } from "@heroicons/vue/24/outline";

const showGradeCreateModal = ref(false);
const closeGradeCreateModal = () => {
  showGradeCreateModal.value = false;
};

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
  deletedGrades: {
    type: Array,
    default: () => [],
  },
});

// Grade Delete (soft delete with reason) Modal State
const showGradeDeleteModal = ref(false);
const deletingGrade = ref(null);
const showDeletedGrades = ref(false);
const gradeDeleteForm = useForm({
    delete_reason: "",
});

function openGradeDeleteModal(grade) {
    deletingGrade.value = grade;
    gradeDeleteForm.reset();
    gradeDeleteForm.clearErrors();
    showGradeDeleteModal.value = true;
}

function closeGradeDeleteModal() {
    showGradeDeleteModal.value = false;
    deletingGrade.value = null;
    gradeDeleteForm.reset();
}

function submitGradeDelete() {
    if (!deletingGrade.value) return;
    gradeDeleteForm.delete(route('grades.soft-delete', { grade: deletingGrade.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire("Deleted", "Grade moved to deleted grades.", "success");
            closeGradeDeleteModal();
        },
        onError: () => {
            Swal.fire("Error", "Failed to delete grade.", "error");
        },
    });
}

function restoreGrade(grade) {
    Swal.fire({
        title: "Restore this grade?",
        text: "It will become visible again in the transcript and student portal.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, restore",
        confirmButtonColor: "#16a34a",
    }).then((result) => {
        if (!result.isConfirmed) return;
        router.patch(route('grades.restore', { grade: grade.id }), {}, {
            preserveScroll: true,
            onSuccess: () => Swal.fire("Restored", "Grade restored successfully.", "success"),
            onError: () => Swal.fire("Error", "Failed to restore grade.", "error"),
        });
    });
}

function formatDate(value) {
    if (!value) return "—";
    const d = new Date(value);
    return isNaN(d) ? value : d.toLocaleString();
}

// Grade Edit Modal State
const showGradeEditModal = ref(false);
const editingGrade = ref(null);
const gradeForm = useForm({
    changed_letter: "",
    changed_grade: "",
    grade_comment: "",
});

// Add grade form state
const gradeCreateForm = useForm({
    course_id: "",
    grade_point: "",
    grade_letter: "",
});

// Courses prop (assume passed from parent or fetched)
const courses = ref([]); // You may need to fetch or pass this prop

function openGradeEditModal(grade) {
    editingGrade.value = grade;
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

// Open create modal
function openGradeCreateModal() {
    gradeCreateForm.course_id = "";
    gradeCreateForm.grade_point = "";
    gradeCreateForm.grade_letter = "";
    gradeCreateForm.reset();
    gradeCreateForm.clearErrors();
    showGradeCreateModal.value = true;
}

// Submit create grade
function submitGradeCreate() {
    gradeCreateForm.post(route('students.grades', { student: props.student.id }), {
        onSuccess: () => {
            Swal.fire("Success", "Grade added successfully.", "success");
            showGradeCreateModal.value = false;
            gradeCreateForm.reset();
        },
        onError: () => {
            Swal.fire("Error", "Failed to add grade.", "error");
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

// Auto-calculate grade letter for create form
watch(() => gradeCreateForm.grade_point, (newVal) => {
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
    gradeCreateForm.grade_letter = letter;
});
</script>

<template>
  <div class="max-w-5xl mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-100">
      Grade Report for
      {{ props.student.firstName }} {{ props.student.middleName }} {{ props.student.lastName }}
    </h2>

    <button
        v-if="userCan('add.grades')"
        :key="`add-grade-${props.student.id}`"
        class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-400 text-white font-semibold shadow hover:from-blue-700 hover:to-blue-500 transition flex items-center ml-2"
        @click="openGradeCreateModal"
      >
        <PlusIcon class="w-5 h-5 mr-1 inline" />
        Add Grade
    </button>

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
            <th v-if="userCan('edit.grades') || userCan('delete.grades')" class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Actions</th>
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
              <div class="flex items-center gap-3">
                <button
                  v-if="userCan('edit.grades')"
                  :key="`edit-${grade.id}`"
                  class="text-blue-600 dark:text-blue-400 hover:underline flex items-center"
                  @click="openGradeEditModal(grade)"
                >
                  <PencilSquareIcon class="w-5 h-5 mr-1 inline" />
                  Edit
                </button>
                <button
                  v-if="userCan('delete.grades')"
                  :key="`delete-${grade.id}`"
                  class="text-red-600 dark:text-red-400 hover:underline flex items-center"
                  @click="openGradeDeleteModal(grade)"
                >
                  <TrashIcon class="w-5 h-5 mr-1 inline" />
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="text-center text-gray-500 dark:text-gray-400 mt-10">
      No grade records found for this student.
    </div>

    <!-- Deleted Grades (recoverable) -->
    <div v-if="userCan('delete.grades') && deletedGrades && deletedGrades.length > 0" class="mt-10">
      <button
        type="button"
        class="flex items-center gap-2 text-sm font-semibold text-red-700 dark:text-red-400 hover:underline"
        @click="showDeletedGrades = !showDeletedGrades"
      >
        <TrashIcon class="w-5 h-5" />
        Deleted Grades ({{ deletedGrades.length }})
        <span class="text-xs text-gray-500">{{ showDeletedGrades ? 'Hide' : 'Show' }}</span>
      </button>

      <div v-if="showDeletedGrades" class="overflow-x-auto mt-3 border border-red-200 dark:border-red-900 rounded-lg">
        <table class="min-w-full divide-y divide-red-200 dark:divide-red-900">
          <thead class="bg-red-50 dark:bg-red-950/40">
            <tr>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Course</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Semester</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Letter</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Point</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Reason</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Deleted By</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Deleted At</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-red-100 dark:divide-red-900/50">
            <tr v-for="grade in deletedGrades" :key="`deleted-${grade.id}`" class="hover:bg-red-50/50 dark:hover:bg-red-950/20">
              <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ grade.course?.name || 'N/A' }}</td>
              <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ grade.semester?.name || 'N/A' }}</td>
              <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ grade.grade_letter || '-' }}</td>
              <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ grade.grade_point || '-' }}</td>
              <td class="px-4 py-2 text-gray-600 dark:text-gray-400 max-w-xs whitespace-pre-wrap">{{ grade.delete_reason || '—' }}</td>
              <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ grade.deleted_by_name || '—' }}</td>
              <td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-sm">{{ formatDate(grade.deleted_at) }}</td>
              <td class="px-4 py-2">
                <button
                  class="text-green-600 dark:text-green-400 hover:underline flex items-center"
                  @click="restoreGrade(grade)"
                >
                  <ArrowPathIcon class="w-5 h-5 mr-1 inline" />
                  Restore
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Create Course Grade for a Student -->
  <Modal :show="showGradeCreateModal" @close="closeGradeCreateModal">
    <div class="p-6 space-y-6 bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-xl shadow-xl">
      <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-300 flex items-center gap-2">
        <PlusIcon class="w-6 h-6 text-blue-500 dark:text-blue-300" />
        Create Course Grade
      </h2>

      <form @submit.prevent="submitGradeCreate" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Course</label>
          <select v-model="gradeCreateForm.course_id" class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" required>
            <option value="">Select a course</option>
            <option v-for="enrolment in student.enrollments" :key="enrolment.id" :value="enrolment.course.id">{{ enrolment.course.name }}</option>
          </select>
        </div>

        <div class="flex gap-4">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Grade Point</label>
            <input
              v-model="gradeCreateForm.grade_point"
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
              v-model="gradeCreateForm.grade_letter"
              type="text"
              maxlength="2"
              class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              required
              readonly
            />
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
          <button
            type="button"
            @click="closeGradeCreateModal"
            class="px-5 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-5 py-2 rounded-lg bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-400 transition font-semibold"
          >
            Save Grade
          </button>
        </div>
      </form>
    </div>
  </Modal>

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

  <!-- Delete Grade (soft delete with reason) -->
  <Modal :show="showGradeDeleteModal" @close="closeGradeDeleteModal">
    <div class="p-6 space-y-5 bg-white dark:bg-gray-800 rounded-xl shadow-xl">
      <h2 class="text-2xl font-bold text-red-700 dark:text-red-400 flex items-center gap-2">
        <TrashIcon class="w-6 h-6" />
        Delete Grade
      </h2>

      <p class="text-gray-600 dark:text-gray-300">
        You are about to delete the grade for
        <strong class="text-gray-900 dark:text-gray-100">
          {{ deletingGrade?.course?.name || 'N/A' }}
        </strong>
        ({{ deletingGrade?.grade_letter || '-' }} / {{ deletingGrade?.grade_point || '-' }}).
        It will be hidden from the transcript and student portal but can be restored later.
      </p>

      <form @submit.prevent="submitGradeDelete" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
            Reason for deletion <span class="text-red-500">*</span>
          </label>
          <textarea
            v-model="gradeDeleteForm.delete_reason"
            rows="3"
            required
            class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
            placeholder="Explain why this grade is being deleted..."
          ></textarea>
          <p v-if="gradeDeleteForm.errors.delete_reason" class="text-xs text-red-500 mt-1">
            {{ gradeDeleteForm.errors.delete_reason }}
          </p>
        </div>

        <div class="flex justify-end space-x-3 pt-2">
          <button
            type="button"
            @click="closeGradeDeleteModal"
            class="px-5 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="gradeDeleteForm.processing"
            class="px-5 py-2 rounded-lg bg-red-600 text-white font-semibold shadow hover:bg-red-700 transition disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <span v-if="gradeDeleteForm.processing" class="animate-spin mr-2 inline-block">
              <svg class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
              </svg>
            </span>
            Delete Grade
          </button>
        </div>
      </form>
    </div>
  </Modal>
</template>
