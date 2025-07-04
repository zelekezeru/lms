<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { DatePicker, Listbox, Select } from "primevue";
import Swal from "sweetalert2";
import { ref, watch } from "vue";

const props = defineProps({
    form: Object,
    years: Array,
    studyModes: Array,
    semester: Object,
});

const selectedStudyModes = ref(
    props.semester
        ? props.semester.studyModes.map((studyMode) => studyMode.id)
        : []
);

const dateWithoutTimezone = (date) => {
    const tzoffset = date.getTimezoneOffset() * 60000; //offset in milliseconds
    const withoutTimezone = new Date(date.valueOf() - tzoffset)
        .toISOString()
        .slice(0, -1);
    return withoutTimezone;
};

watch(
    selectedStudyModes,
    (modes) => {
        // modes is a collection of selected study mode ids
        props.form.study_modes = modes.reduce((acc, m) => {
            console.log(m);

            console.log(
                props.semester.studyModes.find((studyMode) => studyMode.id == m)
                    .semester_start_date,
                props.semester.studyModes.find((studyMode) => studyMode.id == m)
                    .semester_end_date
            );

            acc[m] = {
                start_date: props.form.study_modes[m]
                    ? props.form.study_modes[m].start_date
                    : props.semester &&
                      props.semester.studyModes.find(
                          (studyMode) => studyMode.id == m
                      )
                    ? props.semester.studyModes.find(
                          (studyMode) => studyMode.id == m
                      ).semester_start_date
                    : props.form.start_date,
                end_date: props.form.study_modes[m]
                    ? props.form.study_modes[m].end_date
                    : props.semester &&
                      props.semester.studyModes.find(
                          (studyMode) => studyMode.id == m
                      )
                    ? props.semester.studyModes.find(
                          (studyMode) => studyMode.id == m
                      ).semester_end_date
                    : props.form.end_date,
            };

            return acc;
        }, {});
    },
    { immediate: true }
);

const emits = defineEmits(["submit"]);
</script>

<template>
    <form @submit.prevent="emits('submit')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel
                    for="name"
                    value="Name"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    placeholder="Name"
                    autocomplete="name"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.name"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <div>
                <InputLabel
                    for="level"
                    value="Level"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <TextInput
                    id="level"
                    type="number"
                    v-model="form.level"
                    required
                    placeholder="Level"
                    autofocus
                    autocomplete="level"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.level"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <div>
                <InputLabel for="year_id" value="Select Year" />
                <Select
                    id="year_id"
                    v-model="form.year_id"
                    :options="years"
                    placeholder="Select Year"
                    option-value="id"
                    option-label="name"
                    class="w-full px-3 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError :message="form.errors?.year_id" class="mt-2" />
            </div>
            <div>
                <InputLabel for="start_date" value="Start Date" />
                <DatePicker
                    dateFormat="dd-mm-yy"
                    showIcon
                    :modelValue="new Date(form.start_date)"
                    @update:modelValue="
                        (value) => {
                            form.start_date = dateWithoutTimezone(value);
                        }
                    "
                    placeholder="Select Start Date"
                />
                <InputError :message="form.errors?.start_date" class="mt-2" />
            </div>

            <div>
                <InputLabel for="end_date" value="End Date" />
                <DatePicker
                    :modelValue="new Date(form.end_date)"
                    @update:modelValue="
                        (value) => {
                            form.end_date = dateWithoutTimezone(value);
                        }
                    "
                    dateFormat="dd-mm-yy"
                    showIcon
                    placeholder="Select End Date"
                />
                <InputError :message="form.errors?.end_date" class="mt-2" />
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

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm text-gray-600 dark:text-gray-400 mb-1"
                                    >
                                        Start Date
                                    </label>
                                    <DatePicker
                                        date-format="dd-mm-yy"
                                        show-icon
                                        class="w-full"
                                        :modelValue="
                                            new Date(
                                                form.study_modes[
                                                    studyMode.id
                                                ].start_date
                                            )
                                        "
                                        @update:modelValue="
                                            (value) => {
                                                form.study_modes[
                                                    studyMode.id
                                                ].start_date =
                                                    dateWithoutTimezone(value);
                                            }
                                        "
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm text-gray-600 dark:text-gray-400 mb-1"
                                    >
                                        End Date
                                    </label>
                                    <DatePicker
                                        date-format="dd-mm-yy"
                                        show-icon
                                        class="w-full"
                                        :modelValue="
                                            new Date(
                                                form.study_modes[
                                                    studyMode.id
                                                ].end_date
                                            )
                                        "
                                        @update:modelValue="
                                            (value) => {
                                                form.study_modes[
                                                    studyMode.id
                                                ].end_date =
                                                    dateWithoutTimezone(value);
                                            }
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-6 flex justify-center">
            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <span v-if="!form.processing">{{
                    $t("common.save", "Submit")
                }}</span>
                <span v-else>{{ $t("common.loading", "Submitting...") }}</span>
            </button>
        </div>
    </form>
</template>
