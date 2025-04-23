<template>
    <input
        :type="type"
        v-bind="$attrs"
        v-model="modelValue"
        class="border rounded px-3 py-2 w-full"
    />
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
    modelValue: [String, Number],
    type: {
        type: String,
        default: "text",
    },
});

const emits = defineEmits(["update:modelValue"]);
</script>
