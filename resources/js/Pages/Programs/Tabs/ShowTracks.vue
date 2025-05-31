<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { PencilIcon, EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { CogIcon, PlusCircleIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    program: { type: Object, required: true },
    users: { type: Array, required: false },
});

const createTrack = ref(false);

const trackForm = useForm({
    name: "",
    description: "",
    duration: "",
    program_id: props.program.id,
});

const addTrack = () => {
    trackForm.post(
        route("tracks.store", {
            redirectTo: route("programs.show", { program: props.program.id }),
            params: { program: props.program.id },
        }),
        {
            onSuccess: () => {
                Swal.fire(
                    $t("programs.tracks.added_title"),
                    $t("programs.tracks.added_text"),
                    "success"
                );
                createTrack.value = false;
                trackForm.reset();
            },
        }
    );
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ $t('programs.tracks.title') }}
            </h2>
            <button
                @click="createTrack = !createTrack"
                class="flex items-center text-indigo-600 hover:text-indigo-800"
            >
                <component
                    :is="createTrack ? XMarkIcon : PlusCircleIcon"
                    class="w-8 h-8"
                />
                {{ $t('programs.tracks.add') }}
            </button>
        </div>

        <div class="overflow-x-auto">
            <div
                class="pt-4 pb-4 mt-8 border-t border-b border-gray-300 dark:border-gray-600"
            >
                <div class="overflow-x-auto">
                    <table
                        class="w-full min-w-[500px] table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="w-40 px-4 py-2 text-sm font-medium text-left text-gray-700 border-r border-gray-300 dark:text-gray-200 dark:border-gray-600">
                                    {{ $t('programs.tracks.name') }}
                                </th>
                                <th class="w-40 px-4 py-2 text-sm font-medium text-left text-gray-700 border-r border-gray-300 dark:text-gray-200 dark:border-gray-600">
                                    {{ $t('programs.tracks.description') }}
                                </th>
                                <th class="w-40 px-4 py-2 text-sm font-medium text-left text-gray-700 border-r border-gray-300 dark:text-gray-200 dark:border-gray-600">
                                    {{ $t('programs.tracks.duration') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(track, index) in program.tracks"
                                :key="track.id"
                                :class="index % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'"
                                class="border-b border-gray-300 dark:border-gray-600"
                            >
                                <td class="w-40 px-4 py-2 text-sm text-gray-600 border-r border-gray-300 dark:text-gray-300 dark:border-gray-600">
                                    <Link :href="route('tracks.show', { track: track.id })">
                                        {{ track.name }}
                                    </Link>
                                </td>
                                <td class="w-40 px-4 py-2 text-sm text-gray-600 border-r border-gray-300 dark:text-gray-300 dark:border-gray-600">
                                    {{ track.description }}
                                </td>
                                <td class="w-40 px-4 py-2 text-sm text-gray-600 border-r border-gray-300 dark:text-gray-300 dark:border-gray-600">
                                    {{ track.duration }}
                                </td>
                            </tr>

                            <!-- Create New Track Row -->
                            <transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="-translate-y-2 opacity-0"
                                enter-to-class="translate-y-0 opacity-100"
                                leave-active-class="transition duration-200 ease-in"
                                leave-from-class="translate-y-0 opacity-100"
                                leave-to-class="-translate-y-2 opacity-0"
                            >
                                <tr
                                    v-if="createTrack"
                                    class="border-b border-gray-300 bg-gray-50 dark:bg-gray-700 dark:border-gray-600"
                                >
                                    <td class="px-4 py-2">
                                        <TextInput
                                            v-model="trackForm.name"
                                            type="text"
                                            :placeholder="$t('programs.tracks.name')"
                                            class="w-full px-2 py-1 border rounded-md h-9 dark:bg-gray-800 dark:text-gray-100"
                                        />
                                    </td>

                                    <td class="px-4 py-2">
                                        <TextInput
                                            v-model="trackForm.description"
                                            type="text"
                                            :placeholder="$t('programs.tracks.description')"
                                            class="max-w-[70%] px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                        />
                                    </td>
                                    <td class="flex justify-between px-4 py-2">
                                        <TextInput
                                            v-model="trackForm.duration"
                                            type="number"
                                            :placeholder="$t('programs.tracks.duration')"
                                            class="max-w-[70%] px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                        />
                                        <PrimaryButton
                                            class="px-4 py-1 text-white bg-green-500 rounded-md h-9 hover:bg-green-600"
                                            @click="addTrack"
                                        >
                                            {{ $t('common.save') }}
                                        </PrimaryButton>
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

