<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm, usePage } from "@inertiajs/vue3";

import { ref } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";


// Props for form data
const props = defineProps({
    user: { type: Object, required: true },
});


const form = useForm({
    user_id: props.user.id, 
    title: '',
    description: '',
    image: null, 
    file: null,  
});

const submit = () => {
    form.post(route('userDocuments.store'), {
    forceFormData: true, // Required for file uploads
});
};

</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Add Document of - {{ props.user.name }}</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Fill in the details to register a user document.
                </p>
            </div>
            <Form :form="form" 
            :user="user"
            @submit="submit" />

        </div>
    </AppLayout>
</template>
