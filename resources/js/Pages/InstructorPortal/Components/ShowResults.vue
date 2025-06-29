<script setup>
import { defineProps, ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    PencilIcon
} from "@heroicons/vue/24/solid";

const props = defineProps({
    section: Object,
    course: Object,
    semester: Object,
    weights: Object,
    instructor: Object,
    studentsList: Object
});

const sumOfWeightPoints = computed(() => {
    return props.weights.reduce((sum, weight) => sum + (parseFloat(weight.point) || 0), 0);
});

const students = ref({ students: [], meta: { current_page: 1, per_page: 50 } });
const createResult = ref(false);
const activeWeightId = ref(null);
const resultForm = ref({});

const activateWeight = (weightId) => {
    activeWeightId.value = weightId;
    if (!resultForm.value[weightId]) resultForm.value[weightId] = {};
    setTimeout(() => {
        const column = document.querySelector(`[data-weight-id="${weightId}"] input`);
        if (column) column.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
        if (column) column.focus();
    }, 100);
};

const getStudentTotalPoints = (studentId) => {
    let total = 0;
    props.weights.forEach((weight) => {
        if (activeWeightId.value === weight.id) {
            const value = parseFloat(resultForm.value[weight.id]?.[studentId]);
            if (!isNaN(value)) total += value;
        } else {
            const result = weight.results.find(r => r.student_id === studentId);
            if (result) {
                const point = parseFloat(result.point);
                if (!isNaN(point)) total += point;
            }
        }
    });
    return total.toFixed(2);
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

const getResultValue = (studentId, weightId) => {
    // If value is already in the form (edited or entered), return it
    if (resultForm.value[weightId]?.[studentId] !== undefined) return resultForm.value[weightId][studentId];

    // Try to fetch from the latest data in props.weights (in case of updates)
    const weight = props.weights.find(w => w.id === weightId);
    if (weight && Array.isArray(weight.results)) {
        // If results are not loaded, fetch them (simulate fetch if needed)
        if (weight.results.length === 0) {
            // You can add an API call here if needed to fetch results for this weight
            // Example: fetchResultsForWeight(weightId);
        }
        const existing = weight.results.find(r => r.student_id === studentId);
        return existing ? existing.point : '';
    }
    return '';
};

const setResultValue = (studentId, weightId, value) => {
    if (!resultForm.value[weightId]) resultForm.value[weightId] = {};
    resultForm.value[weightId][studentId] = value;
};

const submitWeightResults = () => {
    const weightId = activeWeightId.value;
    const weight = props.weights.find(w => w.id === weightId);
    if (!weightId || !resultForm.value[weightId]) {
        Swal.fire({ icon: "warning", title: "No active weight", text: "Please activate a weight column before submitting." });
        return;
    }

    const rawResults = resultForm.value[weightId];
    const results = [];
    for (const [student_id, point] of Object.entries(rawResults)) {
        const numericPoint = parseFloat(point);
        if (isNaN(numericPoint) || numericPoint < 0 || numericPoint > weight.point) {
            Swal.fire({ icon: "error", title: "Invalid Input", text: `Enter valid points between 0 and ${weight.point}.` });
            return;
        }
        results.push({ instructor_id: props.instructor.id, weight_id: weightId, student_id: parseInt(student_id), point: numericPoint });
    }

    if (results.length === 0) {
        Swal.fire({ icon: "info", title: "Nothing to Submit", text: "No valid results entered." });
        return;
    }

    router.post(route('results.store'), { results }, {
        onSuccess: () => {
            resultForm.value[weightId] = {};
            activeWeightId.value = null;
            Swal.fire({ icon: "success", title: "Saved!", text: `Results saved.`, timer: 2000, showConfirmButton: false });
        },
        onError: () => {
            Swal.fire({ icon: "error", title: "Error", text: "Saving failed. Try again." });
        }
    });
};

const getResultPoint = (weight, studentId) => {
    const result = weight.results?.find(r => r.student_id === studentId);
    return result ? result.point : null;
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
                            <button v-if="!activeWeightId && (!props.section.grades || props.section.grades.filter(g => g.course_id === props.course.id).length === 0)"
                                @click="activateWeight(weight.id)"
                                class="ml-2 text-xs text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded">
                                <PencilIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-left">Total ({{ sumOfWeightPoints }}pt)</th>
                    <th class="px-6 py-2 text-left">Grade</th>
                </tr>
            </thead>
            <tbody>
                
                <tr v-for="(student, index) in studentsList" :key="student.id"
                    :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'"
                    class="border-b border-gray-300 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300">
                    <td class="px-4 py-2">{{ index + 1 }}</td>
                    <td class="px-4 py-2">{{ student.firstName }} {{ student.middleName }}</td>
                    <td v-for="weight in weights" :key="weight.id" :data-weight-id="weight.id"
                        class="px-4 py-2 text-center">
                        <span v-if="activeWeightId === weight.id">
                            <input type="number" step="0.01" min="0" max="100"
                                class="px-1 py-0.5 h-7 rounded-md dark:bg-gray-800 dark:text-gray-100 text-center"
                                :value="getResultValue(student.id, weight.id)"
                                @input="setResultValue(student.id, weight.id, $event.target.value)" />
                        </span>
                        <span v-else>
                            <span v-if="getResultPoint(weight, student.id) !== null" class="text-gray-900 dark:text-gray-100">
                                {{ getResultPoint(weight, student.id) }}
                            </span>
                            
                            <span v-else class="text-gray-400">N/A</span>
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ getStudentTotalPoints(student.id) }}</td>
                    <td class="px-4 py-2">
                        <!-- if result is null dont show grade -->
                        <span v-if="getStudentGradeLetter(student.id) !== null" :class="{
                            'text-green-600 font-semibold': getStudentGradeLetter(student.id).startsWith('A'),
                            'text-yellow-600 font-semibold': getStudentGradeLetter(student.id).startsWith('B'),
                            'text-orange-600 font-semibold': getStudentGradeLetter(student.id).startsWith('C'),
                            'text-red-600 font-semibold': getStudentGradeLetter(student.id).startsWith('D') || getStudentGradeLetter(student.id).startsWith('F')
                        }">
                            {{ getStudentGradeLetter(student.id) }}
                        </span>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="100%" class="px-4 py-3 text-center bg-gray-50 dark:bg-gray-800 border-t border-gray-300 dark:border-gray-600">
                        <button v-if="activeWeightId" @click="submitWeightResults"
                            class="text-sm px-4 py-2 rounded text-white bg-green-600 hover:bg-green-700">
                            Save Results``
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>
