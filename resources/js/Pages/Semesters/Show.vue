<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref, watch } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { DatePicker, Listbox, MultiSelect } from "primevue";
import { CheckCircleIcon, XCircleIcon } from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
    semester: {
        type: Object,
        required: true,
    },
    studyModes: {
        type: Array,
        required: true,
    },
});

const selectedStudyModes = ref(
    props.semester.studyModes.map((studyMode) => studyMode.id)
);

const studyModeRelationsForm = useForm({
    study_modes: props.semester.studyModes.reduce((acc, m) => {
        acc[m.id] = {
            start_date: m.semester_start_date,
            end_date: m.semester_end_date,
        };

        return acc;
    }, {}),
});

watch(selectedStudyModes, (modes) => {
    // modes is a collection of selected study mode ids
    studyModeRelationsForm.study_modes = modes.reduce((acc, m) => {
        acc[m] = {
            start_date: studyModeRelationsForm.study_modes[m]
                ? studyModeRelationsForm.study_modes[m].start_date
                : props.semester.studyModes.find(
                      (studyMode) => studyMode.id == m
                  )
                ? props.semester.studyModes.find(
                      (studyMode) => studyMode.id == m
                  ).semester_start_date
                : props.semester.start_date,
            end_date: studyModeRelationsForm.study_modes[m]
                ? studyModeRelationsForm.study_modes[m].end_date
                : props.semester.studyModes.find(
                      (studyMode) => studyMode.id == m
                  )
                ? props.semester.studyModes.find(
                      (studyMode) => studyMode.id == m
                  ).semester_end_date
                : props.semester.end_date,
        };

        return acc;
    }, {});
});

const dateWithoutTimezone = (date) => {
    const tzoffset = date.getTimezoneOffset() * 60000; //offset in milliseconds
    const withoutTimezone = new Date(date.valueOf() - tzoffset)
        .toISOString()
        .slice(0, -1);
    return withoutTimezone;
};

