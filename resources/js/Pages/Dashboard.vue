<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { 
    AcademicCapIcon, 
    UserGroupIcon, 
    ClipboardDocumentCheckIcon, 
    ClockIcon, 
    BellIcon, 
    CalendarIcon, 
    ChartBarIcon, 
    ArrowTrendingUpIcon 
} from '@heroicons/vue/24/outline';
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout>
        <div class="p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Students (Blue) -->
                <div class="bg-blue-500 text-white shadow-lg rounded-lg p-6 flex items-center">
                    <AcademicCapIcon class="w-12 h-12 text-white" />
                    <div class="ml-4">
                        <p class="text-white">Total Students</p>
                        <h4 class="text-xl font-bold">1,200</h4>
                    </div>
                </div>

                <!-- Active Teachers (Green) -->
                <div class="bg-green-500 text-white shadow-lg rounded-lg p-6 flex items-center">
                    <UserGroupIcon class="w-12 h-12 text-white" />
                    <div class="ml-4">
                        <p class="text-white">Active Teachers</p>
                        <h4 class="text-xl font-bold">80</h4>
                    </div>
                </div>

                <!-- Ongoing Courses (Yellow) -->
                <div class="bg-yellow-500 text-white shadow-lg rounded-lg p-6 flex items-center">
                    <ClipboardDocumentCheckIcon class="w-12 h-12 text-white" />
                    <div class="ml-4">
                        <p class="text-white">Ongoing Courses</p>
                        <h4 class="text-xl font-bold">35</h4>
                    </div>
                </div>

                <!-- Pending Admissions (Red) -->
                <div class="bg-red-500 text-white shadow-lg rounded-lg p-6 flex items-center">
                    <ClockIcon class="w-12 h-12 text-white" />
                    <div class="ml-4">
                        <p class="text-white">Pending Admissions</p>
                        <h4 class="text-xl font-bold">50</h4>
                    </div>
                </div>
            </div>

            <!-- Additional Widgets -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- System Notifications -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <BellIcon class="w-8 h-8 text-red-500" />
                        <h3 class="text-lg font-semibold ml-2">System Notifications</h3>
                    </div>
                    <ul class="text-gray-600 space-y-2">
                        <li>New student enrollment request</li>
                        <li>Upcoming teacher meeting</li>
                        <li>System maintenance scheduled</li>
                    </ul>
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <CalendarIcon class="w-8 h-8 text-blue-500" />
                        <h3 class="text-lg font-semibold ml-2">Upcoming Events</h3>
                    </div>
                    <ul class="text-gray-600 space-y-2">
                        <li>March 20: Parent-Teacher Conference</li>
                        <li>April 5: Semester Exams Begin</li>
                        <li>April 15: School Science Fair</li>
                    </ul>
                </div>

                <!-- Performance Overview -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <ChartBarIcon class="w-8 h-8 text-green-500" />
                        <h3 class="text-lg font-semibold ml-2">Performance Overview</h3>
                    </div>
                    <div class="text-gray-600">
                        <p>Average Attendance: <span class="font-bold">92%</span></p>
                        <p>Course Completion Rate: <span class="font-bold">85%</span></p>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                <div class="flex items-center mb-4">
                    <ArrowTrendingUpIcon class="w-8 h-8 text-purple-500" />
                    <h3 class="text-lg font-semibold ml-2">Recent Activities</h3>
                </div>
                <ul class="text-gray-600 space-y-2">
                    <li>Admin approved 5 new students</li>
                    <li>Teacher uploaded grades for Math 101</li>
                    <li>New course "Web Development" added</li>
                </ul>
            </div>

        </div>
    </AppLayout>
</template>
