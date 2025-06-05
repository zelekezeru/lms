<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section class="max-w-xl mx-auto bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700 mt-8">
        <header class="mb-8">
            <h2 class="text-2xl font-bold text-indigo-700 dark:text-indigo-400 flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3zm0 0v6m0 0c-3.866 0-7-3.134-7-7a7 7 0 1114 0c0 3.866-3.134 7-7 7z"/></svg>
                Update Password
            </h2>
            <p class="mt-2 text-base text-gray-500 dark:text-gray-400">
                Keep your account secure with a strong, unique password.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="space-y-8">
            <div>
                <InputLabel
                    for="current_password"
                    value="Current Password"
                    class="text-base font-semibold text-gray-700 dark:text-gray-200"
                />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-2 block w-full bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-2 border-gray-200 dark:border-gray-700 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 transition"
                    autocomplete="current-password"
                    placeholder="Enter your current password"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <div>
                <InputLabel
                    for="password"
                    value="New Password"
                    class="text-base font-semibold text-gray-700 dark:text-gray-200"
                />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-2 block w-full bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-2 border-gray-200 dark:border-gray-700 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 transition"
                    autocomplete="new-password"
                    placeholder="Create a new password"
                />

                <InputError
                    :message="form.errors.password"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                    class="text-base font-semibold text-gray-700 dark:text-gray-200"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-2 block w-full bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 border-2 border-gray-200 dark:border-gray-700 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 transition"
                    autocomplete="new-password"
                    placeholder="Repeat your new password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <div class="flex items-center gap-6 mt-6">
                <PrimaryButton
                    :disabled="form.processing"
                    class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold px-6 py-2 rounded-lg shadow-lg hover:from-indigo-600 hover:to-purple-600 focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 transition"
                >
                    <span v-if="!form.processing">Save</span>
                    <svg v-else class="animate-spin h-5 w-5 text-white ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0 translate-y-2"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-base text-green-600 dark:text-green-400 font-medium flex items-center gap-1"
                    >
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
