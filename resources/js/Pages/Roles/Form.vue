<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({ form: Object });
const emit = defineEmits(['submit']);

// Ensure guard_name is included in form data
props.form.guard_name = props.form.guard_name || 'web';
</script>

<template>
    <form @submit.prevent="emit('submit')" class="bg-white p-4 shadow rounded">
        <div class="w-1/2 pr-2 py-5">
            <label class="block text-gray-700">Role Name</label>
            <input v-model="form.name" class="w-full border rounded p-2" placeholder="Role Name" />
        </div>
        
        <input type="hidden" v-model="form.guard_name" value="web"/>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</template>
