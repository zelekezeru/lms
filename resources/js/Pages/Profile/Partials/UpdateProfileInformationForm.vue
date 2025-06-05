<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    profile_img: user.profile_img,
});
</script>

<template>
    <section class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 mt-8 border border-gray-200 dark:border-gray-700">
        <header class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Profile Information
            </h2>
            <p class="mt-2 text-base text-gray-600 dark:text-gray-300">
                Update your account's profile information and Profile image.
            </p>
        </header>

        <form @submit.prevent="form.post(route('profile.update'))" enctype="multipart/form-data">
            
            <div>
                <InputLabel for="name" value="Name" class="text-lg font-medium text-gray-800 dark:text-gray-200" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-2 block w-full bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-2 border-gray-200 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 transition"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError
                    class="mt-2 text-sm text-red-600 dark:text-red-400"
                    :message="form.errors.name"
                />
            </div>
            <!-- update profile_img -->
            <div>
                <InputLabel for="profile_img" value="Profile Image" class="text-lg font-medium text-gray-800 dark:text-gray-200 my-6" />

                <input
                    id="profile_img"
                    type="file"
                    accept="image/*"
                    class="mt-2 block w-full bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-2 border-gray-200 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 transition"
                    @change="e => form.profile_img = e.target.files[0]"
                />

                <InputError
                    class="mt-2 text-sm text-red-600 dark:text-red-400"
                    :message="form.errors.profile_img"
                />

                <div v-if="user.profile_img" class="mt-4">
                    <img
                        :src="user.profile_img"
                        alt="Current Profile Image"
                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600"
                    />
                </div>
            </div>
            
            <div class="flex items-center gap-4 my-6">
                <PrimaryButton
                    :disabled="form.processing"
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-xl font-semibold shadow-md hover:from-indigo-700 hover:to-purple-700 dark:bg-gradient-to-r dark:from-indigo-500 dark:to-purple-500 dark:hover:from-indigo-600 dark:hover:to-purple-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition"
                >
                    <span v-if="form.processing" class="animate-spin mr-2 inline-block align-middle">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                    </span>
                    Save
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-green-600 dark:text-green-400 font-medium ml-2"
                    >
                        <svg class="w-5 h-5 inline-block mr-1 align-text-bottom text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
