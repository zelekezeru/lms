<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <AppLayout>
        <template #header>
            <h2
                class="flex flex-col text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100"
            >
                Profile
            </h2>
        </template>

        <div class="py-12 flex flex-col mt-5">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Profile Information Form -->
                <div>
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- Update Password Form -->
                <div>
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
