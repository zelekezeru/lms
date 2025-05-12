<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";
import { defineProps, ref } from "vue";

const props = defineProps({
    userDocument: {
        type: Object,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    user_id: props.userDocument?.user_id || '',
    title: props.userDocument?.title || '',
    description: props.userDocument?.description || '',
    image: props.userDocument?.image || '', // Existing image
    file: props.userDocument?.file || '', // Existing document
    _method: 'PATCH', // For PATCH request
});

const imageLoaded = ref(false);

const handleImageLoad = () => {
    console.log("hello");

    imageLoaded.value = true;
};

const submit = (id) => {
    form.post(route('userDocuments.update', { userDocument: id }), {
        forceFormData: true, // Ensures file data is included
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Document</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Update the details of the document.
                </p>
            </div>
            <Form :form="form" @submit="submit(userDocument.id)" />
        </div>
    </AppLayout>
</template>
