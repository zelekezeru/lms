<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { ref } from "vue";
import CourseList from "./List.vue";

defineProps({
    courses: {
        type: Object,
        required: true,
    },
    sortInfo: {
        type: Object,
        required: false,
    },
});

const refreshing = ref(false);

// Refresh function
const refreshData = () => {
    refreshing.value = true;
    router.visit(route("courses.index"), {
        only: ["courses"],
        onFinish: () => {
            refreshing.value = false;
        },
    });
};

const search = ref(usePage().props.search || "");
// Search function
const searchCourses = () => {
    router.get(
        route("courses.index"),
        { ...route().params, search: search.value },
        { preserveState: true },
    );
};

// Delete function with SweetAlert confirmation
const deleteCourse = (id) => {
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
            router.delete(route("courses.destroy", { course: id }), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "The course has been deleted.",
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
        <h1
            class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
        >
            Courses
        </h1>

        <!-- Add New Course Button -->
        <div class="flex justify-between items-center mb-3">
            <Link
                v-if="userCan('create-courses')"
                :href="route('courses.create')"
                class="inline-flex items-center rounded-md bg-gray-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Add New Course
            </Link>

            <button
                @click="refreshData"
                class="inline-flex items-center rounded-md bg-blue-800 text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                title="Refresh Data"
            >
                <ArrowPathIcon
                    class="w-5 h-5 mr-2"
                    :class="{ 'animate-spin': refreshing }"
                />
                Refresh Data
            </button>
        </div>

        <!-- Course List Component -->
        <CourseList
            :courses="courses"
            :sort-info="sortInfo"
            :deleteCourse="deleteCourse"
            :search="search"
        />
    </AppLayout>
</template>
