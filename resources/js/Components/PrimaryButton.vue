<template>
    <div class="flex justify-center">
        <button
            class="inline-flex btn-xl items-center rounded-md border border-transparent 
            bg-green-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white 
            transition hover:bg-green-700 focus:bg-green-700 active:bg-green-800 
            dark:bg-green-400 dark:text-gray-900 dark:hover:bg-green-300 dark:focus:bg-green-300 dark:active:bg-green-500 
            focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 
            duration-150 ease-in-out w-1/2"
        >
            <slot />
        </button>
    </div>
</template>
