<script setup>
import { defineProps, ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    section: Object,
    course: Object,
    semester: Object,
    weights: Array,
    instructor: Object,
    studentsList: Array,
    studentResults: Object,
});

const sumOfWeightPoints = computed(() => {
    return props.weights.reduce(
        (sum, weight) => sum + (parseFloat(weight.point) || 0),
        0
    );
});

const activeWeightId = ref(null);
const resultForm = ref({});

// Initialize resultForm with existing points
onMounted(() => {
    props.studentsList.forEach((student) => {
        resultForm.value[student.id] = {};
        props.weights.forEach((weight) => {
            const existing = props.studentResults[student.id]?.[weight.id];
            resultForm.value[student.id][weight.id] = existing?.point ?? null;
        });
    });
});

const activateWeight = (weightId) => {
    activeWeightId.value = weightId;
    // Focus first input
    setTimeout(() => {
        const input = document.querySelector(
            `[data-weight-id="${weightId}"] input`
        );
        if (input) {
            input.scrollIntoView({
                behavior: "smooth",
                block: "center",
                inline: "center",
            });
            input.focus();
        }
    }, 100);
};

const getStudentResultPoint = (studentId, weightId) => {
    return props.studentResults[studentId]?.[weightId]?.point ?? null;
};

const getStudentTotalPoints = (studentId) => {
    let total = 0;
    props.weights.forEach((weight) => {
        let point = resultForm.value[studentId]?.[weight.id];
        if (weight.id !== activeWeightId.value) {
            point = getStudentResultPoint(studentId, weight.id);
        }
        total += parseFloat(point) || 0;
    });
    return total.toFixed(2);
};

const getStudentGradeLetter = (studentId) => {
    const total = parseFloat(getStudentTotalPoints(studentId));
    if (isNaN(total)) return null;
    if (total >= 94) return "A (4.0)";
    if (total >= 90) return "A- (3.7)";
    if (total >= 87) return "B+ (3.3)";
    if (total >= 84) return "B (3.0)";
    if (total >= 80) return "B- (2.7)";
    if (total >= 77) return "C+ (2.3)";
    if (total >= 74) return "C (2.0)";
    if (total >= 70) return "C- (1.7)";
    if (total >= 67) return "D+ (1.3)";
    if (total >= 64) return "D (1.0)";
    if (total >= 60) return "D- (0.7)";
    return "F (0.0)";
};

const submitWeightResults = () => {
    const weightId = activeWeightId.value;
    if (!weightId) {
        Swal.fire({
            icon: "warning",
            title: "No active weight",
            text: "Activate a weight column first.",
        });
        return;
    }

    const weight = props.weights.find((w) => w.id === weightId);
    const results = [];

    for (const student of props.studentsList) {
        const point = parseFloat(resultForm.value[student.id][weightId]);
        if (isNaN(point) || point < 0 || point > weight.point) {
            Swal.fire({
                icon: "error",
                title: "Invalid input",
                text: `Points for ${student.firstName} must be between 0 and ${weight.point}`,
            });
            return;
        }
        results.push({
            instructor_id: props.instructor.id,
            weight_id: weightId,
            student_id: student.id,
            point,
        });
    }

    router.post(
        route("results.store"),
        { results },
        {
            onSuccess: () => {
                activeWeightId.value = null;
                Swal.fire({
                    icon: "success",
                    title: "Saved!",
                    timer: 2000,
                    showConfirmButton: false,
                });
            },
            onError: () => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to save results.",
                });
            },
        }
    );
};
</script>

<template>
    <div class="overflow-x-auto">
        <table
            class="min-w-full divide-y divide-gray-200 border rounded shadow-sm bg-white dark:bg-gray-900"
        >
            <thead
                class="bg-gray-100 dark:bg-gray-800 text-md font-semibold text-gray-700 dark:text-gray-200"
            >
                <tr>
                    <th class="px-4 py-2 text-left">#</th>
                    <th class="px-4 py-2 text-left">Student</th>
                    <th
                        v-for="weight in props.weights"
                        :key="weight.id"
                        class="px-4 py-2 text-center"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                >{{ weight.name }} ({{ weight.point }}pt)</span
                            >
                            <button
                                v-if="!activeWeightId"
                                @click="activateWeight(weight.id)"
                                class="ml-2 text-xs text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded"
                            >
                                <PencilIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </th>
                    <th class="px-4 py-2 text-left">
                        Total ({{ sumOfWeightPoints }}pt)
                    </th>
                    <th class="px-6 py-2 text-left">Grade</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(student, index) in props.studentsList"
                    :key="student.id"
                    :class="
                        index % 2 === 0
                            ? 'bg-white dark:bg-gray-800'
                            : 'bg-gray-50 dark:bg-gray-700'
                    "
                    class="border-b border-gray-300 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300"
                >
                    <td class="px-4 py-2">{{ index + 1 }}</td>
                    <td class="px-4 py-2">
                        {{ student.first_name }} {{ student.middle_name }}
                    </td>
                    <td
                        v-for="weight in props.weights"
                        :key="weight.id"
                        :data-weight-id="weight.id"
                        class="px-4 py-2 text-center"
                    >
                        <span v-if="activeWeightId === weight.id">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                :max="weight.point"
                                class="px-1 py-0.5 h-7 rounded-md dark:bg-gray-800 dark:text-gray-100 text-center"
                                v-model.number="
                                    resultForm[student.id][weight.id]
                                "
                            />
                        </span>
                        <span v-else>
                            <span
                                v-if="
                                    getStudentResultPoint(
                                        student.id,
                                        weight.id
                                    ) !== null
                                "
                                class="text-gray-900 dark:text-gray-100"
                            >
                                {{
                                    getStudentResultPoint(student.id, weight.id)
                                }}
                            </span>
                            <span v-else class="text-gray-400">N/A</span>
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        {{ getStudentTotalPoints(student.id) }}
                    </td>
                    <td class="px-4 py-2">
                        <span
                            v-if="getStudentGradeLetter(student.id)"
                            :class="{
                                'text-green-600 font-semibold':
                                    getStudentGradeLetter(
                                        student.id
                                    ).startsWith('A'),
                                'text-yellow-600 font-semibold':
                                    getStudentGradeLetter(
                                        student.id
                                    ).startsWith('B'),
                                'text-orange-600 font-semibold':
                                    getStudentGradeLetter(
                                        student.id
                                    ).startsWith('C'),
                                'text-red-600 font-semibold':
                                    getStudentGradeLetter(
                                        student.id
                                    ).startsWith('D') ||
                                    getStudentGradeLetter(
                                        student.id
                                    ).startsWith('F'),
                            }"
                        >
                            {{ getStudentGradeLetter(student.id) }}
                        </span>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td
                        colspan="100%"
                        class="px-4 py-3 text-center bg-gray-50 dark:bg-gray-800 border-t border-gray-300 dark:border-gray-600"
                    >
                        <button
                            v-if="activeWeightId"
                            @click="submitWeightResults"
                            class="text-sm px-4 py-2 rounded text-white bg-green-600 hover:bg-green-700"
                        >
                            Save Results
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>
