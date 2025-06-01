<script setup>
defineProps({
    isOpen: Boolean,
    isMobile: Boolean,
});
</script>

<template>
    <aside
        :class="[
            'transition-all duration-300 z-50',
            isMobile ? 'fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 shadow-xl' :
            isOpen ? 'w-64 bg-white dark:bg-gray-800' : 'w-0 overflow-hidden',
        ]"
    >
        <nav class="p-4 space-y-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Registrar Panel</h2>
            <Link href="#" class="block py-2 px-4 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Dashboard</Link>
            <Link href="#" class="block py-2 px-4 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Manage Students</Link>
            <Link href="#" class="block py-2 px-4 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Manage Grades</Link>
            <Link href="#" class="block py-2 px-4 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Settings</Link>
        </nav>
    </aside>
</template>
