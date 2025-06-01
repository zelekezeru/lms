<script setup>
import { defineProps } from "vue";

const props = defineProps({
  form: { type: Object, required: true },
});
</script>

<template>
  <form @submit.prevent="$emit('submit')">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          {{ $t('centers.name') }}
        </label>
        <input
          id="name"
          type="text"
          v-model="form.name"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <div v-if="form.errors.name" class="text-red-500 text-sm">
          {{ form.errors.name }}
        </div>
      </div>

      <!-- Address -->
      <div>
        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          {{ $t('centers.address') }}
        </label>
        <input
          id="address"
          type="text"
          v-model="form.address"
          required
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
        <div v-if="form.errors.address" class="text-red-500 text-sm">
          {{ form.errors.address }}
        </div>
      </div>

      <!-- Coordinator (optional select) -->
      <div>
        <label for="coordinator_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          {{ $t('centers.coordinator') }}
        </label>
        <select
          id="coordinator_id"
          v-model="form.coordinator_id"
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option :value="null">{{ $t('centers.select_coordinator') }}</option>
          <!-- Add your coordinators list dynamically if passed via props -->
          <option v-for="coordinator in form.coordinators ?? []" :key="coordinator.id" :value="coordinator.id">
            {{ coordinator.name }}
          </option>
        </select>
        <div v-if="form.errors.coordinator_id" class="text-red-500 text-sm">
          {{ form.errors.coordinator_id }}
        </div>
      </div>

      <!-- Status -->
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          {{ $t('centers.status') }}
        </label>
        <select
          id="status"
          v-model="form.status"
          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option value="Inactive">{{ $t('status.inactive') }}</option>
          <option value="Active">{{ $t('status.active') }}</option>
        </select>
        <div v-if="form.errors.status" class="text-red-500 text-sm">
          {{ form.errors.status }}
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-6 flex justify-center">
      <button
        type="submit"
        :disabled="form.processing"
        class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <span v-if="!form.processing">{{ $t('common.save', 'Submit') }}</span>
        <span v-else>{{ $t('common.loading', 'Submitting...') }}</span>
      </button>
    </div>
  </form>
</template>
