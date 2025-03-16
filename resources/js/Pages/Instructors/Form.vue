<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    instructor: { type: Object, default: null }, // For edit, instructor data is passed
    departments: { type: Array, required: true }, // Departments for dropdown
});

// Initialize form data (for both create & edit)
const form = useForm({
    name: props.instructor?.name || "",
    email: props.instructor?.email || "",
    department_id: props.instructor?.department_id || "",
    specialization: props.instructor?.specialization || "",
    employment_type: props.instructor?.employment_type || "",
    hire_date: props.instructor?.hire_date || "",
    status: props.instructor?.status || "active",
    bio: props.instructor?.bio || "",
    profile_img: null, // Image upload
    _method: props.instructor ? "PATCH" : "POST", // Determines request type
});

// Ref for image preview
const imagePreview = ref(props.instructor?.profile_img || null);

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
                            <InputLabel for="department_id" value="Department" />
                            <select v-model="form.department_id" class="w-full p-2 border rounded-md">
                                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                    {{ dept.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.department_id" />
                        </div>

                        <div>
                            <InputLabel for="specialization" value="Specialization" />
                            <TextInput id="specialization" type="text" v-model="form.specialization" class="w-full" />
                            <InputError :message="form.errors.specialization" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <InputLabel for="employment_type" value="Employment Type" />
                            <select v-model="form.employment_type" class="w-full p-2 border rounded-md">
                                <option value="full-time">Full-Time</option>
                                <option value="part-time">Part-Time</option>
                                <option value="contract">Contract</option>
                            </select>
                            <InputError :message="form.errors.employment_type" />
                        </div>

                        <div>
                            <InputLabel for="hire_date" value="Hire Date" />
                            <TextInput id="hire_date" type="date" v-model="form.hire_date" class="w-full" />
                            <InputError :message="form.errors.hire_date" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="bio" value="Bio" />
                        <textarea v-model="form.bio" class="w-full p-2 border rounded-md" rows="3"></textarea>
                        <InputError :message="form.errors.bio" />
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
