<script setup>
import { defineProps } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";

// Define the expected props: form data and departments/roles for the dropdowns
const props = defineProps({
    form: { type: Object, required: true }, // The form object passed from parent
    departments: { type: Array, required: true },
    roles: { type: Array, required: true },
});

const emits = defineEmits(["submit"]);

// Handle profile image selection and preview
const handleFileChange = (e) => {
    const file = e.target.files[0];
    props.form.profile_img = file;
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            props.form.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        props.form.imagePreview = null;
    }
};
</script>

<template>
    <form 
        @submit.prevent="emits('submit')"
        enctype="multipart/form-data">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- User Details -->
            <div>
                <InputLabel for="name" value="Full Name" />
                <TextInput
                    id="name"
                    v-model="props.form.name"
                    required
                    class="w-full"
                />
                <InputError :message="props.form.errors.name" />
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

            <div>
                <InputLabel for="role" value="Select Role" />
                <select
                    id="role"
                    v-model="props.form.role_name"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                >
                    <option disabled value="">Select Role</option>
                    <option
                        v-for="role in props.roles"
                        :key="role.id"
                        :value="role.name"
                    >
                        {{ role.name }}
                    </option>
                </select>

                <InputError :message="props.form.errors.role_name" />
            </div>
            <div>
                <InputLabel for="job_position" value="Job Position" />
                <TextInput
                    id="job_position"
                    type="text"
                    v-model="props.form.job_position"
                    required
                    class="w-full"
                />
                <InputError :message="props.form.errors.job_position" />
            </div>

            <div>
                <InputLabel for="employment_type" value="Employment Type" />
                <select
                    id="employment_type"
                    v-model="props.form.employment_type"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                >
                    <option disabled value="">Select Type</option>
                    <option value="FULL-TIME">FULL-TIME</option>
                    <option value="PART-TIME">PART-TIME</option>
                    <option value="Contract">Contract</option>
                </select>

                <InputError :message="props.form.errors.employment_type" />
            </div>

            <div>
                <InputLabel for="office_hours" value="Office Hours" />
                <TextInput
                    id="office_hours"
                    type="text"
                    v-model="props.form.office_hours"
                    class="w-full"
                />
                <InputError :message="props.form.errors.office_hours" />
            </div>
            

            <!-- Profile Image Upload & Preview -->
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
                        v-if="props.form.imagePreview"
                        class="w-16 h-16 rounded-full border shadow overflow-hidden"
                    >
                        <img
                            :src="props.form.imagePreview"
                            alt="Profile Preview"
                            class="object-cover w-full h-full"
                        />
                    </div>
                </div>
                <InputError :message="props.form.errors.profile_img" />
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
