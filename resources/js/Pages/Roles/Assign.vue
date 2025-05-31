<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, defineProps, onMounted } from "vue";

const props = defineProps({
    role: Object,
    permissions: Array,
    attachedPermissions: Array,
});
const form = ref({ permissions: [] });

// Initialize selectedPermissions with existing attached permissions
const selectedPermissions = ref([...props.attachedPermissions]);

const togglePermission = (permissionId) => {
    if (selectedPermissions.value.includes(permissionId)) {
        selectedPermissions.value = selectedPermissions.value.filter(
            (id) => id !== permissionId
        );
    } else {
        selectedPermissions.value.push(permissionId);
    }
};

const submit = () => {
    form.value.permissions = selectedPermissions.value;
    router.put(route("roles.attach", props.role.id), form.value);
};
</script>

<template>
    <Head :title="$t('role.assign_permissions_title')" />
    <AppLayout>
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                {{ $t('role.assign_permissions_to') }}
                <span class="text-info">{{ role.name }}</span> {{ $t('role.role') }}
            </h2>
        </div>

        <form
            @submit.prevent="submit"
            class="dark:bg-gray-800 p-4 shadow rounded"
        >
            <div class="w-full py-5">
                <label class="block text-gray-100 dark:text-white mb-2">
                    {{ $t('role.select_permissions_for_role') }}
                    <span class="text-info">"{{ role.name }}"</span>
                </label>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
                >
                    <div
                        v-for="permission in permissions"
                        :key="permission.id"
                        class="flex items-center"
                    >
                        <input
                            type="checkbox"
                            :id="'perm-' + permission.id"
                            :value="permission.id"
                            :checked="
                                selectedPermissions.includes(permission.id)
                            "
                            v-model="selectedPermissions"
                            class="text-primary focus:ring-primary border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        />
                        <label
                            :for="'perm-' + permission.id"
                            class="ml-2 text-gray-700 dark:text-gray-100"
                        >
                            {{ permission.name }}
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="px-6 py-2 text-white bg-blue-800 mt-4">
                {{ $t('common.save', 'Save') }}
            </button>
        </form>
    </AppLayout>
</template>
