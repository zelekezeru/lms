<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

// Define props to accept the user data
const props = defineProps({
    user: { type: Object, required: true },
    roles: { type: Array, required: true },
    userRoles: { type: Array, required: true },
});

// Initialize the form with the user data or empty values
const form = useForm({
    id: props.user.id || "",
    name: props.user.name || "",
    email: props.user.email || "",
    password: "",
    password_confirmation: "",
    phone: props.user.phone | "",
    role_name: props.user.roles?.[0]?.name || "", // Get the first role name if available
    profile_img: props.user.profile_img || "",
    _method: "PUT", // Indicates PUT method for the update
});

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

// Submit the form, passing the user ID to the route
const submit = (id) => {
    form.post(route("users.update", { user: id }));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit {{ props.user.name }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Modify the user details below.
                </p>
                <div class="flex justify-center mb-8">
                    <div
                        v-if="!imageLoaded"
                        class="rounded-full w-44 h-44 bg-gray-300 dark:bg-gray-700 animate-pulse"
                    ></div>
                    
                    <img
                        v-show="imageLoaded"
                        class="rounded-full w-44 h-44 object-contain bg-gray-400"
                        :src="user.profileImg"
                        :alt="`Logo of ` + user.name"
                        @load="handleImageLoad"
                    />
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <!-- Pass the form to the Form component, and handle submit with user.id -->
                <Form 
                    :form="form" 
                    :roles="roles" 
                    :user="props.user"
                    :userRoles="userRoles"
                    @submit="submit(props.user.id)" 
                />
            </div>
        </div>
    </AppLayout>
</template>
