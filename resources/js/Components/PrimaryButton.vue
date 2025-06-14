<template>
    <div class="flex justify-center">
        <button
            class="inline-flex btn-xl items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-900 dark:hover:bg-gray-300 dark:focus:bg-gray-300 dark:active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 duration-150 ease-in-out w-1/2"
        >
            <slot />
        </button>
    </div>
</template>
