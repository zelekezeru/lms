<script setup>
import { defineProps } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";
import { MultiSelect, Select } from "primevue";

// Define the expected props: form data and roles for the dropdown
const props = defineProps({
    form: { type: Object, required: true }, // The form object passed from parent
    roles: { type: Array, required: true },
});

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
    <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- User Details -->
            <div>
                <InputLabel for="name" value="Full Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    required
                    class="w-full"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="phone" value="Phone Number" />
                <TextInput
                    id="phone"
                    v-model="form.phone"
                    required
                    class="w-full"
                />
                <InputError :message="form.errors.phone" />
            </div>

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

            <div>
                <InputLabel for="roles" value="Select Role" />
                <MultiSelect
                    id="roles"
                    class="w-full"
                    v-model="form.roles"
                    :options="roles"
                    option-label="name"
                    option-value="id"
                    required
                    filter
                    checkmark
                />
                <InputError :message="form.errors.roles" />
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
                class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <span v-if="!form.processing">Submit</span>
                <span v-else>Submitting...</span>
            </button>
        </div>
    </form>
</template>
