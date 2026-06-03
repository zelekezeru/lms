<script setup>
import { defineProps, ref, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    PlusCircleIcon,
    TrashIcon,
    PencilSquareIcon,
    CheckIcon,
    XMarkIcon,
    LockClosedIcon,
    ExclamationTriangleIcon,
} from "@heroicons/vue/24/solid";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    section: { type: Object, required: true },
    course: { type: Object, required: true },
    semester: { type: Object, required: true },
    weights: { type: Array, required: true },
    instructor: { type: Object, required: true },
    redirectTo: { type: String, required: false },
    courseOffering: { type: Object, required: true },
});

// ─── Computed ─────────────────────────────────────────────────────────────────

const sumOfWeightPoints = computed(() =>
    props.weights.reduce((sum, w) => sum + (parseFloat(w.point) || 0), 0)
);

const remainingPoints = computed(() => Math.max(0, 100 - sumOfWeightPoints.value));

const progressPercent = computed(() => Math.min(100, sumOfWeightPoints.value));

const isWeightFull = computed(() => sumOfWeightPoints.value >= 100);

const progressBarColor = computed(() => {
    if (sumOfWeightPoints.value === 100) return "bg-green-500";
    if (sumOfWeightPoints.value > 100) return "bg-red-500";
    return "bg-indigo-500";
});

// ─── Create Form ──────────────────────────────────────────────────────────────

const createWeight = ref(false);
const weightForm = useForm({
    name: "",
    point: "",
    description: "",
    course_id: props.course.id,
    section_id: props.section.id,
    semester_id: props.semester.id,
});

const newWeightPreviewTotal = computed(() => {
    const val = parseFloat(weightForm.point || 0);
    return sumOfWeightPoints.value + val;
});

const addWeight = () => {
    if (newWeightPreviewTotal.value > 100) {
        Swal.fire({
            icon: "error",
            title: "Exceeds 100%",
            text: `Adding ${weightForm.point}pt would bring the total to ${newWeightPreviewTotal.value}pt. Maximum is 100pt.`,
            confirmButtonColor: "#4f46e5",
        });
        return;
    }
    weightForm.post(
        route("weights.store", {
            redirectTo:
                props.redirectTo ??
                route("assessments.section_course", {
                    course: props.course.id,
                    section: props.section.id,
                }),
            params: { section: props.section.id, course: props.course.id },
        }),
        {
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "Added!",
                    text: "Assessment weight added successfully.",
                    timer: 1800,
                    showConfirmButton: false,
                });
                createWeight.value = false;
                weightForm.reset();
            },
        }
    );
};

// ─── Edit Form ────────────────────────────────────────────────────────────────

const editingWeightId = ref(null);
const editWeightForm = useForm({
    name: "",
    point: "",
    description: "",
    course_id: props.course.id,
    section_id: props.section.id,
    semester_id: props.semester.id,
});

const startEditWeight = (weight) => {
    if (weight.results_count > 0) {
        Swal.fire({
            icon: "warning",
            title: "Cannot Edit",
            html: `<p>This weight has <strong>${weight.results_count}</strong> student result(s) recorded against it.</p><p class="mt-2 text-sm text-gray-500">Delete the associated results first before modifying this weight.</p>`,
            confirmButtonColor: "#4f46e5",
        });
        return;
    }
    editingWeightId.value = weight.id;
    editWeightForm.name = weight.name;
    editWeightForm.point = weight.point;
    editWeightForm.description = weight.description;
};

const cancelEditWeight = () => {
    editingWeightId.value = null;
    editWeightForm.reset();
};

const editPreviewTotal = computed(() => {
    if (!editingWeightId.value) return sumOfWeightPoints.value;
    const others = props.weights
        .filter((w) => w.id !== editingWeightId.value)
        .reduce((s, w) => s + parseFloat(w.point || 0), 0);
    return others + parseFloat(editWeightForm.point || 0);
});

const updateWeight = (weightId) => {
    if (editPreviewTotal.value > 100) {
        Swal.fire({
            icon: "error",
            title: "Exceeds 100%",
            text: `This would bring the total to ${editPreviewTotal.value}pt. Maximum is 100pt.`,
            confirmButtonColor: "#4f46e5",
        });
        return;
    }
    editWeightForm.put(
        route("weights.update", {
            weight: weightId,
            redirectTo:
                props.redirectTo ??
                route("assessments.section_course", {
                    course: props.course.id,
                    section: props.section.id,
                }),
        }),
        {
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "Updated!",
                    timer: 1500,
                    showConfirmButton: false,
                });
                cancelEditWeight();
            },
        }
    );
};

