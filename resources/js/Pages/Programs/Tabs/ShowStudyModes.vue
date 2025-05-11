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
                    "Added!",
                    "Study Mode added successfully.",
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
                Study Modes This Program Is Offered In
            </h2>
            <button
                @click="assignModeToProgram = !assignModeToProgram"
                class="flex items-center text-indigo-600 hover:text-indigo-800"
            >
                <PlusCircleIcon class="w-8 h-8" />
                <span class="hidden sm:inline">Add Mode</span>
            </button>
        </div>
        <!-- Program Study Modes List -->
        <div class="overflow-x-auto">
            <div
                class="mt-8 border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <div class="overflow-x-auto">
                    <table
                        class="w-full min-w-[500px] table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="px-4 w-96 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Mode
                                </th>
                                <th
                                    class="w-30 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Duration (Years)
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
                                    class="w-96 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ mode.name }}
                                </td>
                                <td
                                    class="w-30 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ mode.duration }}
                                </td>
                            </tr>

                            <!-- Create New Mode Row -->
                            <transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="opacity-0 -translate-y-2"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition duration-200 ease-in"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 -translate-y-2"
                            >
                                <tr
                                    v-if="assignModeToProgram"
                                    class="bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600"
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
                                            placeholder="Select Study Mode"
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
                                            placeholder="Duration (Years)"
                                            class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                        />
                                        <PrimaryButton
                                            class="px-4 py-1 h-9 bg-green-500 text-white rounded-md hover:bg-green-600"
                                            @click="assignMode"
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
</template>
