<script setup>
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
  href: {
    type: String,
    required: true,
  }
});


</script>

<template>
  <Link class="flex text-nowrap items-center px-4 py-2 hover:bg-gray-700 rounded" :href="href">
    <slot />
  </Link>
</template>