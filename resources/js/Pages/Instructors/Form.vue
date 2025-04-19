<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed, watchEffect } from "vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";

// Define the expected props
const props = defineProps({
    form: Object,
    roles: { type: Array, required: true },
});

const emits = defineEmits(["submit"]);

const imagePreview = ref(props.instructor?.profile_img || null);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    props.form.profile_img = file; // Assign file to form
    
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        imagePreview.value = props.instructor?.profile_img || null;
    }
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
            <form
                @submit.prevent="emits('submit')"
                enctype="multipart/form-data"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- User Name -->
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            class="w-full"
                        />
                        <InputError :message="form.errors.name" />
                    </div>
                    <!-- User Email -->

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            class="w-full"
                        />
                        <InputError :message="form.errors.email" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    
                    <!-- Job Position -->
                    <div>
                        <InputLabel for="job_position" value="Job Position" />
                        <TextInput
                            id="job_position"
                            type="text"
                            v-model="form.job_position"
                            class="w-full"
                        />
                        <InputError :message="form.errors.job_position" />
                    </div>
                    
                    <!-- Role -->
                    <div>
                        <InputLabel for="role" value="Select Role" />
                        <select
                            id="role"
                            v-model="form.role_name"
                            required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                        >
                            <option disabled value="">Select Role</option>
                            <option
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.name"
                                :selected="role.name === form.role_name"
                            >
                                {{ role.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.role_name" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- Employment Type -->
                    <div>
                        <InputLabel
                            for="employment_type"
                            value="Employment Type"
                        />
                        <select
                            v-model="form.employment_type"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                        >
                            <option disabled value="">
                                Select Employment Type
                            </option>
                            <option value="full-time">Full-Time</option>
                            <option value="part-time">Part-Time</option>
                        </select>
                        <InputError :message="form.errors.employment_type" />
                    </div>

                    <!-- Office Hours -->
                    <div>
                        <InputLabel for="office_hours" value="Office Hours" />
                        <TextInput
                            id="office_hours"
                            type="text"
                            v-model="form.office_hours"
                            class="w-full"
                        />
                        <InputError :message="form.errors.office_hours" />
                    </div>

                    <!-- Hire Date -->
                    <div>
                        <InputLabel for="hire_date" value="Hire Date" />
                        <TextInput
                            id="hire_date"
                            type="date"
                            v-model="form.hire_date"
                            class="w-full"
                        />
                        <InputError :message="form.errors.hire_date" />
                    </div>

                    <!-- Specialization -->
                    <div>
                        <InputLabel
                            for="specialization"
                            value="Specialization"
                        />
                        <TextInput
                            id="specialization"
                            type="text"
                            v-model="form.specialization"
                            class="w-full"
                        />
                        <InputError :message="form.errors.specialization" />
                    </div>

                    <!-- Status -->
                    <div>
                        <label
                            for="status"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Status</label
                        >
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                            <option value="Inactive">Inactive</option>
                            <option value="Active">Active</option>
                            <option value="Suspended">Suspended</option>
                            <option value="Terminated">Terminated</option>
                        </select>
                        <div
                            v-if="form.errors.status"
                            class="text-red-500 text-sm"
                        >
                            {{ form.errors.status }}
                        </div>
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <InputLabel
                        for="bio"
                        value="Bio"
                        class="block mb-1 text-gray-800 dark:text-gray-200"
                    />
                    <textarea
                        v-model="form.bio"
                        id="bio"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                        rows="3"
                    ></textarea>
                    <InputError
                        :message="form.errors.bio"
                        class="mt-2 text-sm text-red-500"
                    />
                </div>

                <!-- Profile Image Upload -->
                <div class="mt-4">
                    <InputLabel for="profile_img" value="Profile Image" />
                    <div class="flex items-center gap-4">
                        <label
                            for="profile_img"
                            class="cursor-pointer px-4 py-2 text-white flex items-center gap-2 rounded-md shadow transition bg-black hover:bg-blue-700"
                        >
                            <PhotoIcon class="w-5 h-5" />
                            Upload Image
                        </label>

                        <input
                            id="profile_img"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleFileChange"
                        />

                        <!-- Image Preview -->
                        <div
                            v-if="imagePreview"
                            class="w-16 h-16 rounded-full border shadow overflow-hidden"
                        >
                            <img
                                :src="imagePreview"
                                alt="Profile Preview"
                                class="object-cover w-full h-full"
                            />
                        </div>
                    </div>
                    <InputError :message="form.errors.profile_img" />
                </div>

                <div class="mt-6 flex justify-center">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <span v-if="!form.processing">Submit</span>
                        <span v-else>Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Smooth fade-in effect for image preview */
img {
    transition: opacity 0.3s ease-in-out;
}
</style>
