<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref, computed } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    PencilIcon, EyeIcon, XMarkIcon, CogIcon,
    PlusCircleIcon, DocumentTextIcon, PresentationChartBarIcon,
    CheckBadgeIcon, TrashIcon
} from "@heroicons/vue/24/solid";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Props
const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    course: {
        type: Object,
        required: true,
    },
    semester: {
        type: Object,
        required: true,
    },
    weights: {
        type: Array,
        required: true,
    },
    instructor: {
        type: Object,
        required: true,
    },
});

// Computed
const sumOfWeightPoints = computed(() => {
    return props.weights.reduce((sum, weight) => {
        return sum + (parseFloat(weight.point) || 0);
    }, 0);
});

// States
const students = ref({
    data: [],
    meta: {
        current_page: 1,
        per_page: 50,
    },
});

const createResult = ref(false);

// Result entry states
const activeWeightId = ref(null);

const resultForm = ref({});

// Activate a weight column
const activateWeight = (weightId) => {
    activeWeightId.value = weightId;
    if (!resultForm.value[weightId]) {
        resultForm.value[weightId] = {};
    }

    // Scroll to the active weight column
    setTimeout(() => {
        const column = document.querySelector(`[data-weight-id="${weightId}"]`);
        if (column) {
            column.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, 100);
};

//  Total Result of Student
const getStudentTotalPoints = (studentId) => {
    let total = 0;

    props.weights.forEach((weight) => {
        // If it's the active weight, use the input value (unsaved yet)
        if (activeWeightId.value === weight.id) {
            const value = parseFloat(resultForm.value[weight.id]?.[studentId]);
            if (!isNaN(value)) total += value;
        } else {
            // Otherwise use the saved result
            const result = weight.results.find(r => r.student_id === studentId);
            if (result) {
                const point = parseFloat(result.point);
                if (!isNaN(point)) total += point;
            }
        }
    });

    return total.toFixed(2); // Rounded to 2 decimal places
};

const getStudentGradeLetter = (studentId) => {
    const total = parseFloat(getStudentTotalPoints(studentId));

    if (isNaN(total)) return 'N/A';

    if (total >= 94) return 'A (4.0)';
    if (total >= 90) return 'A- (3.7)';
    if (total >= 87) return 'B+ (3.3)';
    if (total >= 84) return 'B (3.0)';
    if (total >= 80) return 'B- (2.7)';
    if (total >= 77) return 'C+ (2.3)';
    if (total >= 74) return 'C (2.0)';
    if (total >= 70) return 'C- (1.7)';
    if (total >= 67) return 'D+ (1.3)';
    if (total >= 64) return 'D (1.0)';
    if (total >= 60) return 'D- (0.7)';
    return 'F (0.0)';
};



// Get result value for an input
const getResultValue = (studentId, weightId) => {
    // Return the newly typed value if exists
    if (resultForm.value[weightId]?.[studentId] !== undefined) {
        return resultForm.value[weightId][studentId];
    }

    // Otherwise, return the existing result from weight.results
    const weight = props.weights.find(w => w.id === weightId);
    if (weight && weight.results) {
        const existing = weight.results.find(r => r.student_id === studentId);
        return existing ? existing.point : '';
    }

    return '';
};

// Set result value when user types
const setResultValue = (studentId, weightId, value) => {
    if (!resultForm.value[weightId]) {
        resultForm.value[weightId] = {};
    }
    resultForm.value[weightId][studentId] = value;
};

// Submit results for the active weight
const submitWeightResults = () => {
    const weightId = activeWeightId.value;
    const weight = props.weights.find(w => w.id === weightId);

    if (!weightId || !resultForm.value[weightId]) {
        Swal.fire({
            icon: "warning",
            title: "No active weight",
            text: "Please activate a weight column before submitting.",
        });
        return;
    }

    const rawResults = resultForm.value[weightId];
    
    const results = [];

    for (const [student_id, point] of Object.entries(rawResults)) {
        const numericPoint = parseFloat(point);

        if (isNaN(numericPoint) || numericPoint < 0 || numericPoint > weight.point) {
            Swal.fire({
                icon: "error",
                title: "Invalid Input",
                text: `Please enter a valid point between 0 and ${weight.point} for all students.`,
            });
            return;
        }

        results.push({
            weight_id: weightId,
            student_id: parseInt(student_id),
            point: numericPoint,
        });
    }

    if (results.length === 0) {
        Swal.fire({
            icon: "info",
            title: "Nothing to Submit",
            text: "You haven't entered any valid results.",
        });
        return;
    }

    router.post(route('results.store'), { results }, {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Saved!",
                text: `Results for "${weight.name}" saved successfully.`,
                timer: 2000,
                showConfirmButton: false,
            });
            activeWeightId.value = null;
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Something went wrong while saving. Please try again.",
            });
        }
    });
};