const cancelChanges = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Revert My Changes!",
    }).then((result) => {
        if (result.isConfirmed) {
            studyModeRelationsForm.reset();
            selectedStudyModes.value = [];
        }
    });
};
const applyChanges = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "Your Changes Will Be Applied!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Apply My Changes!",
    }).then((result) => {
        if (result.isConfirmed) {
            studyModeRelationsForm.post(
                route("semesters.syncStudyModes", {
                    semester: props.semester.id,
                }),
                {
                    preserveState: false,
                    onSuccess: () => {
                        Swal.fire(
                            "Success!",
                            "The semester has been updated.",
                            "success"
                        );
                    },
                }
            );
        }
    });
};
// Delete function with SweetAlert confirmation
const deleteSemester = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("semesters.destroy", { semester: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The semester has been deleted.",
                        "success"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto p-6">
            <h1
                class="text-3xl font-bold text-center text-gray-900 dark:text-gray-100 mb-10"
            >
                {{ semester.name }} - {{ semester.year.name }}
            </h1>

            <!-- Semester Info Card -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow p-6 space-y-10"
            >
                <!-- Grid Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Semester Name</span
                        >
                        <div
                            class="mt-1 text-lg font-semibold text-gray-800 dark:text-gray-100"
                        >
                            {{ semester.name }}
                        </div>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Academic Year</span
                        >
                        <div
                            class="mt-1 text-lg font-semibold text-blue-600 hover:underline"
                        >
                            <Link
                                :href="
                                    route('years.show', {
                                        year: semester.year.id,
                                    })
                                "
                            >
                                {{ semester.year.name }}
                            </Link>
                        </div>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Status</span
                        >
                        <div
                            class="mt-1 text-lg font-semibold"
                            :class="
                                semester.status === 'Active'
                                    ? 'text-green-600'
                                    : 'text-red-600'
                            "
                        >
                            {{ semester.status }}
                        </div>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Start Date</span
                        >
                        <div
                            class="mt-1 text-lg font-semibold text-gray-800 dark:text-gray-100"
                        >
                            {{
                                new Date(
                                    semester.start_date
                                ).toLocaleDateString()
                            }}
                        </div>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >End Date</span
                        >
                        <div
                            class="mt-1 text-lg font-semibold text-gray-800 dark:text-gray-100"
                        >
                            {{
                                new Date(semester.end_date).toLocaleDateString()
                            }}
                        </div>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Approval</span
                        >
                        <div
                            class="mt-1 text-lg font-semibold"
                            :class="
                                semester.is_approved
                                    ? 'text-green-600'
                                    : 'text-red-600'
                            "
                        >
                            {{
                                semester.is_approved
                                    ? "Approved"
                                    : "Not Approved"
                            }}
                        </div>
                    </div>
                </div>

                <!-- Study Mode Application -->
                <div class="border-t pt-6 dark:border-gray-700">
                    <div class="space-y-10">
                        <!-- Listbox Section -->
                        <div>
                            <h2
                                class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100"
                            >
                                This semester is applied in...
                            </h2>
                            <Listbox
                                v-model="selectedStudyModes"
                                :options="studyModes"
                                checkmark
                                optionLabel="name"
                                option-value="id"
                                filter
                                multiple
                                placeholder="Select study modes"
                                class="w-full"
                            />
                        </div>

                        <!-- Duration Picker -->
                        <div>
                            <h2
                                class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100"
                            >
                                Pick duration in each study mode
                            </h2>

                            <div
                                v-if="selectedStudyModes.length === 0"
                                class="text-gray-500 italic dark:text-gray-400"
                            >
                                No study mode selected
                            </div>

                            <div v-else class="space-y-6">
                                <div
                                    v-for="studyMode in studyModes.filter((m) =>
                                        selectedStudyModes.includes(m.id)
                                    )"
                                    :key="studyMode.id"
                                    class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700"
                                >
                                    <h3
                                        class="text-base font-semibold mb-4 text-gray-800 dark:text-gray-100"
                                    >
                                        {{ studyMode.name }}
                                    </h3>

                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-2 gap-4"
                                    >
                                        <div>
                                            <label
                                                class="block text-sm text-gray-600 dark:text-gray-400 mb-1"
                                            >
                                                Start Date
                                            </label>
                                            <DatePicker
                                                :modelValue="
                                                    new Date(
                                                        studyModeRelationsForm.study_modes[
                                                            studyMode.id
                                                        ].start_date
                                                    )
                                                "
                                                @update:modelValue="
                                                    (value) => {
                                                        studyModeRelationsForm.study_modes[
                                                            studyMode.id
                                                        ].start_date =
                                                            dateWithoutTimezone(
                                                                value
                                                            );
                                                    }
                                                "
                                                show-icon
                                                class="w-full"
                                            />
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm text-gray-600 dark:text-gray-400 mb-1"
                                            >
                                                End Date
                                            </label>
                                            <DatePicker
                                                :modelValue="
                                                    new Date(
                                                        studyModeRelationsForm.study_modes[
                                                            studyMode.id
                                                        ].end_date
                                                    )
                                                "
                                                @update:modelValue="
                                                    (value) => {
                                                        studyModeRelationsForm.study_modes[
                                                            studyMode.id
                                                        ].end_date =
                                                            dateWithoutTimezone(
                                                                value
                                                            );
                                                    }
                                                "
                                                show-icon
                                                class="w-full"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button
                            @click="applyChanges"
                            class="inline-flex items-center px-5 mr-3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition duration-200"
                        >
                            <CheckCircleIcon class="w-5 h-5 mr-2 -ml-1" />
                            Apply Changes
                        </button>
                        <button
                            @click="cancelChanges"
                            class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-sm transition duration-200"
                        >
                            <XCircleIcon class="w-5 h-5 mr-2 -ml-1" />
                            Cancel Changes
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end pt-4 gap-5">
                    <Link
                        v-if="userCan('update-semesters')"
                        :href="
                            route('semesters.edit', { semester: semester.id })
                        "
                        class="inline-flex items-center space-x-2 text-blue-500 hover:text-blue-700"
                    >
                        <PencilIcon class="w-5 h-5" />
                        <span>Edit</span>
                    </Link>
                    <button
                        v-if="userCan('delete-semesters')"
                        @click="deleteSemester(semester.id)"
                        class="inline-flex items-center gap-2 text-red-600 hover:text-red-800 font-medium"
                    >
                        <TrashIcon class="w-5 h-5" />
                        <span>Delete Semester</span>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
