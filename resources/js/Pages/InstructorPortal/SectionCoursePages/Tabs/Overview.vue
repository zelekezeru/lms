<script setup>

</script>

<template>
<div class="instructor-section mb-8">
    <h2 class="text-2xl font-semibold mb-4">Instructor</h2>
    <div class="flex items-center gap-6 bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <img
            :src="instructor?.photo_url || '/images/instructor-placeholder.png'"
            :alt="instructor?.name || 'Instructor Photo'"
            class="instructor-photo w-24 h-24 rounded-full object-cover border"
        />
        <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ instructor?.name || 'N/A' }}
            </h3>
            <p class="text-gray-600 dark:text-gray-300">
                {{ instructor?.title || 'Instructor' }}
            </p>
            <p class="text-gray-500 dark:text-gray-400">
                Email: <span>{{ instructor?.email || 'N/A' }}</span>
            </p>
        </div>
    </div>
</div>
</template>
