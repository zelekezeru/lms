<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { PhotoIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    course: { type: Object, default: null }, // For edit, course data is passed
    departments: { type: Array, required: true }, // Departments for dropdown
});

// Initialize form data (for both create & edit)
const form = useForm({
    name: props.course?.name || "",
    credit_hours: props.course?.credit_hours || "",
    duration: props.course?.duration || "",
    description: props.course?.description || "",
    is_training: props.course?.is_training || 0,
    status: props.course?.status || 1,
    department_id: props.course?.department_id || "",
    instructor_id: props.course?.instructor_id || "",
    _method: props.course ? "PATCH" : "POST", // Determines request type
});

// Ref for image preview
const imagePreview = ref(props.course?.profile_img || null);

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
        imagePreview.value = props.course?.profile_img || null;
    }
};

// Computed property to determine form action
const formAction = computed(() => {
    return props.course
        ? route("courses.update", { course: props.course.id })
        : route("courses.store");
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
                    {{ course ? "Edit course" : "Add course" }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ course ? "Update the course's details." : "Fill in the details to register a new course." }}
                </p>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="name" value="Course Name" />
                            <TextInput id="name" type="text" v-model="form.name" required class="w-full" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div>
                            <InputLabel for="credit_hours" value="Credit Hours" />
                            <TextInput id="credit_hours" type="number" v-model="form.credit_hours" required class="w-full" />
                            <InputError :message="form.errors.credit_hours" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

                        <div>
                            <InputLabel for="description" value="Description"
                                class="block mb-1 text-gray-800 dark:text-gray-200" />
                            <textarea
                                v-model="form.description"
                                id="description" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition" rows="3"
                            ></textarea>
                            <InputError :message="form.errors.description" class="mt-2 text-sm text-red-500"/>
                        </div>

                        <div>
                            <InputLabel for="duration" value="Duration (weeks)" />
                            <TextInput id="duration" type="number" v-model="form.duration" required class="w-full" />
                            <InputError :message="form.errors.duration" />
                        </div>
                    </div>

                    <!-- Department Dropdown -->
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
                        <InputLabel
                            for="instructor_id"
                            value="Select Course Instructor"
                            class="block mb-1 text-gray-800 dark:text-gray-200"
                        />
                        <select id="instructor_id" v-model="form.instructor_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition">
                            <option value="">Select Instructor</option>
                            <option v-for="instructor in instructors" :key="instructor.id" :value="instructor.id">
                            {{ instructor.name }} </option>
                        </select>
                        <InputError :message="form.errors.instructor_id" class="mt-2 text-sm text-red-500"/>
                    
                    </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <InputLabel for="start_date" value="Start Date" />
                            <TextInput id="start_date" type="date" v-model="form.start_date" class="w-full" />
                            <InputError :message="form.errors.start_date" />
                        </div>

                        <div>
                            <InputLabel for="end_date" value="End Date" />
                            <TextInput id="end_date" type="date" v-model="form.end_date" class="w-full" />
                            <InputError :message="form.errors.end_date" />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="mt-4">
                            <label for="is_training" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Training Course</label>
                            <div class="flex items
                            -center gap-4">
                                <label for="is_training" class="flex items-center gap-2 cursor-pointer">
                                    <input
                                        id="is_training"
                                        type="checkbox"
                                        v-model="form.is_training"
                                        class="rounded text-indigo-600 border-gray-300 focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100"
                                    />
                                    <span>Is Training Course?</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="syllabus" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Syllabus</label>
                            <div class="flex items-center gap-4">
                            <label
                                for="syllabus"
                                class="cursor-pointer px-4 py-2 text-white flex items-center gap-2 rounded-md shadow transition bg-black hover:bg-blue-700"
                            >
                                <PhotoIcon class="w-5 h-5" />
                                Upload Course Syllabus
                            </label>
                            <input
                                id="syllabus"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleFileChange"
                            />
                            <div v-if="imagePreview" class="w-16 h-16 rounded-full border shadow overflow-hidden">
                                <img :src="imagePreview" alt="syllabus Preview" class="object-cover w-full h-full" />
                            </div>
                            </div>
                            <div v-if="form.errors.syllabus" class="text-red-500 text-sm">{{ form.errors.syllabus }}</div>
                        </div>
                        </div>
 

                    <div class="mt-6 flex justify-center">
                        <PrimaryButton :disabled="form.processing">
                            <span v-if="!form.processing">{{ course ? "Update Course" : "Submit Course" }}</span>
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
