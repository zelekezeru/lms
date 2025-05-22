<script setup>
import InstructorLayout from "@/Layouts/InstructorLayout.vue";
import { ref } from "vue";

const selectedCourse = ref(null);
const selectedBatch = ref(null);

const courses = [
    {
        id: 1,
        title: "Introduction to Computer Science",
        code: "CS101",
        batches: [
            { id: 1, name: "CS101 - A" },
            { id: 2, name: "CS101 - B" },
        ],
    },
    {
        id: 2,
        title: "Database Systems",
        code: "DB301",
        batches: [
            { id: 3, name: "DB301 - A" },
            { id: 4, name: "DB301 - B" },
        ],
    },
];

const students = ref([
    { id: 1, name: "Alice Johnson", present: true },
    { id: 2, name: "Bob Smith", present: true },
    { id: 3, name: "Clara Evans", present: false },
    { id: 4, name: "Daniel Kim", present: true },
]);

const previousAttendances = ref([
    {
        id: 1,
        course: "CS101",
        batch: "CS101 - A",
        date: "2025-04-24",
        present: 25,
        total: 30,
    },
    {
        id: 2,
        course: "DB301",
        batch: "DB301 - B",
        date: "2025-04-23",
        present: 18,
        total: 20,
    },
    {
        id: 3,
        course: "CS101",
        batch: "CS101 - B",
        date: "2025-04-22",
        present: 22,
        total: 25,
    },
]);

function toggleAttendance(studentId) {
    const student = students.value.find((s) => s.id === studentId);
    if (student) student.present = !student.present;
}

function submitAttendance() {
    const attendanceData = students.value.map((s) => ({
        id: s.id,
        present: s.present,
    }));

    console.log("Submitting attendance:", {
        course: selectedCourse.value,
        batch: selectedBatch.value,
        attendance: attendanceData,
    });

    // Optionally: send to backend with Inertia.post(...)
}
</script>

<template>
    <InstructorLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Take Attendance
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                Mark attendance for your students by course and batch.
            </p>
        </div>

        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >Select Course</label
                >
                <select
                    v-model="selectedCourse"
                    class="w-full px-3 py-2 rounded-md border dark:bg-gray-800 dark:border-gray-700 text-sm text-gray-800 dark:text-white"
                >
                    <option disabled value="">-- Choose a course --</option>
                    <option
                        v-for="course in courses"
                        :key="course.id"
                        :value="course"
                    >
                        {{ course.title }}
                    </option>
                </select>
            </div>
            <div v-if="selectedCourse">
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >Select Batch</label
                >
                <select
                    v-model="selectedBatch"
                    class="w-full px-3 py-2 rounded-md border dark:bg-gray-800 dark:border-gray-700 text-sm text-gray-800 dark:text-white"
                >
                    <option disabled value="">-- Choose a batch --</option>
                    <option
                        v-for="batch in selectedCourse.batches"
                        :key="batch.id"
                        :value="batch"
                    >
                        {{ batch.name }}
                    </option>
                </select>
            </div>
        </div>

        <div
            v-if="selectedBatch"
            class="bg-white dark:bg-gray-800 rounded-xl shadow p-4"
        >
            <h2
                class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
            >
                Students in {{ selectedBatch.name }}
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border dark:border-gray-700">
                    <thead
                        class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200"
                    >
                        <tr>
                            <th class="text-left px-4 py-2">#</th>
                            <th class="text-left px-4 py-2">Name</th>
                            <th class="text-left px-4 py-2">Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(student, index) in students"
                            :key="student.id"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="px-4 py-2">{{ index + 1 }}</td>
                            <td class="px-4 py-2">{{ student.name }}</td>
                            <td class="px-4 py-2">
                                <button
                                    @click="toggleAttendance(student.id)"
                                    :class="
                                        student.present
                                            ? 'bg-green-500 hover:bg-green-600'
                                            : 'bg-red-500 hover:bg-red-600'
                                    "
                                    class="text-white text-xs px-3 py-1 rounded transition"
                                >
                                    {{ student.present ? "Present" : "Absent" }}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-right">
                <button
                    @click="submitAttendance"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md text-sm font-medium"
                >
                    Submit Attendance
                </button>
            </div>
        </div>

        <!-- Previous Attendance Records -->
        <div class="mt-12">
            <h2
                class="text-lg font-semibold text-gray-800 dark:text-white mb-4"
            >
                Previous Attendance Records
            </h2>

            <div
                class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow p-4"
            >
                <table class="min-w-full text-sm border dark:border-gray-700">
                    <thead
                        class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200"
                    >
                        <tr>
                            <th class="text-left px-4 py-2">#</th>
                            <th class="text-left px-4 py-2">Course</th>
                            <th class="text-left px-4 py-2">Batch</th>
                            <th class="text-left px-4 py-2">Date</th>
                            <th class="text-left px-4 py-2">Present</th>
                            <th class="text-left px-4 py-2">Total</th>
                            <th class="text-left px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(record, index) in previousAttendances"
                            :key="record.id"
                            class="border-t dark:border-gray-700"
                        >
                            <td class="px-4 py-2">{{ index + 1 }}</td>
                            <td class="px-4 py-2">{{ record.course }}</td>
                            <td class="px-4 py-2">{{ record.batch }}</td>
                            <td class="px-4 py-2">{{ record.date }}</td>
                            <td class="px-4 py-2">{{ record.present }}</td>
                            <td class="px-4 py-2">{{ record.total }}</td>
                            <td class="px-4 py-2">
                                <button
                                    class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs px-3 py-1 rounded"
                                >
                                    View Details
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </InstructorLayout>
</template>
