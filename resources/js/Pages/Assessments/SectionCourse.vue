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

// Tabs
const tabs = [
    { key: "details", label: "Details", icon: CogIcon },
    { key: "results", label: "results", icon: DocumentTextIcon },
    { key: "weights", label: "weights", icon: PresentationChartBarIcon },
    { key: "grades", label: "Grades", icon: CheckBadgeIcon },
];

// States
const students = ref({
    data: [],
    meta: {
        current_page: 1,
        per_page: 10,
    },
});

const weightForm = useForm({
    name: "",
    point: "",
    description: "",
    course_id: props.course.id,
    section_id: props.section.id,
    semester_id: props.semester.id,
});

const selectedTab = ref("details");
const createResult = ref(false);
const createWeight = ref(false);

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

const addWeight = () => {
    weightForm.post(
        route("weights.store", {
            redirectTo: route('assessments.section_course',{course: props.course.id, section: props.section.id}),
            params: { section: props.section.id, course: props.course.id },
        }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Added!",
                    "Weight added successfully.",
                    "success"
                );
                createWeight.value = false;
                weightForm.reset();
            },
        }
    );
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ section.name }} - {{ course.name }} Course Assessments
            </h1>

            <nav class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700" >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    :class="[
                        'flex items-center px-4 py-2 space-x-2 text-sm font-medium transition',
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
                    ]"
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700" >

                <!-- Details Panel -->

                <div v-show="selectedTab === 'details'" class="grid grid-cols-2 gap-2">

                    <!-- Section Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Section</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.name }}
                        </span>
                    </div>

                    <!-- Program name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Program name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.program.name }}
                        </span>
                    </div>

                    <!-- Course Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Course</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ course.name }}
                        </span>
                    </div>

                    <!-- Instructor Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Instructor</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ instructor.user.name }}
                        </span>
                    </div>

                    <!-- Credit Hours  -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Credit Hours</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ course.credit_hours }}
                        </span>
                    </div>                        
                </div>
                

                <!-- Results Panel -->

                <div v-show="selectedTab === 'results'">
                    <div class="flex items-center justify-center mb-2">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Assessment Results
                        </h2>
                    </div>
                    <!-- Assesment  Results List -->
                    <div class="overflow-x-auto">
                    
                        <div
                        class="mt-4 border-t border-b border-gray-300 dark:border-gray-600 pt-2 pb-4"
                        >
                            <!-- Section Students list -->
                            <div class="overflow-x-auto">
                                <div v-if="selectedTab === 'results'" class="overflow-x-auto mt-6">
                                    <table class="min-w-full divide-y divide-gray-200 border rounded shadow-sm bg-white dark:bg-gray-900">
                                        <thead class="bg-gray-100 dark:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                            <tr>
                                                <th class="px-4 py-2 text-left">#</th>
                                                <th class="px-4 py-2 text-left">Student</th>
                                                <th v-for="weight in props.weights" :key="weight.id" class="px-4 py-2 text-center">
                                                    <div class="flex items-center justify-between">
                                                        <span>{{ weight.name }} ({{ weight.point }}pt)</span>
                                                        <button
                                                            v-if="!activeWeightId"
                                                            @click="activateWeight(weight.id)"
                                                            class="ml-2 text-xs text-white bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded"
                                                        >
                                                            <PencilIcon class="w-5 h-5" />
                                                        </button>
                                                    </div>
                                                </th>
                                                <th class="px-4 py-2 text-left">Total ({{ sumOfWeightPoints }}pt)</th>
                                                <th class="px-4 py-2 text-left">Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <!-- Section Students itteration -->
                                            <tr
                                                v-for="(student, index) in section.students"
                                                :key="student.id"
                                                class="border-t border-gray-200 dark:border-gray-700"
                                            >
                                                <td class="px-4 py-2 border-gray-300 dark:border-gray-600">{{ index + 1 }}</td>
                                                <td class="px-4 py-2">{{ student.student_name }} {{ student.father_name }}</td>
                                                
                                                <!-- Section Course Weights -->
                                                <td
                                                    v-for="weight in props.weights"
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
                                                        <span v-if="weight.results.some(result => result.student_id === student.id)" class="text-gray-900 dark:text-gray-100">
                                                            {{ weight.results.find(result => result.student_id === student.id)?.point }}
                                                        </span>
                                                        <span v-else class="text-gray-400">N/A</span>
                                                    </span>
                                                </td>

                                                <!-- Total Points Column -->
                                                <td class="px-4 py-2 border-gray-300 dark:border-gray-600">
                                                    {{ getStudentTotalPoints(student.id) }}

                                                </td>

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

                            </div>
                        </div>
                    </div>
                </div>


                        <!-- Weights Panel -->
                <div v-show="selectedTab === 'weights'">
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Assessment Weights
                        </h2>
                        <button
                            @click="createWeight = !createWeight"
                            class="flex items-center space-x-6 text-indigo-600 hover:text-indigo-800 transition"
                        >
                            <PlusCircleIcon class="w-8 h-8" />
                            <span class="hidden sm:inline"
                                >Add Weight</span
                            >
                        </button>
                    </div>
                    <!--  weights List -->
                    <div class="overflow-x-auto">
                        <div
                            class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
                        >
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                                >
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-700">
                                            <th
                                                class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                Weight
                                            </th>
                                            <th
                                                class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                Point ( {{ sumOfWeightPoints }}/ 100%)
                                            </th>
                                            <th
                                                class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                            >
                                                Description
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                weight, index
                                            ) in weights"
                                            :key="weight.id"
                                            :class="
                                                index % 2 === 0
                                                    ? 'bg-white dark:bg-gray-800'
                                                    : 'bg-gray-50 dark:bg-gray-700'
                                            "
                                            class="border-b border-gray-300 dark:border-gray-600"
                                        >
                                            <td
                                                class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                {{ weight.name }}
                                            </td>
                                            <td
                                                class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                {{ weight.point }}
                                            </td>
                                            <td
                                                class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                            >
                                                {{ weight.description }}
                                            </td>
                                        </tr>

                                        <!-- Create New weight Row -->
                                        <transition
                                            enter-active-class="transition duration-300 ease-out"
                                            enter-from-class="opacity-0 -translate-y-2"
                                            enter-to-class="opacity-100 translate-y-0"
                                            leave-active-class="transition duration-200 ease-in"
                                            leave-from-class="opacity-100 translate-y-0"
                                            leave-to-class="opacity-0 -translate-y-2"
                                        >
                                            <tr
                                                v-if="createWeight"
                                                class="bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600"
                                            >
                                                <TextInput
                                                    v-model="weightForm.course_id"
                                                    type="text"
                                                    :value="props.course.id"
                                                    readonly hidden 
                                                />
                                                <TextInput
                                                    v-model="weightForm.section_id"
                                                    type="text"
                                                    :value="props.section.id"
                                                    readonly hidden 
                                                />
                                                <TextInput
                                                    v-model="weightForm.semester_id"
                                                    type="text"
                                                    :value="props.semester.id"
                                                    readonly hidden 
                                                />

                                                <td class="px-4 py-2">
                                                    <TextInput
                                                        v-model="
                                                            weightForm.name
                                                        "
                                                        type="text"
                                                        placeholder="Title"
                                                        class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                                    />
                                                </td>                                                                                                
                                                
                                                <td class="px-3 py-2">
                                                    <TextInput
                                                        v-model="
                                                            weightForm.point
                                                        "
                                                        type="number"
                                                        placeholder="Weight Point"
                                                        min="0"
                                                        class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                                    />
                                                </td>                                                                                                
                                                
                                                <td class="px-5 py-2">
                                                    <div
                                                        class="flex items-center space-x-6"
                                                    >
                                                        <TextInput
                                                            v-model="
                                                                weightForm.description
                                                            "
                                                            type="text"
                                                            placeholder="Description"
                                                            class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                                        />
                                                        <PrimaryButton
                                                            class="px-4 py-1 h-9 bg-green-500 text-white rounded-md hover:bg-green-600"
                                                            @click="addWeight"
                                                        > 
                                                            Save
                                                        </PrimaryButton>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                        </transition>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grades Panel -->
                <div v-show="selectedTab === 'grades'" class="grid grid-cols-2 gap-2">
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Grades</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.grades }}
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Actions</span
                        >
                        <Link
                            :href="route('grades.create', { section: section.id })"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <PlusCircleIcon class="w-5 h-5 mr-2" />
                            Add Grade
                        </Link>
                    </div>
                </div>

                
            </div>  
        </div>
    </AppLayout>
</template>
