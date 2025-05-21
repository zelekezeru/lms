<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

// Props
const props = defineProps({
    
});

// States
const activeWeightId = ref(null);
const resultForm = ref({});

// Total weight
const sumOfWeightPoints = computed(() => {
    return props.weights.reduce((sum, weight) => sum + (parseFloat(weight.point) || 0), 0);
});

// Activate weight
const activateWeight = (weightId) => {
    activeWeightId.value = weightId;
    if (!resultForm.value[weightId]) {
        resultForm.value[weightId] = {};
    }

    setTimeout(() => {
        const column = document.querySelector(`[data-weight-id="${weightId}"]`);
        if (column) column.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 100);
};

// Calculate total for single student
const getStudentTotalPoints = () => {
    let total = 0;
    props.weights.forEach(weight => {
        if (activeWeightId.value === weight.id) {
            const val = parseFloat(resultForm.value[weight.id]?.[props.student.id]);
            if (!isNaN(val)) total += val;
        } else {
            const result = weight.results.find(r => r.student_id === props.student.id);
            if (result) {
                const val = parseFloat(result.point);
                if (!isNaN(val)) total += val;
            }
        }
    });
    return total.toFixed(2);
};

const getStudentGradeLetter = () => {
    const total = parseFloat(getStudentTotalPoints());
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

const getResultPoint = (weight) => {
    const result = weight.results?.find(r => r.student_id === props.student.id);
    return result ? result.point : null;
};

const getResultValue = (weightId) => {
    if (resultForm.value[weightId]?.[props.student.id] !== undefined) {
        return resultForm.value[weightId][props.student.id];
    }

    const weight = props.weights.find(w => w.id === weightId);
    if (weight) {
        const existing = weight.results.find(r => r.student_id === props.student.id);
        return existing ? existing.point : '';
    }

    return '';
};

const setResultValue = (weightId, value) => {
    if (!resultForm.value[weightId]) {
        resultForm.value[weightId] = {};
    }
    resultForm.value[weightId][props.student.id] = value;
};

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

    const value = parseFloat(resultForm.value[weightId][props.student.id]);

    if (isNaN(value) || value < 0 || value > weight.point) {
        Swal.fire({
            icon: "error",
            title: "Invalid Input",
            text: `Enter a valid score between 0 and ${weight.point}.`,
        });
        return;
    }

    const results = [{
        weight_id: weightId,
        student_id: props.student.id,
        point: value,
    }];

    router.post(route('results.store'), { results }, {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Saved!",
                text: `Result saved for ${weight.name}.`,
                timer: 2000,
                showConfirmButton: false,
            });
            activeWeightId.value = null;
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Something went wrong while saving.",
            });
        }
    });
};
</script>

<template>
    <div class="space-y-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Results for {{ student.name }}</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weight</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Points</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="weight in weights" :key="weight.id" :data-weight-id="weight.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button @click="activateWeight(weight.id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ weight.name }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input
                                type="number"
                                :value="getResultValue(weight.id)"
                                @input="setResultValue(weight.id, $event.target.value)"
                                :disabled="activeWeightId !== weight.id"
                                :max="weight.point"
                                class="border border-gray-300 rounded-md p-2 w-full"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ getStudentTotalPoints() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ getStudentGradeLetter() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button @click.prevent="submitWeightResults" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Save Results
        </button>
    </div>
</template>