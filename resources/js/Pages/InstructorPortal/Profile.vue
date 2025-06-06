<template>
  <StudentLayout>
    <div class="p-6 space-y-6">
      <!-- Profile Header -->
      <div class="flex flex-col md:flex-row items-center md:items-start md:justify-between gap-6">
        <div class="flex items-center gap-4">
          <img
            :src="student.avatar || 'https://ui-avatars.com/api/?name=' + student.name"
            alt="Profile"
            class="w-24 h-24 rounded-full border-4 border-blue-500 shadow-lg"
          />
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ student.firstName }} {{ student.middleName }}
            </h1>
            <p class="text-gray-600 dark:text-gray-300">{{ student.program.name }}</p>
          </div>
        </div>
        <a class="bg-indigo-300 dark:bg-gray-800 text-gray-900 dark:text-white rounded-md p-2" :href="route('profile.edit')">Edit Profile</a>
      </div>

      <!-- Profile Info Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Basic Info -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            Basic Information
          </h2>
          <hr>
          <div class="space-y-2 mt-2">
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Full Name:</span> {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Gender:</span> {{ student.sex }}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Birthdate:</span> {{ student.dateOfBirth }}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Nationality:</span> {{ student.nationality }}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Marital Status:</span> {{ student.maritalStatus }}
            </p>
          </div>
        </div>

        <!-- Contact Info -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            Contact Information
          </h2>
          <hr>
          <div class="space-y-2 mt-2">
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Email:</span> {{ student.user.email }}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Phone:</span> {{ student.mobilePhone }}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Office Phone:</span> {{ student.officePhone }}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Address:</span> {{ student.address }}
            </p>
          </div>
        </div>

        <!-- Academic Info -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
              Academic Information
            </h2>
            <hr>
            <div class="space-y-2 mt-2">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                <span class="font-semibold">Student ID:</span> {{ student.user.user_uuid}}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Program:</span> {{ student.program.name }}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Program language:</span> {{ student.program.language }}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Section:</span> {{ student.section.name}}
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              <span class="font-semibold">Year:</span> {{ student.section.year.name }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import StudentLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});
</script>
