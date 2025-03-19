<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
            <h2 class="mb-6 text-3xl font-bold text-center text-gray-800">
                Login
            </h2>
            <form
                @submit.prevent="submit"
                class="flex flex-col justify-center items-center"
            >
                <div class="mb-4 w-full">
                    <label
                        for="email"
                        class="block mb-2 text-sm font-medium text-gray-700"
                        >Email</label
                    >
                    <TextInput
                        id="email"
                        v-model="form.email"
                        required
                        class="block w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <span v-if="form.errors.email" class="text-red-500">{{
                        form.errors.email
                    }}</span>
                </div>
                <div class="mb-4 w-full">
                    <label
                        for="password"
                        class="block mb-2 text-sm font-medium text-gray-700"
                        >Password</label
                    >
                    <TextInput
                        type="password"
                        id="password"
                        v-model="form.password"
                        required
                        class="block w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <span v-if="form.errors.password" class="text-red-500">{{
                        form.errors.password
                    }}</span>
                </div>
                <div class="flex items-center mb-4 w-full">
                    <input
                        type="checkbox"
                        id="remember"
                        v-model="form.remember"
                        class="mr-2"
                    />
                    <label for="remember" class="text-sm text-gray-600"
                        >Remember Me</label
                    >
                </div>
                <div class="flex justify-center w-full">
                    <PrimaryButton
                        type="submit"
                        class="w-full flex justify-center items-center"
                    >
                        Login
                    </PrimaryButton>
                </div>
            </form>
            <p class="mt-4 text-center">
                <Link
                    href="/forgot-password"
                    class="text-blue-600 hover:underline"
                    >Forgot Password?</Link
                >
            </p>
            <p class="mt-2 text-center">
                <Link href="/register" class="text-blue-600 hover:underline"
                    >Create an Account</Link
                >
            </p>
        </div>
    </div>
</template>
