<script setup>
import { defineProps, defineEmits } from "vue";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";

const props = defineProps({ student: Object, user: Object });

const student = ref(props.student);
const user = ref(props.user);

const form = useForm({
    profile_img: null,
    payment_status: student.value.payment_status || "",
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
        <div class="max-w-2xl mx-auto p-6">
            <h1 class="text-3xl font-semibold mb-6 text-gray-900 dark:text-gray-100 text-center">
                Edit {{ student.student_name }} {{ student.father_name }}'s Profile
            </h1>
            
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 transition"
            >
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Payment Status -->
                        <div>
                            <InputLabel for="payment_status" value="Payment Status" />
                            <select
                                id="payment_status"
                                v-model="form.payment_status"
                                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                                <option value="">Select Payment Status</option>
                                <option value="Paid">Paid</option>
                                <option value="Unpaid">Unpaid</option>
                            </select>
                            <InputError :message="form.errors.payment_status" />
                        </div>

                        <!-- Profile Image -->
                        <div>
                            <InputLabel for="profile_img" value="Profile Image" />
                            <div class="flex items-center gap-4">
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
