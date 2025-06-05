<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-8 max-w-xl mx-auto bg-gradient-to-br from-white via-gray-50 to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700">
        <header class="flex items-center gap-4">
            <div class="flex-shrink-0 bg-red-100 dark:bg-red-900 rounded-full p-3">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v2H7V5a2 2 0 012-2h4zm-7 4h14"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">
                    Delete Account
                </h2>
                <p class="mt-1 text-base text-gray-600 dark:text-gray-400">
                    Permanently remove your account and all associated data. This action cannot be undone.
                </p>
            </div>
        </header>

        <DangerButton
            @click="confirmUserDeletion"
            class="w-full py-3 text-lg font-semibold rounded-xl bg-gradient-to-r from-red-500 to-red-700 hover:from-red-600 hover:to-red-800 shadow-lg transition-all duration-200"
        >
            Delete Account
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 max-w-lg mx-auto">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v2H7V5a2 2 0 012-2h4zm-7 4h14"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Confirm Account Deletion
                    </h2>
                </div>
                <p class="mb-6 text-gray-700 dark:text-gray-300">
                    Are you sure you want to delete your account? All your data will be <span class="font-bold text-red-600 dark:text-red-400">permanently deleted</span>. Please enter your password to confirm.
                </p>

                <div class="mb-6">
                    <InputLabel
                        for="password"
                        value="Password"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-200 border-2 border-gray-200 dark:border-gray-700 rounded-lg shadow-sm focus:ring-2 focus:ring-red-400 dark:focus:ring-red-500 px-4 py-3 text-lg"
                        placeholder="Enter your password"
                        @keyup.enter="deleteUser"
                        autocomplete="current-password"
                    />

                    <InputError
                        :message="form.errors.password"
                        class="mt-2 text-red-600 dark:text-red-400 text-sm"
                    />
                </div>

                <div class="flex justify-end gap-3">
                    <SecondaryButton
                        @click="closeModal"
                        class="px-6 py-2 rounded-lg text-base font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                    >
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="px-6 py-2 rounded-lg text-base font-semibold bg-gradient-to-r from-red-500 to-red-700 hover:from-red-600 hover:to-red-800 text-white shadow-md transition disabled:opacity-40"
                        :class="{ 'opacity-40': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
