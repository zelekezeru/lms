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

const tabs = [
    { key: "details", label: "Details", icon: CogIcon },
    { key: "departments", label: "Departments", icon: PencilIcon },
    { key: "modes", label: "Study Modes", icon: EyeIcon },
];
const selectedTab = ref("details");

const modeForm = useForm({
    program_id: props.program.id,
    mode: "",
    duration: "",
    fees: "",
});
const departmentForm = useForm({
    name: "",
    description: "",
    program_id: props.program.id,
    user_id: "",
});

const createMode = ref(false);

const createDepartment = ref(false);

const addMode = () => {
    modeForm.post(
        route("studyModes.store", {
            redirectTo: route("programs.show", { program: props.program.id }),
            params: { program: props.program.id },
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

const deleteProgram = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("programs.destroy", { program: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "Program has been deleted.",
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
        <div class="max-w-5xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ program.name }} Program
            </h1>

            <nav
                class="flex justify-center space-x-4 mb-6 border-b border-gray-200 dark:border-gray-700"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    @click="selectedTab = tab.key"
                    :class="[
                        'flex items-center px-4 py-2 space-x-2 text-sm font-medium transition',
                        selectedTab === tab.key
                            ? 'border-b-2 border-indigo-500 text-indigo-600'
                            : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200',
                    ]"
                >
                    <component :is="tab.icon" class="w-5 h-5" />
                    <span>{{ tab.label }}</span>
                </button>
            </nav>

            <div
                class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border dark:border-gray-700"
            >
                <transition
                    mode="out-in"
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-75"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-75"
                >
                    <div :key="selectedTab">
                        <div
                            v-show="selectedTab === 'details'"
                            class="grid grid-cols-2 gap-4"
                        >
                            <div class="flex flex-col">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >CODE</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ program.code }}</span
                                >
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Name</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ program.name }}</span
                                >
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Language</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ program.language }}</span
                                >
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Description</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ program.description }}</span
                                >
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                    >Program Director</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ program.director?.name || "N/A" }}</span
                                >
                            </div>
                            <div class="flex justify-end col-span-2 mt-4">
                                <Link
                                    v-if="userCan('update-programs')"
                                    :href="
                                        route('programs.edit', {
                                            program: program.id,
                                        })
                                    "
                                    class="inline-flex items-center space-x-2 text-blue-500 hover:text-blue-700"
                                >
                                    <PencilIcon class="w-5 h-5" />
                                    <span>Edit Program</span>
                                </Link>
                                <button
                                    v-if="userCan('delete-programs')"
                                    @click="deleteProgram(program.id)"
                                    class="ml-4 inline-flex items-center space-x-2 text-red-500 hover:text-red-700"
                                >
                                    <TrashIcon class="w-5 h-5" />
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>

                        <!-- Departments Panel -->
                        <div v-show="selectedTab === 'departments'">
                            <div class="flex items-center justify-between mb-4">
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    Departments
                                </h2>
                                <button
                                    @click="
                                        createDepartment = !createDepartment
                                    "
                                    class="flex items-center text-indigo-600 hover:text-indigo-800"
                                >
                                    <component
                                        :is="
                                            createDepartment
                                                ? XMarkIcon
                                                : PlusCircleIcon
                                        "
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
                                                <tr
                                                    class="bg-gray-50 dark:bg-gray-700"
                                                >
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
                                                                route(
                                                                    'departments.show',
                                                                    {
                                                                        department:
                                                                            department.id,
                                                                    }
                                                                )
                                                            "
                                                        >
                                                            {{
                                                                department.name
                                                            }}
                                                        </Link>
                                                    </td>
                                                    <td
                                                        class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                                    >
                                                        {{
                                                            department.description
                                                        }}
                                                    </td>
                                                    <td
                                                        class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
                                                    >
                                                        {{
                                                            department.head
                                                                ?.name
                                                        }}
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
                                                                v-model="
                                                                    departmentForm.name
                                                                "
                                                                type="text"
                                                                placeholder="name"
                                                                class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                                            />
                                                        </td>

                                                        <td class="px-4 py-2">
                                                            <TextInput
                                                                v-model="
                                                                    departmentForm.description
                                                                "
                                                                type="text"
                                                                placeholder="Description"
                                                                class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                                            />
                                                        </td>

                                                        <td class="px-4 py-2">
                                                            <select
                                                                v-model="
                                                                    departmentForm.user_id
                                                                "
                                                                class="w-[60%] mr-10 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100"
                                                            >
                                                                <option
                                                                    value=""
                                                                >
                                                                    Select Head
                                                                </option>
                                                                <option
                                                                    v-for="user in users"
                                                                    :value="
                                                                        user.id
                                                                    "
                                                                >
                                                                    {{
                                                                        user.name
                                                                    }}
                                                                </option>
                                                            </select>

                                                            <PrimaryButton
                                                                class="px-4 py-1 h-9 bg-green-500 text-white rounded-md hover:bg-green-600"
                                                                @click="
                                                                    addDepartment
                                                                "
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

                        <!-- Study Modes Panel -->
                        <div v-show="selectedTab === 'modes'">
                            <div class="flex items-center justify-between mb-4">
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    Study Modes
                                </h2>
                                <button
                                    @click="createMode = !createMode"
                                    class="flex items-center text-indigo-600 hover:text-indigo-800"
                                >
                                    <PlusCircleIcon class="w-8 h-8" />
                                </button>
                            </div>
                            <!-- Program Study Modes List -->
                            <div class="overflow-x-auto">
                                <div
                                    class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
                                >
                                    <div
                                        class="flex items-center justify-between mb-4"
                                    >
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
                                            <span class="hidden sm:inline"
                                                >Add Mode</span
                                            >
                                        </button>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table
                                            class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                                        >
                                            <thead>
                                                <tr
                                                    class="bg-gray-50 dark:bg-gray-700"
                                                >
                                                    <th
                                                        class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                                    >
                                                        Mode
                                                    </th>
                                                    <th
                                                        class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                                    >
                                                        Duration (Months)
                                                    </th>
                                                    <th
                                                        class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                                    >
                                                        Fees
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        mode, index
                                                    ) in program.studyModes"
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
                                                        class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                                    >
                                                        {{ mode.duration }}
                                                    </td>
                                                    <td
                                                        class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300"
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
                                                                v-model="
                                                                    modeForm.mode
                                                                "
                                                                class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100"
                                                            >
                                                                <option
                                                                    value=""
                                                                >
                                                                    Select Mode
                                                                </option>
                                                                <option
                                                                    value="REGULAR"
                                                                >
                                                                    REGULAR
                                                                </option>
                                                                <option
                                                                    value="EXTENSION"
                                                                >
                                                                    EXTENSION
                                                                </option>
                                                                <option
                                                                    value="DISTANCE"
                                                                >
                                                                    DISTANCE
                                                                </option>
                                                                <option
                                                                    value="ONLINE"
                                                                >
                                                                    ONLINE
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td class="px-4 py-2">
                                                            <TextInput
                                                                v-model="
                                                                    modeForm.duration
                                                                "
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
                                                                    v-model="
                                                                        modeForm.fees
                                                                    "
                                                                    type="number"
                                                                    placeholder="Fees"
                                                                    class="w-full px-2 py-1 h-9 border rounded-md dark:bg-gray-800 dark:text-gray-100"
                                                                />
                                                                <PrimaryButton
                                                                    class="px-4 py-1 h-9 bg-green-500 text-white rounded-md hover:bg-green-600"
                                                                    @click="
                                                                        addMode
                                                                    "
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
                    </div>
                </transition>
            </div>
        </div>
    </AppLayout>

    <Modal
        :show="assignCourses"
        @close="assignCourses = !assignCourses"
        :maxWidth="'6xl'"
        class="p-24 h-full"
    >
        <div class="w-full px-16 py-8">
            <h1 class="text-lg mb-5">
                Pick Courses You Would like To Assign To This Section
            </h1>

            <Listbox
                id="cousesList"
                v-model="courseAssignmentForm.courses"
                :options="courses"
                optionLabel="name"
                option-value="id"
                appendTo="self"
                filter
                checkmark
                multiple
                list-style="max-height: 500px"
                placeholder="Select Courses"
                :maxSelectedLabels="3"
                class="w-full"
            />

            <InputError
                :message="courseAssignmentForm.errors.programs"
                class="mt-2 text-sm text-red-500"
            />
            <div class="flex justify-end mt-4">
                <button
                    @click="submitCourseAssignment"
                    :disabled="courseAssignmentForm.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                >
                    {{ courseAssignmentForm.processing ? "Assigning..." : "Assign" }}
                </button>

                <button
                    @click="closeCourseAssignment"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                >
                    Close
                </button>
            </div>
        </div>
    </Modal>

</template>
