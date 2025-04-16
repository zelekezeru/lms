<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

const form = ref({
    course_id: '',
    section_ids: [],
    errors: {},
    processing: false,
});

const courses = ref([]); // Replace with actual courses data from props or API
const sections = ref([]); // Replace with actual sections data from props or API

const submit = () => {
    form.value.processing = true;
    router.post(route('courses.sections.attach'), form.value, {
        onSuccess: () => {
            alert('Course and sections attached successfully!');
            form.value.processing = false;
        },
        onError: (errors) => {
            form.value.errors = errors;
            form.value.processing = false;
        },
    });
};
</script>

<template>
    <Head title="Attach Courses to Sections" />
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Attach Courses to Sections</h2>
        </template>
        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <InputLabel for="course_id" value="Select Course" />
                    <SelectInput
                        id="course_id"
                        v-model="form.course_id"
                        :options="courses"
                        placeholder="Select a course"
                    />
                    <InputError :message="form.errors.course_id" class="mt-2" />
                </div>

                <div class="mb-4">
                    <InputLabel for="section_ids" value="Select Sections" />
                    <SelectInput
                        id="section_ids"
                        v-model="form.section_ids"
                        :options="sections"
                        multiple
                        placeholder="Select sections"
                    />
                    <InputError :message="form.errors.section_ids" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <PrimaryButton :disabled="form.processing">
                        <span v-if="!form.processing">Attach</span>
                        <span v-else>Processing...</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>