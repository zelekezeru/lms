<script setup>
import { ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm} from "@inertiajs/vue3";

const props = defineProps({
    instructor: { type: Object, required: true },
    roles: { type: Object, required: true },
});

// Initialize form data
const form = useForm({
    name: props.instructor?.name || "",
    email: props.instructor?.email || "",
    role_name: props.instructor?.roleName || "",
    job_position: props.instructor?.jobPosition || "",
    hire_date: props.instructor?.hireDate || "",
    specialization: props.instructor?.specialization || "",
    bio: props.instructor?.bio || "",
    status: props.instructor?.status || "",
    employment_type: props.instructor?.employmentType || "",
    office_hours: props.instructor?.officeHours || "",
    status : props.instructor?.status || "",
    profile_img: props.instructor?.profileImg || "",
    role_id: props.instructor?.roleId || "",
    password: "instructors@default",
    password_confirmation: "instructors@default",    
    profile_img: "",
    _method: 'PATCH',
});

// Submit the form
const submit = (id) => {
    form.post(route("instructors.update", { instructor: id }));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Edit {{ props.instructor.name }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Modify the instructor details below.
            </p>
            <Form
                :form="form"
                :departments="departments"
                :roles="roles"
                @submit="submit(instructor.id)"
            />
        </div>
    </AppLayout>
</template>
