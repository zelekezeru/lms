<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
  AcademicCapIcon,
  UserGroupIcon,
  ClipboardDocumentCheckIcon,
  CurrencyDollarIcon,
  PencilSquareIcon,
  TrashIcon
} from '@heroicons/vue/24/outline';
import { Chart } from 'chart.js/auto';

let lineChart = null;
let pieChart = null;

// Props from the controller
const props = defineProps({
  courses: {
    type: Number,
    required: true,
  },
  students: {
    type: Number,
    required: true,
  },
  instructors: {
    type: Number,
    required: true,
  }
});

const createCharts = () => {
  const lineCtx = document.getElementById('lineChart');
  const pieCtx = document.getElementById('pieChart');

  // Sample dynamic data generation (you can replace this with a real API call)
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
  const enrollments = [10, 25, 40, 30, 60, props.students]; // simulate growth ending in current total students

  if (lineCtx && pieCtx) {
    lineChart = new Chart(lineCtx, {
      type: 'line',
      data: {
        labels: months,
        datasets: [{
          label: 'New Students',
          data: enrollments,
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
          label: 'User Distribution',
          data: [props.instructors, props.students, props.courses],
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
  }
};

onMounted(createCharts);
</script>


<template>
  <AppLayout>
    <div class="space-y-8">
      <!-- Header -->
      <div>
        <h1 class="mb-2 text-3xl font-bold text-gray-800 dark:text-white">{{ $t('admin_dashboard.title') }}</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ $t('admin_dashboard.subtitle') }}</p>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="p-6 transition bg-white shadow dark:bg-gray-800 rounded-xl hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <AcademicCapIcon class="w-8 h-8 text-blue-500" />
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('admin_dashboard.courses') }}</p>
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ courses }}</h2>
            </div>
          </div>
        </div>
        <div class="p-6 transition bg-white shadow dark:bg-gray-800 rounded-xl hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <UserGroupIcon class="w-8 h-8 text-green-500" />
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('admin_dashboard.students') }}</p>
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ students }}</h2>
            </div>
          </div>
        </div>
        <div class="p-6 transition bg-white shadow dark:bg-gray-800 rounded-xl hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <ClipboardDocumentCheckIcon class="w-8 h-8 text-yellow-500" />
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('admin_dashboard.instructors') }}</p>
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ instructors }}</h2>
            </div>
          </div>
        </div>
        <div class="p-6 transition bg-white shadow dark:bg-gray-800 rounded-xl hover:shadow-lg">
          <div class="flex items-center space-x-4">
            <CurrencyDollarIcon class="w-8 h-8 text-purple-500" />
            <div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ $t('admin_dashboard.revenue') }}</p>
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">$45,000</h2>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="p-6 bg-white shadow-md dark:bg-gray-800 rounded-xl">
          <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">{{ $t('admin_dashboard.enrollment_chart') }}</h2>
          <canvas id="lineChart" class="w-full h-64"></canvas>
        </div>
        <div class="p-6 bg-white shadow-md dark:bg-gray-800 rounded-xl">
          <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">{{ $t('admin_dashboard.user_distribution') }}</h2>
          <canvas id="pieChart" class="w-full h-64"></canvas>
        </div>
      </div>

      <!-- Instructor Management Section -->
      <div class="p-6 bg-white shadow-md dark:bg-gray-800 rounded-xl">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $t('admin_dashboard.manage_instructors') }}</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="bg-gray-100 dark:bg-gray-700">
              <tr>
                <th class="px-6 py-3">{{ $t('table.name') }}</th>
                <th class="px-6 py-3">{{ $t('table.email') }}</th>
                <th class="px-6 py-3">{{ $t('table.specialization') }}</th>
                <th class="px-6 py-3">{{ $t('table.status') }}</th>
                <th class="px-6 py-3">{{ $t('table.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">
                <td class="px-6 py-4">Dr. Jane Smith</td>
                <td class="px-6 py-4">jane.smith@example.com</td>
                <td class="px-6 py-4">Computer Science</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                    {{ $t('status.active') }}
                  </span>
                </td>
                <td class="px-6 py-4 space-x-2">
                  <button class="inline-flex items-center text-blue-600 hover:underline">
                    <PencilSquareIcon class="w-4 h-4 mr-1" />
                    {{ $t('actions.edit') }}
                  </button>
                  <button class="inline-flex items-center text-red-600 hover:underline">
                    <TrashIcon class="w-4 h-4 mr-1" />
                    {{ $t('actions.delete') }}
                  </button>
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

