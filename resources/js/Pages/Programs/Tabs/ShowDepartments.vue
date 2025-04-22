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

const createDepartment = ref(false);

const departmentForm = useForm({
    name: "",
    description: "",
    program_id: props.program.id,
    user_id: "",
});

const addDepartment = () => {
    departmentForm.post(
        route("departments.store", {
            redirectTo: route("programs.show", { program: props.program.id }),
            params: { program: props.program.id },
        }),
        {
            onSuccess: () => {
                Swal.fire(
                    "Added!",
                    "Department added successfully.",
                    "success"
                );
                createDepartment.value = false;
                departmentForm.reset();
            },
        }
    );
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Departments
            </h2>
            <button
                @click="createDepartment = !createDepartment"
                class="flex items-center text-indigo-600 hover:text-indigo-800"
            >
                <component
                    :is="createDepartment ? XMarkIcon : PlusCircleIcon"
                    class="w-8 h-8"
                />
                Add Department
            </button>
        </div>

        <div class="overflow-x-auto">
            <div
                class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
            >
                <!-- Program Departments list -->
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Name
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                >
                                    Description
                                </th>
                                <th
                                    class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Head
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(
                                    department, index
                                ) in program.departments"
                                :key="department.id"
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
                                    <Link
                                        :href="
                                            route('departments.show', {
                                                department: department.id,
                                            })
                                        "
                                    >
                                        {{ department.name }}
                                    </Link>
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                >
                                    {{ department.description }}
                                </td>
                                <td
                                    class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                >
                                    {{ department.head?.name }}
                                </td>
                            </tr>

                            <!-- Create New Department Row -->
                            <transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="opacity-0 -translate-y-2"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition duration-200 ease-in"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 -translate-y-2"
                            >
                                <tr
                                    v-if="createDepartment"
                                    class="bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600"
                                >
                                    <td class="px-4 py-2">
                                        <TextInput
                                            v-model="departmentForm.name"
                                            type="text"
                                            placeholder="name"
                                            class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                        />
                                    </td>

                                    <td class="px-4 py-2">
                                        <TextInput
                                            v-model="departmentForm.description"
                                            type="text"
                                            placeholder="Description"
                                            class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                        />
                                    </td>

                                    <td class="px-4 py-2">
                                        <select
                                            v-model="departmentForm.user_id"
                                            class="w-[60%] mr-10 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100"
                                        >
                                            <option value="">
                                                Select Head
                                            </option>
                                            <option
                                                v-for="user in users"
                                                :value="user.id"
                                            >
                                                {{ user.name }}
                                            </option>
                                        </select>

                                        <PrimaryButton
                                            class="px-4 py-1 h-9 bg-green-500 text-white rounded-md hover:bg-green-600"
                                            @click="addDepartment"
                                        >
                                            Save
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
