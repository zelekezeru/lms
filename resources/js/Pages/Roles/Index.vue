// resources/js/Pages/roles/Index.vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ roles: Object });

const deleterole = (id) => {
    if (confirm('Are you sure you want to delete this role?')) {
        router.delete(route('roles.destroy', id));
    }
};
</script>

<template>
    <Head title="roles" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">roles</h2>
        </template>
        <div class="p-4">
            <Link :href="route('roles.create')" class="btn btn-primary mb-4">Add Role</Link>
            <table class="w-full bg-white shadow-md rounded">
                <thead>
                    <tr>
                        <th class="p-2">ID</th>
                        <th class="p-2">Role Name</th>
                        <th class="p-2">Role Users</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(role, index) in roles.data" :key="role.id">
                        <td class="p-2">{{ index + 1 }}</td>
                        <td class="p-2">{{ role.name }}</td>
                        <td class="p-2"></td>
                        <td class="p-2">
                            <Link :href="route('roles.show', role.id)" class="text-green-500">View</Link> |
                            <Link :href="route('roles.edit', role.id)" class="text-blue-500">Edit</Link> |
                            <button @click="deleterole(role.id)" class="text-red-500">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>