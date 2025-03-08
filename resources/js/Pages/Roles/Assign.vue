<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, defineProps } from 'vue';

const props = defineProps({
    role: Object,
    permissions: Array,
    attachedPermissions: Array
});
const form = ref({ permissions: [] });

// Initialize selectedPermissions with existing attached permissions
const selectedPermissions = ref(props.attachedPermissions);

const submit = () => {
    form.value.permissions = selectedPermissions.value;
    router.put(route('roles.attach', props.role.id), form.value);
};
</script>

<template>
    <Head title="Assign Permissions" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Assign Permissions to <span class="text-info">{{ role.name }}</span> role</h2>
        </template>

        <form @submit.prevent="submit" class="bg-white p-4 shadow rounded">
            <div class="w-1/2 pr-2 py-5">
                <label class="block text-gray-700">Select Permissions</label>
                <select v-model="selectedPermissions" class="w-full border rounded p-2" multiple>
                    <option v-for="permission in permissions" :key="permission.id" :value="permission.id">
                        {{ permission.name }}
                    </option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </AppLayout>
</template>
