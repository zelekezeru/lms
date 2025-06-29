<template>
    <div class="flex justify-center">
        <button
            class="px-4 py-2 bg-green-600 text-white rounded"
            v-bind="$attrs"
        >
            <slot></slot>
        </button>
    </div>
</template>
