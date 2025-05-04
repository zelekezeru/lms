<template>
    <div>
        <!-- Title Field -->
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Category Title</label>
            <input v-model="form.title" type="text" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none" />
            <InputError :message="form.errors.title" class="mt-2" />
        </div>

        <!-- Description Field -->
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Category Description</label>
            <input v-model="form.description" type="text" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none" />
            <InputError :message="form.errors.description" class="mt-2" />
        </div>

        <!-- Status Field -->
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Status</label>
            <select v-model="form.is_active" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 focus:outline-none">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            <InputError :message="form.errors.is_active" class="mt-2" />
        </div>
    </div>
</template>

<script setup>
import InputError from "@/Components/InputError.vue";

defineProps({
    form: {
        type: Object,
        required: true,
    },
});
</script>
