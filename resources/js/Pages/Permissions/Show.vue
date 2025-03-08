<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({ permission: Object });

const deletepermission = (id) => {
    if (confirm('Are you sure you want to delete this permission?')) {
        // Add your delete logic here
    }
};
</script>

<template>
    <Head title="Permission Details" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Permission Details</h2>

        </template>
        <div class="p-6 bg-white shadow rounded-lg">

            <Link :href="route('permissions.create')" class="btn btn-primary mb-4">Add Permissions</Link>

            <div class="mb-4">
                <p><strong>Permission Name:</strong> {{ permission.name }}</p>
            </div>
            <div class="mb-4">
                <p><strong>Permission Users:</strong> {{ permission.users }}</p>
            </div>
            <div class="flex justify-between items-center mt-6">
                <Link :href="route('permissions.edit', permission.id)" class="text-blue-500">Edit</Link>
                <button @click="deletepermission(permission.id)" class="text-red-500">Delete</button>
                <br>
            </div>

                
            <div class="mt-6 text-right">
                <Link :href="route('permissions.index')" class="text-blue-500">Back to List</Link>
            </div>
        </div>
    </AppLayout>
</template>
