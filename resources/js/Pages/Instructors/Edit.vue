<script setup>
import { ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm} from "@inertiajs/vue3";

const props = defineProps({
    instructor: { type: Object, required: true },
});

// Initialize form data
const form = useForm({
    name: instructor?.name || "",
    email: instructor?.email || "",
    role_name: instructor?.role_name || "",
    department_id: instructor?.department_id || "",
    job_position: instructor?.job_position || "",
    employment_type: instructor?.employment_type || "",
    office_hours: instructor?.office_hours || "",
    profile_img: instructor?.profile_img || "",
    _method: 'PATCH',
});

// Submit the form
const submit = () => {
    form.patch(route("instructors.update", { instructor: form.id }));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Edit Instructor
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Modify the instructor details below.
            </p>
            <Form
                :form="form"
                :departments="departments"
                :roles="roles"
                :instructor="form"
                @submit="submit(instructor.id)"
            />
        </div>
    </AppLayout>
</template>
