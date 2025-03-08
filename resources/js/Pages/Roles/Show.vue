<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ role: Object, rolePermissions: Array });

const deleterole = (id) => {
    if (confirm('Are you sure you want to delete this role?')) {
        // Add your delete logic here
    }
};
</script>

<template>
    <Head title="Role Details" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Role Details</h2>
        </template>
        <div class="p-6 bg-white shadow rounded-lg">
            <Link :href="route('roles.permissions', { role: role.id })" class="btn btn-primary mb-4">Assign Permissions</Link>
            <div class="mb-4">
                <p><strong>Role Name:</strong> {{ role.name }}</p>
            </div>
            <div class="mb-4">
                <p><strong>Role Permissions:</strong></p>
                <ul>
                    <li v-for="permission in rolePermissions" :key="permission.id">
                        {{ permission.name }}
                    </li>
                </ul>
            </div>
            <div class="flex justify-between items-center mt-6">
                <Link :href="route('roles.edit', role.id)" class="text-blue-500">Edit</Link>
                <button @click="deleterole(role.id)" class="text-red-500">Delete</button>
            </div>
            <div class="mt-6 text-right">
                <Link :href="route('roles.index')" class="text-blue-500">Back to List</Link>
            </div>
        </div>
    </AppLayout>
</template>
