<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    section: Object,
    users: Array,
    programs: Array,
    years: Array,
    centers: Array,
});
console.log(props.section);

const form = useForm({
    name: props.section?.name || "",
    code: props.section?.code || "",
    user_id: props.section.user?.id || "",
    program_id: props.section.program?.id || "",
    track_id: props.section.track?.id || "",
    year_id: props.section.year?.id || "",
    semester_id: props.section.semester?.id || "",
    study_mode_id: props.section.studyMode?.id || "",
    center_id: props.section.center?.id || "",
    _method: "PATCH",
});

const submit = () => {
    form.post(route("sections.update", { section: props.section.id }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Edit Section
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Modify the details of the section.
                </p>
            </div>
            <Form
                :form="form"
                :users="users"
                :programs="programs"
                :years="years"
                :centers="centers"
                @submit="submit"
            />
        </div>
    </AppLayout>
</template>
