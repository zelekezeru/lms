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
    instructor: { type: Object, required: false },
    departments: { type: Array, required: true },
    roles: { type: Array, required: true },
});

const form = useForm({
    id: null,
    name: "",
    email: "",
    password: "instructors@default",
    password_confirmation: "instructors@default",
    role_name: "",
    department_id: "",
    job_position: "",
    employment_type: "",
    office_hours: "",
    profile_img: null,
});

const imagePreview = ref(props.instructor?.profile_img || null);

// Watch effect to populate form if an instructor object is passed
watchEffect(() => {
    if (props.instructor) {
        form.id = props.instructor.id;
        form.name = props.instructor.name;
        form.email = props.instructor.email;
        form.role_name = props.instructor.role_name;
        form.department_id = props.instructor.department_id;
        form.job_position = props.instructor.job_position;
        form.employment_type = props.instructor.employment_type;
        form.office_hours = props.instructor.office_hours;
        imagePreview.value = props.instructor.profile_img;
    }
});

// Handle profile image selection
const handleFileChange = (e) => {
    const file = e.target.files[0];
    form.profile_img = file; // Assign file to form
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

// Computed property to determine form action
const formAction = computed(() => {
    return props.instructor
        ? route("instructors.update", { instructor: props.instructor.id })
        : route("instructors.store");
});

// Submit the form
const submit = () => {
    form.post(formAction.value);
};
</script>

<template>
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ instructor ? "Edit Instructor" : "Add Instructor" }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                {{ instructor ? "Update the instructor's details." : "Fill in the details to register a new instructor." }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
            <form @submit.prevent="submit" enctype="multipart/form-data"> 
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" type="text" v-model="form.name" required class="w-full" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" type="email" v-model="form.email" required class="w-full" />
                        <InputError :message="form.errors.email" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <InputLabel
                            for="department_id"
                            value="Select Department"
                            class="block mb-1 text-gray-800 dark:text-gray-200"
                        />
                        <select id="department_id" v-model="form.department_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition">
                            <option value="">Select Department</option>
                            <option v-for="department in departments" :key="department.id" :value="department.id">
                            {{ department.name }} </option>
                        </select>
                        <InputError :message="form.errors.department_id" class="mt-2 text-sm text-red-500"/>
                    
                    </div>

                    <div>
                        <InputLabel for="job_position" value="Job Position" />
                        <TextInput id="job_position" type="text" v-model="form.job_position" class="w-full" />
                        <InputError :message="form.errors.job_position" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <InputLabel for="employment_type" value="Employment Type" />
                        <select v-model="form.employment_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition">
                            <option disabled value="">Select Employment Type</option>
                            <option value="full-time">Full-Time</option>
                            <option value="part-time">Part-Time</option>
                            <option value="contract">Contract</option>
                        </select>
                        <InputError :message="form.errors.employment_type" />
                    </div>

                    <div>
                        <InputLabel for="office_hours" value="Office Hours" />
                        <TextInput id="office_hours" type="text" v-model="form.office_hours" class="w-full" />
                        <InputError :message="form.errors.office_hours" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput id="password" type="text" v-model="form.password" value="pwd@default (Default Password)" readonly class="w-full bg-gray-200" />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="role" value="Select Role" />
                        <select id="role" v-model="form.role_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition">
                            <option disabled value="">Select Role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                        </select>
                        <InputError :message="form.errors.role_name" />
                    </div>
                </div>

                <div>
                    <InputLabel for="bio" value="Bio"
                        class="block mb-1 text-gray-800 dark:text-gray-200" />
                    <textarea
                        v-model="form.bio"
                        id="bio" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition" rows="3"
                    ></textarea>
                    <InputError :message="form.errors.bio" class="mt-2 text-sm text-red-500"/>
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
                        <div v-if="imagePreview" class="w-16 h-16 rounded-full border shadow overflow-hidden">
                            <img :src="imagePreview" alt="Profile Preview" class="object-cover w-full h-full" />
                        </div>
                    </div>
                    <InputError :message="form.errors.profile_img" />
                </div>

                <div class="mt-6 flex justify-center">
                    <PrimaryButton :disabled="form.processing">
                        <span v-if="!form.processing">{{ instructor ? "Update" : "Submit" }}</span>
                        <span v-else>Processing...</span>
                    </PrimaryButton>
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