// ─── Delete ───────────────────────────────────────────────────────────────────

const deleteWeight = (weight) => {
    if (weight.results_count > 0) {
        Swal.fire({
            icon: "warning",
            title: "Cannot Delete",
            html: `<p>This weight has <strong>${weight.results_count}</strong> student result(s) recorded against it.</p><p class="mt-2 text-sm text-gray-500">Delete the associated results first before removing this weight.</p>`,
            confirmButtonColor: "#4f46e5",
        });
        return;
    }

    Swal.fire({
        title: "Delete Weight?",
        html: `<p>Are you sure you want to delete <strong>${weight.name}</strong> (${weight.point}pt)?</p><p class="text-sm text-gray-500 mt-2">This action cannot be undone.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, delete it",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("weights.destroy", { weight: weight.id }), {
                onSuccess: () =>
                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        timer: 1500,
                        showConfirmButton: false,
                    }),
                onError: (errors) =>
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: Object.values(errors)[0] ?? "Could not delete weight.",
                    }),
            });
        }
    });
};
</script>

<template>
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                Assessment Weights
                <span v-if="courseOffering.isGradeSubmissionApproved || courseOffering.grade_submission_status === 'approved'"
                    class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 px-2 py-0.5 rounded-full font-medium">
                    <LockClosedIcon class="w-3 h-3" />
                    Locked
                </span>
            </h2>
            <button
                v-if="!isWeightFull && courseOffering.completed == 0 && courseOffering.grade_submission_status !== 'approved'"
                @click="createWeight = !createWeight"
                class="flex items-center gap-2 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg shadow transition-all duration-200 font-medium"
            >
                <PlusCircleIcon class="w-5 h-5" />
                Add Weight
            </button>
        </div>

        <!-- Progress Bar -->
        <div class="space-y-1">
            <div class="flex justify-between text-xs font-medium text-gray-500 dark:text-gray-400">
                <span>Total Weight</span>
                <span :class="sumOfWeightPoints > 100 ? 'text-red-600' : sumOfWeightPoints === 100 ? 'text-green-600' : 'text-indigo-600'" class="font-bold">
                    {{ sumOfWeightPoints }} / 100pt
                </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                <div
                    class="h-2.5 rounded-full transition-all duration-500 ease-out"
                    :class="progressBarColor"
                    :style="{ width: progressPercent + '%' }"
                ></div>
            </div>
            <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500">
                <span v-if="remainingPoints > 0">{{ remainingPoints }}pt remaining to allocate</span>
                <span v-else-if="sumOfWeightPoints === 100" class="text-green-600 dark:text-green-400 font-medium">✓ Full allocation reached</span>
                <span v-else class="text-red-600 font-medium">⚠ Over-allocated by {{ sumOfWeightPoints - 100 }}pt</span>
            </div>
        </div>

        <!-- Create New Weight Row (animated) -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-3 scale-98"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-3"
        >
            <div v-if="createWeight" class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 rounded-xl p-4 space-y-3">
                <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">New Assessment Weight</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">Title *</label>
                        <TextInput v-model="weightForm.name" type="text" placeholder="e.g. Midterm Exam" class="w-full h-9" />
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">Weight Point (0–100) *</label>
                        <div class="relative">
                            <TextInput v-model="weightForm.point" type="number" placeholder="e.g. 30" min="0" max="100" class="w-full h-9 pr-12" />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">
                                → {{ newWeightPreviewTotal }}pt
                            </span>
                        </div>
                        <p v-if="newWeightPreviewTotal > 100" class="text-xs text-red-600 mt-1 flex items-center gap-1">
                            <ExclamationTriangleIcon class="w-3 h-3" />Exceeds 100pt!
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">Description</label>
                        <TextInput v-model="weightForm.description" type="text" placeholder="Optional description" class="w-full h-9" />
                    </div>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <button
                        @click="addWeight"
                        :disabled="newWeightPreviewTotal > 100 || !weightForm.name || !weightForm.point"
                        class="flex items-center gap-1.5 px-4 py-1.5 bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm rounded-lg shadow transition-all duration-200 font-medium"
                    >
                        <CheckIcon class="w-4 h-4" /> Save Weight
                    </button>
                    <button @click="createWeight = false" type="button" class="flex items-center gap-1.5 px-4 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg transition-all duration-200">
                        <XMarkIcon class="w-4 h-4" /> Cancel
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Weights Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/60 text-left">
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-36">Point</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28 text-center">Results</th>
                        <th v-if="courseOffering.completed == 0 && courseOffering.grade_submission_status !== 'approved'" class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider w-36 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr v-if="weights.length === 0">
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                            No assessment weights defined yet. Click "Add Weight" to create one.
                        </td>
                    </tr>
                    <tr
                        v-for="(weight, index) in weights"
                        :key="weight.id"
                        :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/50 dark:bg-gray-800/50'"
                        class="transition duration-150 hover:bg-indigo-50/40 dark:hover:bg-indigo-900/10"
                    >
                        <!-- Name Cell -->
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                            <TextInput v-if="editingWeightId === weight.id" v-model="editWeightForm.name" type="text" class="w-full h-8 text-sm" />
                            <span v-else class="font-medium">{{ weight.name }}</span>
                        </td>

                        <!-- Point Cell -->
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                            <div v-if="editingWeightId === weight.id" class="space-y-1">
                                <TextInput v-model="editWeightForm.point" type="number" min="0" max="100" class="w-full h-8 text-sm" />
                                <p v-if="editPreviewTotal > 100" class="text-xs text-red-600 flex items-center gap-1">
                                    <ExclamationTriangleIcon class="w-3 h-3" /> Would be {{ editPreviewTotal }}pt
                                </p>
                            </div>
                            <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                {{ weight.point }}pt
                            </span>
                        </td>

                        <!-- Description Cell -->
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                            <TextInput v-if="editingWeightId === weight.id" v-model="editWeightForm.description" type="text" class="w-full h-8 text-sm" />
                            <span v-else>{{ weight.description || '—' }}</span>
                        </td>

                        <!-- Results Count -->
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full"
                                :class="weight.results_count > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'">
                                {{ weight.results_count ?? 0 }} student{{ (weight.results_count ?? 0) !== 1 ? 's' : '' }}
                            </span>
                        </td>

                        <!-- Actions Cell -->
                        <td v-if="courseOffering.completed == 0 && courseOffering.grade_submission_status !== 'approved'" class="px-4 py-3">
                            <!-- Editing actions -->
                            <div v-if="editingWeightId === weight.id" class="flex items-center gap-2 justify-center">
                                <button
                                    @click="updateWeight(weight.id)"
                                    :disabled="editPreviewTotal > 100"
                                    class="flex items-center gap-1 px-3 py-1 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-xs rounded-lg transition-all duration-200 font-medium"
                                >
                                    <CheckIcon class="w-3.5 h-3.5" /> Save
                                </button>
                                <button @click="cancelEditWeight" type="button" class="flex items-center gap-1 px-3 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs rounded-lg transition-all duration-200">
                                    <XMarkIcon class="w-3.5 h-3.5" /> Cancel
                                </button>
                            </div>
                            <!-- Default actions -->
                            <div v-else class="flex items-center gap-2 justify-center">
                                <button
                                    @click="startEditWeight(weight)"
                                    :class="weight.results_count > 0 ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'"
                                    class="flex items-center gap-1 px-3 py-1 text-white text-xs rounded-lg shadow-sm transition-all duration-200 font-medium"
                                    :title="weight.results_count > 0 ? 'Cannot edit: student results exist' : 'Edit weight'"
                                >
                                    <PencilSquareIcon class="w-3.5 h-3.5" /> Edit
                                </button>
                                <button
                                    @click="deleteWeight(weight)"
                                    :class="weight.results_count > 0 ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-red-600 hover:bg-red-700'"
                                    class="flex items-center gap-1 px-3 py-1 text-white text-xs rounded-lg shadow-sm transition-all duration-200 font-medium"
                                    :title="weight.results_count > 0 ? 'Cannot delete: student results exist' : 'Delete weight'"
                                >
                                    <TrashIcon class="w-3.5 h-3.5" />
                                    <span v-if="weight.results_count > 0" class="flex items-center gap-1">
                                        <LockClosedIcon class="w-3 h-3" /> Locked
                                    </span>
                                    <span v-else>Delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 dark:bg-gray-700/60 border-t border-gray-200 dark:border-gray-700">
                        <td class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">Total</td>
                        <td class="px-4 py-2">
                            <span class="font-bold text-sm"
                                :class="sumOfWeightPoints === 100 ? 'text-green-600 dark:text-green-400' : sumOfWeightPoints > 100 ? 'text-red-600' : 'text-indigo-600 dark:text-indigo-400'">
                                {{ sumOfWeightPoints }} / 100pt
                            </span>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>
