<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    instructor: { type: Object, required: true },
    roles: { type: Object, required: true },
    courses: { type: Object, required: true },
    tracks: {
        type: Array,
        required: true,
    },
});

// Initialize form data
const form = useForm({
    name: props.instructor?.name || "",
    email: props.instructor?.email || "",
    courses: props.instructor.courses.map((course) => course.id) || "",
    role_name: props.instructor?.roleName || "",
    contact_phone: props.instructor?.ContactPhone || "",
    hire_date: props.instructor?.hireDate || "",
    specialization: props.instructor?.specialization || "",
    bio: props.instructor?.bio || "",
    status: props.instructor?.status || "",
    employment_type: props.instructor?.employmentType || "",
    status: props.instructor?.status || "",
    profile_img: props.instructor?.profileImg || "",
    role_id: props.instructor?.roleId || "",
    password: "instructors@default",
    password_confirmation: "instructors@default",
    profile_img: "",
    _method: "PATCH",
});

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

// Submit the form
const submit = (id) => {
    form.post(route("instructors.update", { instructor: id }));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit {{ props.instructor.name }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Modify the Instructor details below.
                </p>
            </div>
            <!-- instructor Image -->
            <div class="flex justify-center mb-8">
                <div
                    v-if="!imageLoaded"
                    class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                ></div>
                <img
                    v-show="imageLoaded"
                    class="rounded-full w-44 h-44 object-contain bg-gray-400"
                    :src="instructor.profileImg"
                    :alt="`profile image of ` + instructor.name"
                    @load="imageLoaded = true"
                />
            </div>

            <div class="bg-white-100 dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <Form
                    :form="form"
                    :tracks="tracks"
                    :roles="roles"
                    :courses="courses"
                    @submit="submit(instructor.id)"
                />
            </div>
        </div>
    </AppLayout>
</template>
