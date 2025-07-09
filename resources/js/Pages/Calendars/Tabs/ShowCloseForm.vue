<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import Select from "primevue/select";

const props = defineProps({
    studyModes: Array, // e.g. [{ id, name, activeSemester, semesters: [] }]
    setSemesterOf: {
        required: false,
        type: Number,
        default: null,
    },
    backToStatus: Function,
});

const selectedStudyMode = ref(null);

const form = useForm({
    approval: false,
    new_semester_id: null,
    study_mode_id: props.setSemesterOf ? props.setSemesterOf : null,
});

const selectedSemesters = computed(() => {
    return selectedStudyMode.value?.semesters || [];
});

watch(
    () => form.study_mode_id,
    (newVal) => {
        selectedStudyMode.value = props.studyModes.find(
            (studyMode) => studyMode.id == newVal
        );
    },
    { immediate: true }
);
const submit = () => {
    form.post(route("semesters.close"), {
        onSuccess: () => {
            form.reset();
            selectedStudyMode.value = null;
            props.backToStatus();
        },
    });
};
</script>

<template>
    <div
        class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-blue-100 dark:border-gray-700 mt-10"
    >
        <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-200 mb-2">
            Close & Set New Active Semester
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">
            Select a study mode and choose a semester to activate. This action
            will close the current active semester and set a new one.
        </p>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Study Mode Select -->
            <div>
                <label
                    class="block mb-2 font-medium text-gray-900 dark:text-gray-200"
                >
                    Study Mode
                </label>
                <Select
                    v-model="form.study_mode_id"
                    :options="studyModes"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Choose Study Mode"
                    class="w-full"
                    panelClass="z-[1000]"
                />
            </div>

            <!-- If selected -->
            <div v-if="selectedStudyMode">
                <!-- Current Semester Info -->
                <div class="mt-2">
                    <p
                        class="text-lg font-medium text-gray-700 dark:text-gray-300"
                    >
                        Current Active Semester:
                        <span v-if="selectedStudyMode.activeSemester">
                            <span
                                class="text-green-600 dark:text-green-400 font-semibold"
                            >
                                🟢
                                {{ selectedStudyMode.activeSemester.name }}
                            </span>
                        </span>
                        <span v-else class="text-red-500 font-semibold">
                            🔴 None
                        </span>
                    </p>
                </div>

                <!-- New Semester Select -->
                <div class="mt-4">
                    <label
                        class="block mb-2 font-medium text-gray-900 dark:text-gray-200"
                    >
                        Select New Semester
                    </label>
                    <Select
                        v-model="form.new_semester_id"
                        :options="selectedSemesters"
                        optionLabel="name"
                        optionValue="id"
                        emptyMessage="There are no available inactive semesters for this study mode!"
                        filter
                        :filterFields="['level']"
                        filterPlaceholder="Filter by level... eg 1 , 2, 3"
                        placeholder="Choose Semester"
                        class="w-full"
                        panelClass="z-[1000]"
                    >
                        <template #option="slotProps">
                            <div class="flex flex-col">
                                <span class="font-medium">{{
                                    slotProps.option.name
                                }}</span>
                                <span class="text-sm text-gray-500"
                                    >Level: {{ slotProps.option.level }}</span
                                >
                            </div>
                        </template>
                    </Select>
                    <p
                        class="text-red-400"
                        v-if="selectedSemesters.length == 0"
                    >
                        No Semesters are available for this study mode please
                        <Link
                            class="text-blue-400 underline"
                            :href="route('semesters.index')"
                            >firs assign some semesters.</Link
                        >
                    </p>
                    <p
                        class="text-sm text-red-500 mt-1"
                        v-if="form.errors.new_semester_id"
                    >
                        {{ form.errors.new_semester_id }}
                    </p>
                </div>
            </div>

            <!-- Confirmation -->
            <div class="flex items-start gap-2">
                <input
                    id="approval"
                    type="checkbox"
                    v-model="form.approval"
                    class="mt-1"
                />
                <label
                    for="approval"
                    class="text-sm text-gray-800 dark:text-gray-200"
                >
                    I confirm I want to close the current semester and activate
                    the selected one.
                </label>
            </div>
            <p class="text-sm text-red-500" v-if="form.errors.approval">
                {{ form.errors.approval }}
            </p>

            <!-- Submit Button -->
            <div class="pt-4">
                <button
                    type="submit"
                    class="bg-gradient-to-r from-red-500 to-red-700 hover:from-red-600 hover:to-red-800 text-white px-6 py-2 rounded-xl shadow-md transition disabled:opacity-50"
                    :disabled="form.processing || !selectedStudyMode"
                >
                    Submit
                </button>
            </div>
        </form>
    </div>
</template>
