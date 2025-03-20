<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref, watchEffect, defineProps } from "vue";
import { useForm } from "@inertiajs/vue3";
import { PhotoIcon } from "@heroicons/vue/24/outline";

// Define the expected props
const props = defineProps({
    employee: { type: Object, required: false },
    departments: { type: Array, required: true },
    roles: { type: Array, required: true },
});

const form = useForm({
    id: null,
    name: "",
    email: "",
    password: "employee@default",
    password_confirmation: "employee@default",
    role_name: "",
    department_id: "",
    job_position: "",
    employment_type: "",
    office_hours: "",
    profile_img: null,
});

const imagePreview = ref(props.instructor?.profile_img || null);

// Watch effect to populate form if an employee object is passed
watchEffect(() => {
    if (props.employee) {
        form.id = props.employee.id;
        form.name = props.employee.name;
        form.email = props.employee.email;
        form.role_name = props.employee.role_name;
        form.department_id = props.employee.department_id;
        form.job_position = props.employee.job_position;
        form.employment_type = props.employee.employment_type;
        form.office_hours = props.employee.office_hours;
        imagePreview.value = props.employee.profile_img;
    }
});

// Handle profile image selection and preview
const handleFileChange = (e) => {
    const file = e.target.files[0];
    form.profile_img = file;
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        imagePreview.value = null;
    }
};

// Submit the form
const submit = () => {
    if (form.id) {
        form.put(route("employees.update", form.id));
    } else {
        form.post(route("employees.store"));
    }
};
</script>

<template>
    <form @submit.prevent="submit" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- User Details -->
            <section class="space-y-6">
                <div>
                    <InputLabel for="name" value="Full Name" />
                    <TextInput id="name" v-model="form.name" required class="w-full" />
                    <InputError :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" type="email" v-model="form.email" required class="w-full" />
                    <InputError :message="form.errors.email" />
                </div>

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

                <!-- Profile Image Upload & Preview -->
                <div>
                    <InputLabel for="profile_img" value="Profile Image" />
                    <div class="flex items-center gap-4">
                        <label for="profile_img" class="cursor-pointer px-4 py-2 text-white flex items-center gap-2 rounded-md shadow transition bg-black hover:bg-blue-700">
                            <PhotoIcon class="w-5 h-5" /> Upload Image
                        </label>
                        <input id="profile_img" type="file" accept="image/*" class="hidden" @change="handleFileChange" />
                        <div v-if="imagePreview" class="w-16 h-16 rounded-full border shadow overflow-hidden">
                            <img :src="imagePreview" alt="Profile Preview" class="object-cover w-full h-full" />
                        </div>
                    </div>
                    <InputError :message="form.errors.profile_img" />
                </div>
            </section>

            <!-- Employee Details -->
            <section class="space-y-6">
                <div>
                    <InputLabel for="department_id" value="Department" />
                    <select id="department_id" v-model="form.department_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition">
                        <option disabled value="">Select Department</option>
                        <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                    </select>
                    <InputError :message="form.errors.department_id" />
                </div>

                <div>
                    <InputLabel for="job_position" value="Job Position" />
                    <TextInput id="job_position" type="text" v-model="form.job_position" required class="w-full" />
                    <InputError :message="form.errors.job_position" />
                </div>

                <div>
                    <InputLabel for="employment_type" value="Employment Type" />
                    <select id="employment_type" v-model="form.employment_type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition">
                        <option disabled value="">Select Type</option>
                        <option value="FULL_TIME">Full-time</option>
                        <option value="PART_TIME">Part-time</option>
                        <option value="CONTRACT">Contract</option>
                    </select>
                    <InputError :message="form.errors.employment_type" />
                </div>

                <div>
                    <InputLabel for="office_hours" value="Office Hours" />
                    <TextInput id="office_hours" type="text" v-model="form.office_hours" class="w-full" />
                    <InputError :message="form.errors.office_hours" />
                </div>
            </section>
        </div>

        <div class="mt-6 flex justify-center">
            <PrimaryButton :disabled="form.processing">
                <span v-if="!form.processing">Submit</span>
                <span v-else>Submitting...</span>
            </PrimaryButton>
        </div>
    </form>
</template>

