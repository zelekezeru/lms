<script setup>
import { defineProps, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Select } from "primevue";

const props = defineProps({
    program: { type: Object, required: true },
    studyModes: { type: Object, required: false },
});

const modeForm = useForm({
    program_id: props.program.id,
    study_mode_id: "",
    duration: "",
});

const assignModeToProgram = ref(false);

const assignMode = () => {
    modeForm.post(
        route("studyMode-program.assign", {
            redirectTo: route("programs.show", { program: props.program.id }),
        }),
        {
            onSuccess: () => {
                Swal.fire(
                    $t("programs.studymode.added_title"),
                    $t("programs.studymode.added_text"),
                    "success"
                );
                assignModeToProgram.value = false;
                modeForm.reset();
            },
        }
    );
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ $t('programs.studymode.name') }}
            </h2>
            <button
                @click="assignModeToProgram = !assignModeToProgram"
                class="flex items-center text-indigo-600 hover:text-indigo-800"
            >
                <PlusCircleIcon class="w-8 h-8" />
                <span class="hidden sm:inline"> {{ $t('programs.studymode.add') }}</span>
            </button>
        </div>
        <!-- Program Study Modes List -->
        <div class="overflow-x-auto">
            <div
                class="pt-4 pb-4 mt-8 border-b border-gray-300 dark:border-gray-600"
            >
                <div class="overflow-x-auto">
                    <table
                        class="w-full min-w-[500px] table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="px-4 py-2 text-sm font-medium text-left text-gray-700 border-r border-gray-300 w-96 dark:text-gray-200 dark:border-gray-600"
                                >
                                {{ $t('programs.studymode.title') }}
                                </th>
                                <th
                                    class="px-4 py-2 text-sm font-medium text-left text-gray-700 border-r border-gray-300 w-30 dark:text-gray-200 dark:border-gray-600"
                                >
                                {{ $t('programs.studymode.duration') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(mode, index) in program.studyModes"
                                :key="mode.id"
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                                class="border-b border-gray-300 dark:border-gray-600"
                            >
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 border-r border-gray-300 w-96 dark:text-gray-300 dark:border-gray-600"
                                >
                                    {{ mode.name }}
                                </td>
                                <td
                                    class="px-4 py-2 text-sm text-gray-600 border-r border-gray-300 w-30 dark:text-gray-300 dark:border-gray-600"
                                >
                                    {{ mode.duration }}
                                </td>
                            </tr>

                            <!-- Create New Mode Row -->
                            <transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="-translate-y-2 opacity-0"
                                enter-to-class="translate-y-0 opacity-100"
                                leave-active-class="transition duration-200 ease-in"
                                leave-from-class="translate-y-0 opacity-100"
                                leave-to-class="-translate-y-2 opacity-0"
                            >
                                <tr
                                    v-if="assignModeToProgram"
                                    class="border-b border-gray-300 bg-gray-50 dark:bg-gray-700 dark:border-gray-600"
                                >
                                    <td class="px-4 py-2">
                                        <Select
                                            id="modesList"
                                            v-model="modeForm.study_mode_id"
                                            :options="studyModes.filter(
                                                (mode) => !program.studyModes.some((assignedMode) => assignedMode.id === mode.id)
                                            )"
                                            option-label="name"
                                            option-value="id"
                                            checkmark
                                            filter
                                            :placeholder=" $t('programs.studymode.namePlaceholder') "
                                            class="w-full"
                                        />
                                    </td>
                                    <td class="px-4 py-2">
                                        <div
                                            class="flex items-center space-x-6"
                                        >
                                        
                                        <TextInput
                                            v-model="modeForm.duration"
                                            type="number"
                                            :placeholder=" $t('programs.studymode.duration')"
                                            class="w-full px-2 py-1 border rounded-md h-9 dark:bg-gray-800 dark:text-gray-100"
                                        />
                                        <PrimaryButton
                                            class="px-4 py-1 text-white bg-green-500 rounded-md h-9 hover:bg-green-600"
                                            @click="assignMode"
                                        >
                                        {{ $t('programs.studymode.save') }}
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
</template>
