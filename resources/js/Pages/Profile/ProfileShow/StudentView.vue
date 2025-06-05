<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <StudentLayout>

        <section class="min-h-screen bg-gradient-to-br from-indigo-100 to-white dark:from-gray-900 dark:to-gray-800 py-10 px-4">
            <div
            class="max-w-5xl mx-auto backdrop-blur-md bg-white/80 dark:bg-gray-800/80 rounded-3xl shadow-2xl ring-1 ring-gray-300 dark:ring-gray-700 p-8 transition-all duration-300 animate-fade-in"
            >
            <div class="flex flex-col md:flex-row items-center gap-8">
                <!-- Profile Image -->
                <div class="w-40 h-40 md:w-48 md:h-48 rounded-full overflow-hidden border-4 border-indigo-500 shadow-lg">
                <img
                    v-if="props.user.profileImg"
                    :src="props.user.profileImg"
                    alt="Profile"
                    class="object-cover w-full h-full"
                />
                <div
                    v-else
                    class="flex items-center justify-center w-full h-full bg-indigo-100 dark:bg-gray-700 text-indigo-700 dark:text-white text-5xl font-semibold uppercase"
                >
                    {{ props.user.name.charAt(0) }}
                </div>
                </div>

                <!-- User Info -->
                <div class="text-center md:text-left flex-1 space-y-2">
                <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    {{ props.user.name }}
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Email: {{ props.user.email }}
                </p>
                <!-- If props.user id exists -->
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    User ID: <span class="font-medium">{{ props.user.userId }}</span>
                </p>
                
                {{ props.user.useId }}
                <!-- If props.user created_at exists -->

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Phone: <span class="font-medium">{{ props.user.phone }}</span>
                </p>
                </div>
            </div>

            <!-- Divider -->
            <div class="mt-8 border-t border-gray-200 dark:border-gray-700"></div>
            </div>
        </section>
    </StudentLayout>
</template>

<style scoped>
@keyframes fade-in {
  0% {
    opacity: 0;
    transform: translateY(30px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.8s ease-out;
}
</style>