// Fetch student results
const getResultPoint = (weight, studentId) => {
  return weight.results?.find(result => result.student_id === studentId)?.point ?? null;
};

</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border rounded shadow-sm bg-white dark:bg-gray-900">
            <thead class="bg-gray-100 dark:bg-gray-800 text-md font-semibold text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left">#</th>
                    <th class="px-4 py-2 text-left">Student</th>
                    <th v-for="weight in props.weights" :key="weight.id" class="px-4 py-2 text-center">
                        <div class="flex items-center justify-between">
                            <span>{{ weight.name }} ({{ weight.point }}pt)</span>
                            <button
                                v-if="!activeWeightId && !props.section.grades || props.section.grades.filter(grade => grade.course_id === props.course.id).length === 0"
                                @click="activateWeight(weight.id)"
                                class="ml-2 text-xs text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded"
                            >
                                <PencilIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-left">Total ({{ sumOfWeightPoints }}pt)</th>
                    <th class="px-6 py-2 text-left">Grade</th>
                </tr>
            </thead>
            <tbody>
                <!-- Section Students Iteration -->
                <tr
                    v-for="(student, index) in section.students.sort((a, b) => a.first_name.localeCompare(b.first_name))"
                    :key="student.id"
                    :class="
                        index % 2 === 0
                            ? 'bg-white dark:bg-gray-800'
                            : 'bg-gray-50 dark:bg-gray-700'
                    "
                    class="border-b border-gray-300 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300"
                >
                    <td class="px-4 py-2 border-gray-300 dark:border-gray-600">
                        {{ index + 1 }}
                    </td>
                    <td class="px-4 py-2">
                        {{ student.first_name }} {{ student.middle_name }}
                    </td>

                    <!-- Section Course Weights -->
                    <td
                        v-for="weight in weights"
                        :key="weight.id"
                        class="px-4 py-2 text-center border-gray-300 dark:border-gray-600"
                    >
                        <!-- Editable Input for Active Weight -->
                        <span v-if="activeWeightId === weight.id">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                class="px-1 py-0.5 h-7 rounded-md dark:bg-gray-800 dark:text-gray-100 text-center"
                                :value="getResultValue(student.id, weight.id)"
                                @input="setResultValue(student.id, weight.id, $event.target.value)"
                            />
                        </span>

                        <!-- Result or N/A when not active -->
                        <span v-else>
                            <span v-if="getResultPoint(weight, student.id) !== null"
                                class="text-gray-900 dark:text-gray-100"
                            >
                                {{ getResultPoint(weight, student.id) }}
                            </span>
                            
                            <span v-else class="text-gray-400">N/A</span>
                        </span>

                    </td>

                    <!-- Total Points Column -->
                    <td class="px-4 py-2 border-gray-300 dark:border-gray-600">
                        {{ getStudentTotalPoints(student.id) }}
                    </td>

                    <!-- Grade Column -->
                    <td class="px-4 py-2 border-gray-300 dark:border-gray-600">
                        {{ getStudentGradeLetter(student.id) }}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                    <tr>
                        <td colspan="100%" class="px-4 py-3 text-center bg-gray-50 dark:bg-gray-800 border-t border-gray-300 dark:border-gray-600">
                        <div class="relative group inline-block">
                            <button
                                v-if="activeWeightId"
                                @click="submitWeightResults"
                                class="text-sm px-4 py-2 rounded text-white bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                            >
                                Save Results
                            </button>
                        </div>
                        </td>
                    </tr>
                </tfoot>
        </table>
    </div>
</template>
