<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, EyeIcon,XMarkIcon, CogIcon, PlusCircleIcon, DocumentTextIcon , PresentationChartBarIcon, CheckBadgeIcon, TrashIcon } from "@heroicons/vue/24/solid";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Define the props for the section with validation-related data
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
});

const tabs = [
    { key: "details", label: "Details", icon: CogIcon },
    { key: "results", label: "results", icon: DocumentTextIcon },
    { key: "weights", label: "weights", icon: PresentationChartBarIcon },
    { key: "grades", label: "Grades", icon: CheckBadgeIcon  },
];

// Initialize students with a default structure
const students = ref({
    data: [],
    meta: {
        current_page: 1,
        per_page: 10,
    },
});

const weightForm = useForm({
    name: "",
    weight_point: "",
    weight_description: "",
    course_id: props.course.id,
    section_id: props.section.id,
    semester_id: props.semester.id,
});

const selectedTab = ref("details");

const createWeight = ref(false);

const addWeight = () => {
    weightForm.post(
        route("weights.store"),
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
            onError: (errors) => {
                if (errors.status === 403) {
                    Swal.fire(
                        "Unauthorized!",
                        "You do not have permission to perform this action.",
                        "error"
                    );
                } else {
                    Swal.fire(
                        "Error!",
                        "An unexpected error occurred. Please try again.",
                        "error"
                    );
                }
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

                <div v-show="selectedTab === 'results'" class="grid grid-cols-2 gap-2">
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Results</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ section.results }}
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Actions</span
                        >
                        <Link
                            :href="route('results.create', { section: section.id })"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <PlusCircleIcon class="w-5 h-5 mr-2" />
                            Add Result
                        </Link>
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
                        <!-- Program  weights List -->
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
                                                    Point (100%)
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
                                                ) in section.weights"
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
                                                    {{ weight.weight_point }}
                                                </td>
                                                <td
                                                    class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                                >
                                                    {{ weight.weight_description }}
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
                                                        v-weight="weightForm.course_id"
                                                        type="text"
                                                        :value="props.course.id"
                                                        readonly hidden
                                                    />
                                                    <TextInput
                                                        v-weight="weightForm.section_id"
                                                        type="text"
                                                        :value="props.section.id"
                                                        readonly hidden
                                                    />
                                                    <TextInput
                                                        v-weight="weightForm.semester_id"
                                                        type="text"
                                                        :value="props.semester.id"
                                                        readonly hidden
                                                    />

                                                    <td class="px-4 py-2">
                                                        <TextInput
                                                            v-weight="
                                                                weightForm.name
                                                            "
                                                            type="text"
                                                            placeholder="Title"
                                                            class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                                        />
                                                    </td>                                                                                                
                                                    
                                                    <td class="px-3 py-2">
                                                        <TextInput
                                                            v-weight="
                                                                weightForm.weight_point
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
                                                                v-weight="
                                                                    weightForm.weight_description
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
