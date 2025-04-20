<script setup>
import { defineProps } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";

// Define the expected props
const props = defineProps({
    form: Object,
    departments: { type: Array, required: true },
    roles: { type: Array, required: true },
});

const emits = defineEmits(["submit"]);

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
    <form
        @submit.prevent="emits('submit')"
        enctype="multipart/form-data">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- User Details -->
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
            
            <!-- Phone -->
            <div>
                <InputLabel for="contact_phone" value="Contact Phone" />
                <TextInput
                    id="contact_phone"
                    type="text"
                    v-model="form.contact_phone"
                    class="w-full"
                />
                <InputError :message="form.errors.contact_phone" />
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
                    <option value="Full-time">Full-Time</option>
                    <option value="Part-time">Part-Time</option>
                    <option value="Contract">Contract</option>
                    <option value="Guest">Guest</option>
                </select>
                <InputError :message="form.errors.employment_type" />
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
</template>

<style scoped>
/* Smooth fade-in effect for image preview */
img {
    transition: opacity 0.3s ease-in-out;
}
</style>
