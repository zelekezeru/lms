// resources/js/Pages/permissions/Index.vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ permissions: Object });

const deletepermission = (id) => {
    if (confirm('Are you sure you want to delete this permission?')) {
        router.delete(route('permissions.destroy', id));
    }
};
</script>

<template>
    <Head title="permissions" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Permissions</h2>
        </template>
        <div class="p-4">
            <Link :href="route('permissions.create')" class="btn btn-primary mb-4">Add Permission</Link>
            <table class="w-full bg-white shadow-md rounded">
                <thead>
                    <tr>
                        <th class="p-2">ID</th>
                        <th class="p-2">Permission Name</th>
                        <th class="p-2">Permission Users</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(permission, index) in permissions.data" :key="permission.id">
                        <td class="p-2">{{ index + 1 }}</td>
                        <td class="p-2">{{ permission.name }}</td>
                        <td class="p-2"></td>
                        <td class="p-2">
                            <Link :href="route('permissions.show', permission.id)" class="text-green-500">View</Link> |
                            <Link :href="route('permissions.edit', permission.id)" class="text-blue-500">Edit</Link> |
                            <button @click="deletepermission(permission.id)" class="text-red-500">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>