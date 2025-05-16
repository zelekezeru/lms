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

const weightForm = useForm({
    name: "",
    point: "",
    description: "",
    course_id: props.course.id,
    section_id: props.section.id,
    semester_id: props.semester.id,
});

const createWeight = ref(false);

const addWeight = () => {
    // Convert current weights to numbers and sum them
    const totalWeight = props.weights.reduce(
        (sum, weight) => sum + parseFloat(weight.point || 0), 
        0
    );

    const newWeight = parseFloat(weightForm.point || 0);

    // Check if the new total exceeds 100
    if (totalWeight + newWeight > 100) {
        Swal.fire(
            "Error",
            "Total weight cannot exceed 100.",
            "error"
        );
        return;
    }

    // Submit if everything is okay
    weightForm.post(
        route("weights.store", {
            redirectTo: route('assessments.section_course', {
                course: props.course.id,
                section: props.section.id
            }),
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

// Checkif weight is full
const isWeightFull = computed(() => {
  const total = props.weights.reduce((sum, weight) => sum + parseFloat(weight.point || 0), 0);
  return total >= 100;
});

</script>

<template>
    
                    <!-- Weights Panel -->
    <div class="flex items-center justify-between mb-4">
        <h2
            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
        >
            Assessment Weights
        </h2>
        <div 
            v-if="(!props.section.grades || props.section.grades.filter(grade => grade.course_id === props.course.id).length === 0) && !isWeightFull"
            >
            <button
                @click="createWeight = !createWeight"
                class="flex items-center space-x-6 text-indigo-600 hover:text-indigo-800 transition"
            >
                <PlusCircleIcon class="w-8 h-8" />
                <span class="hidden sm:inline">Add Weight</span>
            </button>
        </div>

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
</template>
