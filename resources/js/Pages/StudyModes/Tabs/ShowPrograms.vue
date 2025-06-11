<script setup>
import { defineProps, ref } from "vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import {  } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { EyeIcon, LinkSlashIcon, PlusCircleIcon } from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    studyMode: { type: Object, required: true },
    programs: { type: Array, required: true },
});


const confirmRemove = (programId, studyModeId) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This will unlink the program from the study mode.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, unlink it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("studymodes.programs.destroy", { study_mode: studyModeId, program: programId }), {
                onSuccess: () => {
                    Swal.fire(
                        "Unlinked!",
                        "The program has been removed from the study mode.",
                        "success"
                    );
                },
            });
        }
    });
};

</script>

<template>
    <div>
        <!-- Program Programs List -->
        <div class="overflow-x-auto">
            <div
                class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <div class="flex items-center justify-between mb-4 ">
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        <span class="text-green-700">Programs</span> in {{ studyMode.name }} StudyMode
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="w-full min-w-[500px] table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                
                                <th
                                    class="w-20 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    #
                                </th>
                                
                                <th
                                    class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    program
                                </th>
                                <th
                                    class="w-30 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Duration (Months)
                                </th>
                                <th
                                    class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(program, index) in programs"
                                :key="program.id"
                                :class="
                                    index % 2 === 0
                                        ? 'bg-white dark:bg-gray-800'
                                        : 'bg-gray-50 dark:bg-gray-700'
                                "
                                class="border-b border-gray-300 dark:border-gray-600"
                            >
                                <td
                                    class="w-20 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    <Link
                                        v-if="userCan('view-programs')"
                                        :href="route('programs.show', { program: program.id })"
                                        class="text-blue-500 hover:text-blue-700"
                                    >
                                        {{ program.name }}
                                    </Link>
                                    <span v-else>
                                        {{ program.name }}
                                    </span>
                                </td>
                                <td
                                    class="w-30 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ program.duration }}
                                </td>
                                <td class="px-6 py-4">
                                    <button
                                        v-if="userCan('delete-programs')"
                                        @click="confirmRemove(program.id, studyMode.id)"
                                        class="text-red-500 hover:text-red-700 flex items-center space-x-1"
                                        title="Remove from StudyMode"
                                    >
                                        <LinkSlashIcon class="w-5 h-5" />
                                        <span> Unlink </span> 
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
