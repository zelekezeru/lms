<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import Modal from "@/Components/Modal.vue";
import { Listbox, MultiSelect } from "primevue";
import {
    PencilIcon,
    EyeIcon,
    TrashIcon,
    CogIcon,
    AcademicCapIcon,
    UsersIcon,
    PencilSquareIcon, 
    PlusCircleIcon,
    BookOpenIcon,
    HomeModernIcon,
} from "@heroicons/vue/24/solid";

// Define the props for the instructor
defineProps({
    instructor: {
        type: Object,
        required: true,
    },


});

// Multi nav header options
const selectedTab = ref('details');


const tabs = [
    { key: 'details', label: 'Details', icon: CogIcon },
    { key: 'academics', label: 'Academics', icon: BookOpenIcon },
    { key: 'courses', label: 'Courses', icon: AcademicCapIcon },
    { key: 'sections', label: 'Sections', icon: UsersIcon },
];

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

// Delete function with SweetAlert confirmation
const deleteInstructor = (id) => {
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
            router.delete(route("instructors.destroy", { instructor: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The instructor has been deleted.",
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
        <div class="max-w-8xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                {{ instructor.user.name }}
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

            
            <!-- Details Panel -->
            <div v-show="selectedTab === 'details'" >

                <div class="dark:bg-gray-800 shadow-lg rounded-xl p-6 border dark:border-gray-700">
                        <!-- Instructor Image -->
                        <div class="flex justify-center mb-8">
                            <div
                                v-if="!imageLoaded"
                                class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                            ></div>

                            <img
                                v-show="imageLoaded"
                                class="rounded-full w-44 h-44 object-contain bg-gray-400"
                                :src="instructor.profileImg"
                                :alt="`Logo of ` + instructor.name"
                                @load="handleImageLoad"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">

                            <!-- instructor Full Name -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Full Name</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ instructor.user.name }}</span
                                >
                            </div>

                            <!-- Email -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Email</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ instructor.user.email }}</span
                                >
                            </div>

                            <!-- Phone -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Phone</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ instructor.user.phone }}</span
                                >
                            </div>

                            <!-- Employment Type -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Employment Type</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ instructor.employmentType }}</span
                                >
                            </div>

                            <!-- Bio -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Bio</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >{{ instructor.bio }}</span
                                >
                            </div>

                            <!-- Status -->
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500 dark:text-gray-400"
                                    >Status</span
                                >
                                <span
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                >
                                    <div v-if="instructor.status == 0" class="text-red-500">
                                        Inactive
                                    </div>
                                    <div v-else class="text-green-500">Active</div>
                                </span>
                            </div>
                        </div>

                        <!-- Edit and Delete Buttons -->
                        <div class="flex justify-end mt-6 space-x-6">
                            <!-- Edit Button, only show if user has permission -->
                            <div v-if="userCan('update-instructors')">
                                <Link
                                    :href="
                                        route('instructors.edit', {
                                            instructor: instructor.id,
                                        })
                                    "
                                    class="text-blue-500 hover:text-blue-700"
                                >
                                    <PencilIcon class="w-5 h-5" />
                                    <span>Edit</span>
                                </Link>
                            </div>

                            <!-- Delete Button, only show if user has permission -->
                            <div v-if="userCan('delete-instructors')">
                                <button
                                    @click="deleteInstructor(instructor.id)"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <TrashIcon class="w-5 h-5" />
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </div>

                    
                <!-- Academic Panel -->
                <div v-show="selectedTab === 'academics'">
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Academic Information
                        </h2>
                        
                            
                        <div class="flex justify-center mb-6">
                            

                        </div> 
                    </div>

                    <div class="overflow-x-auto">
                        <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">
                    
                            <!-- instructor details -->
                            <div class="grid grid-cols-2 gap-4">


                                
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses Panel -->
                <div v-show="selectedTab === 'courses'" class="">
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Courses
                        </h2>
                        <button
                            @click="assignCourses = !assignCourses"
                            class="flex items-center text-indigo-600 hover:text-indigo-800"
                        >
                            Assign Course
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <div
                            class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4"
                        >
                            <div class="flex items-center justify-between mb-4">
                                <h2
                                    class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    Courses
                                </h2>
                            </div>
                            <!-- Section courses list -->
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full table-auto border border-gray-300 dark:border-gray-600"
                                >
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-700">
                                            <th
                                                class="w-10 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                No.
                                            </th>
                                            <th
                                                class="w-80 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                Name
                                            </th>
                                            <th
                                                class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                Course Code
                                            </th>
                                            <th
                                                class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                            >
                                                Credit Hours
                                            </th>
                                            <th
                                                class="w-60 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                            >
                                                Sections
                                            </th>
                                            <th
                                                class="w-40 px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200"
                                            >
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                course, index
                                            ) in instructor.courses"
                                            :key="course.id"
                                            :class="
                                                index % 2 === 0
                                                    ? 'bg-white dark:bg-gray-800'
                                                    : 'bg-gray-50 dark:bg-gray-700'
                                            "
                                            class="border-b border-gray-300 dark:border-gray-600"
                                        >
                                            <td
                                                class="w-10 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                1
                                            </td>

                                            <td
                                                class="w-80 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                <Link
                                                    :href="
                                                        route('courses.show', {
                                                            course: course.id,
                                                        })
                                                    "
                                                >
                                                    {{ course.name }}
                                                </Link>
                                            </td>
                                            <td
                                                class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                {{ course.code }}
                                            </td>
                                            <td
                                                class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                {{ course.creditHour }}
                                            </td>

                                            <td
                                                class="w-60 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                    <!-- <span v-if="course.instructor" class="flex justify-between">
                                                        {{course.instructor.name}}
                                                            <PencilSquareIcon @click="opneInstructorAssignemnt(course)" class="w-5 text-green-600 cursor-pointer"/>
                                                    </span>
                                                    <span v-else>
                                                        <button
                                                            @click="opneInstructorAssignemnt(course)"
                                                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-1 rounded-lg shadow-md transition mr-5"
                                                        >
                                                            Assign
                                                        </button>
                                                    </span> -->
                                            </td>
                                            <!-- Course Assessments -->
                                            <td
                                                class="w-40 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600"
                                            >
                                                <Link
                                                    :href="
                                                        route(
                                                            'assessments.section_course',
                                                            {
                                                                course: course.id,
                                                                section:
                                                                    section.id,
                                                            }
                                                        )
                                                    "
                                                    class="text-green-500 hover:text-green-700"
                                                >
                                                    <CogIcon
                                                        class="w-5 h-5 inline-block"
                                                    />
                                                    <span class="inline-block"
                                                        >Assessments</span
                                                    >
                                                </Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Panel -->
                <div v-show="selectedTab === 'sections'">     
                    <div class="flex items-center justify-between mb-4">
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Section Assignments
                        </h2>
                        
                            
                        <div class="flex justify-center mb-6">
                            <button
                                @click="router.visit(route('instructors.profile', { instructor: instructor.id }))"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Assign to Sections
                            </button>
                        </div> 
                    </div>

                    <div class="overflow-x-auto">
                        <div class="mt-8 border-t border-b border-gray-300 dark:border-gray-600 pt-4 pb-4">           
                    
                            <div class="grid grid-cols-2 gap-4">

                                <div v-if="instructor.sections" class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >Assigned Sections</span
                                    >
                                    <ul class="list-disc pl-5">
                                        <li
                                            v-for="section in instructor.sections"
                                            :key="section.id"
                                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                        >
                                            {{ section.name }}
                                        </li>
                                    </ul>
                                </div>
                                <div v-else class="flex flex-col">
                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                        >No Sections Assigned</span
                                    >
                                    <span
                                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ instructor.name }}
                                        has not been assigned to any Sections yet.
                                    </span>
                                </div>
                            </div> 
                        </div>  
                    </div>
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
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                >
                    Assign
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
