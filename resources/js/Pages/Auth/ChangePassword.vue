<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('password.update'), {
        onSuccess: () => {
            window.location.href = route('dashboard');
            Swal.fire({
                title: 'Success',
                text: 'Your password has been changed successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            });

        },
        onError: () => {
            Swal.fire({
                title: 'Error',
                text: 'There was an error changing your password.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        },
        onFinish: () => form.reset('password', 'password_confirmation', 'current_password'),
    });
};

onMounted(() => {
    // Remove forced dark mode
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <Head :title="$t('Change Password')" />
        <!-- Custom Header -->
        <header class="bg-gray-100 dark:bg-gray-800 py-6 mb-6 rounded-lg shadow">
            <div class="max-w-3xl mx-auto px-4 flex flex-col items-center">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">{{$t('Change Password')}}</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400 text-center">
                    {{$t('change_password_info')}}
                </p>
            </div>
        </header>

        <form
            @submit.prevent="submit"
            class="bg-white dark:bg-gray-900 rounded-lg shadow-md p-8 max-w-md mx-auto mt-8"
        >

            <div class="mt-4">
                <InputLabel for="current_password" :value="$t('Old Password')" class="text-gray-700 dark:text-gray-200" />

                <TextInput
                    id="current_password"
                    type="password"
                    class="mt-1 block w-full bg-white text-gray-900 border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
                    v-model="form.current_password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.current_password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="$t('New Password')" class="text-gray-700 dark:text-gray-200" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full bg-white text-gray-900 border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    :value="$t('Confirm New Password')"
                    class="text-gray-700 dark:text-gray-200"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full bg-white text-gray-900 border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>
            <!-- hidden field password_changed -->
            <input type="hidden" name="password_changed" value="1" />

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{$t('Change Password Button')}}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
