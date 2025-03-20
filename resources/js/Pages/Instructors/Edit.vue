<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { usePage, useRoute } from "@inertiajs/vue3";

const { departments, roles } = usePage().props;
const routeParams = useRoute().params;
const instructor = ref(null);

// Fetch instructor details when route changes (for editing)
onMounted(() => {
    axios
        .get(route("instructors.show", routeParams.id))
        .then((response) => {
            instructor.value = response.data;
        })
        .catch((error) => {
            console.error(error);
        });
});
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Edit Instructor
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Modify the Instructor details below.
            </p>
            <Form
                :departments="departments"
                :roles="roles"
                :instructor="instructor"
            />
        </div>
    </AppLayout>
</template>
