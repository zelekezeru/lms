<script setup>
import { defineProps, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PlusCircleIcon } from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    track: { type: Object, required: true },
});

const modeForm = useForm({
    track_id: props.track.id,
    mode: "",
    duration: "",
    fees: "",
});

const trackForm = useForm({
    name: "",
    description: "",
    track_id: props.track.id,
    user_id: "",
});

const createMode = ref(false);

const addMode = () => {
    modeForm.post(
        route("studyModes.store", {
            redirectTo: route("tracks.show", { track: props.track.id }),
            params: { track: props.track.id },
        }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Added!",
                    "Study Mode added successfully.",
                    "success"
                );
                createMode.value = false;
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
                Study Modes
            </h2>
            <button
                @click="createMode = !createMode"
                class="flex items-center text-indigo-600 hover:text-indigo-800"
            >
                <PlusCircleIcon class="w-8 h-8" />
            </button>
        </div>
        <!-- Track Study Modes List -->
        <div class="overflow-x-auto">
            <div
                class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Study Modes
                    </h2>
                    <button
                        @click="createMode = !createMode"
                        class="flex items-center space-x-6 text-indigo-600 hover:text-indigo-800 transition"
                    >
                        <PlusCircleIcon class="w-8 h-8" />
                        <span class="hidden sm:inline">Add Mode</span>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="w-full min-w-[500px] table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Mode
                                </th>
                                <th
                                    class="w-30 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Duration (Months)
                                </th>
                                <th
                                    class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Fees
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(mode, index) in track.studyModes"
                                :key="mode.id"
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                                class="border-b border-gray-300 dark:border-gray-600"
                            >
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ mode.mode }}
                                </td>
                                <td
                                    class="w-30 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ mode.duration }}
                                </td>
                                <td
                                    class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    {{ mode.fees }}
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
                                    v-if="createMode"
                                    class="bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600"
                                >
                                    <td class="px-4 py-2">
                                        <select
                                            v-model="modeForm.mode"
                                            class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100"
                                        >
                                            <option value="">
                                                Select Mode
                                            </option>
                                            <option value="REGULAR">
                                                REGULAR
                                            </option>
                                            <option value="EXTENSION">
                                                EXTENSION
                                            </option>
                                            <option value="DISTANCE">
                                                DISTANCE
                                            </option>
                                            <option value="ONLINE">
                                                ONLINE
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <TextInput
                                            v-model="modeForm.duration"
                                            type="number"
                                            placeholder="Duration (Months)"
                                            class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                        />
                                    </td>
                                    <td class="px-4 py-2">
                                        <div
                                            class="flex items-center space-x-6"
                                        >
                                            <TextInput
                                                v-model="modeForm.fees"
                                                type="number"
                                                placeholder="Fees"
                                                class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                            />
                                            <PrimaryButton
                                                class="px-4 py-1 h-9 bg-green-500 text-white rounded-md hover:bg-green-600"
                                                @click="addMode"
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
