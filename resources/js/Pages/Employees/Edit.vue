<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    departments: { type: Object, required: true },
    roles: { type: Object, required: true },
    employee: { type: Object, required: true },
});

// Initialize form data
const form = useForm({
  name: props.employee.name || "",
  email: props.employee.email || "",
  department_id: props.employee.department.id || "",
  role_name: props.employee.userRole || "",
  job_position: props.employee.jobPosition || "",
  employment_type: props.employee.employmentType || "",
  office_hours: props.employee.officeHours || "",
  profile_img: null,
});

// Ref to hold the image preview
const imagePreview = ref(props.employee.profileImg);

// Handle profile image selection and preview
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
        imagePreview.value = null;
    }
};

// Submit the form
const submit = (id) => {
    form.put(route("employees.update", {'employee': id}));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Create Employee
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Fill in the details to add a new employee.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <form @submit.prevent="submit(employee.id)" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User Details -->
                        <section class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Full Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    required
                                    class="w-full"
                                />
                                <InputError :message="form.errors.name" />
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
                                <InputLabel for="role" value="Select Role" />
                                <select
                                    id="role"
                                    v-model="form.role_name"
                                    required
                                    class="w-full px-3 py-2 border rounded-md"
                                >
                                    <option disabled value="">
                                        Select Role
                                    </option>
                                    <option
                                        v-for="role in roles"
                                        :key="role.id"
                                        :value="role.name"
                                    >
                                        {{ role.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.role" />
                            </div>

                            <!-- Profile Image Upload & Preview -->
                            <div>
                                <InputLabel
                                    for="profile_img"
                                    value="Profile Image"
                                />
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
                                <InputError
                                    :message="form.errors.profile_img"
                                />
                            </div>
                        </section>

                        <!-- Employee Details -->
                        <section class="space-y-6">
                            <div>
                                <InputLabel
                                    for="department_id"
                                    value="Department"
                                />
                                <select
                                    id="department_id"
                                    v-model="form.department_id"
                                    required
                                    class="w-full px-3 py-2 border rounded-md"
                                >
                                    <option disabled value="">
                                        Select Department
                                    </option>
                                    <option
                                        v-for="department in departments"
                                        :key="department.id"
                                        :value="department.id"
                                    >
                                        {{ department.name }}
                                    </option>
                                </select>
                                <InputError
                                    :message="form.errors.department_id"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="job_position"
                                    value="Job Position"
                                />
                                <TextInput
                                    id="job_position"
                                    type="text"
                                    v-model="form.job_position"
                                    required
                                    class="w-full"
                                />
                                <InputError
                                    :message="form.errors.job_position"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="employment_type"
                                    value="Employment Type"
                                />
                                <select
                                    id="employment_type"
                                    v-model="form.employment_type"
                                    required
                                    class="w-full px-3 py-2 border rounded-md"
                                >
                                    <option disabled value="">
                                        Select Type
                                    </option>
                                    <option value="FULL_TIME">Full-time</option>
                                    <option value="PART_TIME">Part-time</option>
                                    <option value="CONTRACT">Contract</option>
                                </select>
                                <InputError
                                    :message="form.errors.employment_type"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="office_hours"
                                    value="Office Hours"
                                />
                                <TextInput
                                    id="office_hours"
                                    type="text"
                                    v-model="form.office_hours"
                                    class="w-full"
                                />
                                <InputError
                                    :message="form.errors.office_hours"
                                />
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
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Smooth fade-in effect for image preview */
img {
    transition: opacity 0.3s ease-in-out;
}
</style>
