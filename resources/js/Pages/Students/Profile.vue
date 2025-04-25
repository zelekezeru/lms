<script setup>
import { defineProps, defineEmits } from "vue";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    student: Object,
    user: Object,
    sections: Array,
    program: Object,
    track: Object,
});

const student = ref(props.student);
const user = ref(props.user);

const form = useForm({
    profile_img: null,
    payment_status: student.value.is_approved || 0,
    section_id: student.value.section_id || "",
    enroll: student.value.is_enrolled || 0,
    student_id: student.value.student_id || "",
    document_submitted: student.value.is_completed || 0,
    is_scholarship: student.value.is_scholarship || 0,
});

// Handle profile image selection and preview
const handleFileChange = (e) => {
    const file = e.target.files[0];
    form.profile_img = file;

    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            form.imagePreview = event.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        form.imagePreview = null;
    }
};

const submit = () => {
    form.post(route("students.updateProfile", { student: student.value.id }), {
        forceFormData: true,
    });
};

const emit = defineEmits(["submit"]);
</script>

<template>
    <AppLayout>
        <div class="max-w-8xl mx-auto p-6">
            <h1
                class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
            >
                Complete Registration
            </h1>

            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition my-2"
            >
                <div class="grid sm:grid-cols-2 gap-4 my-4">
                    <!-- Full Name -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Full Name</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.first_name }}
                            {{ student.middle_name }}
                            {{ student.last_name }}
                        </span>
                    </div>
                    <!-- Student ID -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            ID Number</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.id_no }}
                        </span>
                    </div>

                    <!-- Email -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Email</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ student.user.email }}
                        </span>
                    </div>
                    <!-- Program -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-gray-400"
                            >Program</span
                        >
                        <span
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ program.name }}
                        </span>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <h3
                    class="text-2xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center"
                >
                    Registration Verifications
                </h3>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8">
                        <!-- Payment Status -->
                        <div>
                            <InputLabel
                                for="payment_status"
                                value="Payment Status"
                            />
                            <select
                                id="payment_status"
                                v-model="form.payment_status"
                                class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                                <option value="">Select Payment Status</option>
                                <option value="1">Paid</option>
                                <option value="0">Unpaid</option>
                            </select>
                            <InputError :message="form.errors.payment_status" />
                        </div>

                        <!-- Profile Image Upload -->
                        <div>
                            <InputLabel
                                for="profile_img"
                                value="Profile Image"
                            />
                            <div class="mt-1 flex items-center gap-4">
                                <label
                                    for="profile_img"
                                    class="cursor-pointer px-4 py-2 text-white flex items-center gap-2 rounded-md shadow transition bg-black hover:bg-blue-700"
                                >
                                    <PhotoIcon class="w-5 h-5" /> Upload Image
                                </label>
                                <input
                                    id="profile_img"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleFileChange"
                                />
                                <div
                                    v-if="form.imagePreview"
                                    class="w-16 h-16 rounded-full border shadow overflow-hidden"
                                >
                                    <img
                                        :src="form.imagePreview"
                                        alt="Profile Preview"
                                        class="object-cover w-full h-full"
                                    />
                                </div>
                            </div>
                            <InputError :message="form.errors.profile_img" />
                        </div>

                        <!-- Track Section Select -->
                        <div>
                            <InputLabel
                                for="section_id"
                                value="Select Track Section"
                            />
                            <select
                                id="section_id"
                                v-model="form.section_id"
                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100"
                            >
                                <option value="">Select Section</option>
                                <option
                                    v-for="section in sections"
                                    :key="section.id"
                                    :value="section.id"
                                >
                                    {{ track.name }} - {{ section.name }}
                                </option>
                            </select>
                            <InputError
                                :message="form.errors?.section_id"
                                class="mt-2"
                            />
                        </div>

                        <!-- Enroll to Courses -->
                        <div>
                            <InputLabel
                                for="enroll"
                                value="Enroll to Courses"
                            />
                            <div class="mt-2 flex items-center gap-4">
                                <label
                                    class="flex items-center gap-2 text-gray-700 dark:text-gray-100"
                                >
                                    <input
                                        type="radio"
                                        id="enroll"
                                        value="1"
                                        v-model="form.enroll"
                                        class="form-radio text-indigo-600 focus:ring-indigo-500"
                                    />
                                    Enroll
                                </label>
                                <label
                                    class="flex items-center gap-2 text-gray-700 dark:text-gray-100"
                                >
                                    <input
                                        type="radio"
                                        value="0"
                                        v-model="form.enroll"
                                        class="form-radio text-indigo-600 focus:ring-indigo-500"
                                    />
                                    Do Not Enroll
                                </label>
                            </div>
                            <InputError :message="form.errors.enroll" />
                        </div>

                        <!-- Document Submitted -->
                        <div>
                            <InputLabel
                                for="document_submitted"
                                value="Document Submitted"
                            />
                            <div class="mt-2 flex items-center gap-4">
                                <label
                                    class="flex items-center gap-2 text-gray-700 dark:text-gray-100"
                                >
                                    <input
                                        type="radio"
                                        id="document_submitted"
                                        value="1"
                                        v-model="form.document_submitted"
                                        class="form-radio text-indigo-600 focus:ring-indigo-500"
                                    />
                                    Submitted
                                </label>
                                <label
                                    class="flex items-center gap-2 text-gray-700 dark:text-gray-100"
                                >
                                    <input
                                        type="radio"
                                        value="0"
                                        v-model="form.document_submitted"
                                        class="form-radio text-indigo-600 focus:ring-indigo-500"
                                    />
                                    Not Submitted
                                </label>
                            </div>
                            <InputError
                                :message="form.errors.document_submitted"
                            />
                        </div>

                        <!-- Scholarship Status -->
                        <div>
                            <InputLabel
                                for="is_scholarship"
                                value="Scholarship Status"
                            />
                            <div class="mt-2 flex items-center gap-4">
                                <label
                                    class="flex items-center gap-2 text-gray-700 dark:text-gray-100"
                                >
                                    <input
                                        type="radio"
                                        id="is_scholarship"
                                        value="1"
                                        v-model="form.is_scholarship"
                                        class="form-radio text-indigo-600 focus:ring-indigo-500"
                                    />
                                    Scholarship
                                </label>
                                <label
                                    class="flex items-center gap-2 text-gray-700 dark:text-gray-100"
                                >
                                    <input
                                        type="radio"
                                        value="0"
                                        v-model="form.is_scholarship"
                                        class="form-radio text-indigo-600 focus:ring-indigo-500"
                                    />
                                    Not Scholarship
                                </label>
                            </div>
                            <InputError :message="form.errors.is_scholarship" />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-center">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <span v-if="!form.processing">Submit</span>
                            <span v-else>Submitting...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
