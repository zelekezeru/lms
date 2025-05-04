<template>
  <div class="form-group">
    <label :for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
      {{ label }}
    </label>
    <select
      :id="id"
      :name="name"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-800 dark:text-gray-100"
    >
      <option v-for="(option, index) in options" :key="index" :value="option[valueKey]">
        {{ option[labelKey] }}
      </option>
    </select>
    <p v-if="error" class="mt-2 text-sm text-red-600 dark:text-red-400">
      {{ error }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  id: String,
  name: String,
  label: String,
  options: Array,
  modelValue: [String, Number],
  error: String,
  labelKey: {
    type: String,
    default: "label",
  },
  valueKey: {
    type: String,
    default: "value",
  },
});

defineEmits(["update:modelValue"]);
</script>
