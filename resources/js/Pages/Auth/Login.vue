<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm } from "@inertiajs/vue3";
import LanguageToggle from "@/Components/LanguageToggle.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
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
        class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 flex items-center justify-center px-4"
    >
        <div class="w-full max-w-md relative">
            <!-- Floating blobs -->
            <span
                class="absolute -top-10 -left-10 w-24 h-24 bg-gradient-to-tr from-blue-200 via-blue-400 to-blue-600 opacity-30 rounded-full blur-3xl animate-pulse"
            ></span>
            <span
                class="absolute -bottom-10 -right-10 w-24 h-24 bg-gradient-to-br from-purple-200 via-purple-400 to-purple-600 opacity-30 rounded-full blur-3xl animate-pulse"
            ></span>

            <!-- Language Toggle -->
            <div class="flex justify-end mb-4 z-10 relative">
                <LanguageToggle />
            </div>

            <!-- Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 relative z-6 border border-blue-100 dark:border-gray-700"
            >
                <h2
                    class="text-3xl font-extrabold text-center text-transparent bg-clip-text bg-gradient-to-r from-blue-700 via-blue-500 to-blue-400 mb-6 drop-shadow-md"
                >
                    {{ $t("login") }}
                </h2>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label
                            for="email"
                            class="text-sm text-gray-700 dark:text-gray-300 mb-1 block"
                        >
                            {{ $t("email") }}
                        </label>
                        <TextInput
                            id="email"
                            v-model="form.email"
                            required
                            placeholder="you@example.com"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white"
                        />
                        <span
                            v-if="form.errors.email"
                            class="text-red-500 text-sm mt-1 block"
                        >
                            {{ form.errors.email }}
                        </span>
                    </div>

                    <!-- Password -->
                    <div>
                        <label
                            for="password"
                            class="text-sm text-gray-700 dark:text-gray-300 mb-1 block"
                        >
                            {{ $t("password") }}
                        </label>
                        <TextInput
                            type="password"
                            id="password"
                            v-model="form.password"
                            required
                            placeholder="••••••••"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white"
                        />
                        <span
                            v-if="form.errors.password"
                            class="text-red-500 text-sm mt-1 block"
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

                    <!-- Button -->
                    <div class="pt-2">
                        <PrimaryButton type="submit">
                            {{ $t("login") }}
                        </PrimaryButton>
                    </div>
                </form>

                <!-- Forgot Password -->
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
