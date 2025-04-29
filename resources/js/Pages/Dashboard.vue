<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
  AcademicCapIcon,
  UserGroupIcon,
  ClipboardDocumentCheckIcon,
  CurrencyDollarIcon
} from '@heroicons/vue/24/outline';
import { Chart } from 'chart.js/auto';

let lineChart = null;
let pieChart = null;

const createCharts = () => {
  const lineCtx = document.getElementById('lineChart');
  const pieCtx = document.getElementById('pieChart');

  lineChart = new Chart(lineCtx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'New Students',
        data: [30, 45, 60, 50, 80, 100],
        fill: true,
        backgroundColor: 'rgba(59, 130, 246, 0.2)',
        borderColor: 'rgba(59, 130, 246, 1)',
        tension: 0.4,
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true }
      }
    }
  });

  pieChart = new Chart(pieCtx, {
    type: 'doughnut',
    data: {
      labels: ['Instructors', 'Students', 'Courses'],
      datasets: [{
        label: 'Distribution',
        data: [15, 120, 35],
        backgroundColor: [
          'rgba(34,197,94,0.7)',   // green
          'rgba(59,130,246,0.7)',  // blue
          'rgba(234,179,8,0.7)'    // yellow
        ],
        borderColor: [
          'rgba(34,197,94,1)',
          'rgba(59,130,246,1)',
          'rgba(234,179,8,1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true
    }
  });
};

onMounted(createCharts);
</script>

<template>
  <AppLayout>
    <div class="space-y-8">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Admin Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage the LMS, monitor progress, and overview key metrics.</p>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition">
          <div class="flex items-center space-x-4">
            <AcademicCapIcon class="w-8 h-8 text-blue-500" />
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm">Courses</p>
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">120</h2>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition">
          <div class="flex items-center space-x-4">
            <UserGroupIcon class="w-8 h-8 text-green-500" />
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm">Students</p>
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">2,400</h2>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition">
          <div class="flex items-center space-x-4">
            <ClipboardDocumentCheckIcon class="w-8 h-8 text-yellow-500" />
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm">Instructors</p>
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">35</h2>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition">
          <div class="flex items-center space-x-4">
            <CurrencyDollarIcon class="w-8 h-8 text-purple-500" />
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm">Revenue</p>
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">$45,000</h2>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Student Enrollment Over Time</h2>
          <canvas id="lineChart" class="w-full h-64"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">User Distribution</h2>
          <canvas id="pieChart" class="w-full h-64"></canvas>
        </div>
      </div>

      <!-- Instructor Management Section -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Manage Instructors</h2>
          <button class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm">Add Instructor</button>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="bg-gray-100 dark:bg-gray-700">
              <tr>
                <th class="py-3 px-6">Name</th>
                <th class="py-3 px-6">Email</th>
                <th class="py-3 px-6">Specialization</th>
                <th class="py-3 px-6">Status</th>
                <th class="py-3 px-6">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">
                <td class="py-4 px-6">Dr. Jane Smith</td>
                <td class="py-4 px-6">jane.smith@example.com</td>
                <td class="py-4 px-6">Computer Science</td>
                <td class="py-4 px-6">
                  <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Active</span>
                </td>
                <td class="py-4 px-6 space-x-2">
                  <button class="text-blue-600 hover:underline">Edit</button>
                  <button class="text-red-600 hover:underline">Delete</button>
                </td>
              </tr>
              <!-- Add more instructors here -->
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </AppLayout>
</template>
