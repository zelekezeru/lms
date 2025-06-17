11<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import LanguageToggle from "@/Components/LanguageToggle.vue";

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
    <div
        class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900"
    >
        <div class="w-full max-w-md px-6 py-4">
            <!-- Language toggle outside the card -->
            <div class="flex justify-end mb-4">
                <LanguageToggle />
            </div>

            <!-- Login Card -->
            <div class="p-8 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                <h2
                    class="mb-6 text-3xl font-bold text-center text-gray-800 dark:text-white"
                >
                    {{ $t("login") }}
                </h2>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label
                            for="email"
                            class="block mb-1 text-sm text-gray-700 dark:text-gray-300"
                        >
                            {{ $t("email") }}
                        </label>
                        <TextInput
                            id="email"
                            v-model="form.email"
                            required
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                            placeholder="you@example.com"
                        />
                        <span
                            v-if="form.errors.email"
                            class="text-red-500 text-sm"
                        >
                            {{ form.errors.email }}
                        </span>
                    </div>

                    <!-- Password -->
                    <div>
                        <label
                            for="password"
                            class="block mb-1 text-sm text-gray-700 dark:text-gray-300"
                        >
                            {{ $t("password") }}
                        </label>
                        <TextInput
                            type="password"
                            id="password"
                            v-model="form.password"
                            required
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                            placeholder="••••••••"
                        />
                        <span
                            v-if="form.errors.password"
                            class="text-red-500 text-sm"
                        >
                            {{ form.errors.password }}
                        </span>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="remember"
                            v-model="form.remember"
                            class="mr-2"
                        />
                        <label
                            for="remember"
                            class="text-sm text-gray-600 dark:text-gray-300"
                        >
                            {{ $t("remember_me") }}
                        </label>
                    </div>

                    <!-- Login Button -->
                    <div class="flex text-center">
                        <PrimaryButton type="submit" class="w-full">
                            <span class="w-full text-center block">{{
                                $t("login")
                            }}</span>
                        </PrimaryButton>
                    </div>
                </form>

                <!-- Links -->
                <p class="mt-4 text-center">
                    <Link
                        href="/forgot-password"
                        class="text-blue-600 hover:underline dark:text-blue-400"
                    >
                        {{ $t("forgot_password") }}
                    </Link>
                </p>
            </div>
        </div>
    </div>
</template>
