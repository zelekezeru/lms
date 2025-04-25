<script setup>
import { defineProps, defineEmits, ref } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Listbox, Select } from "primevue";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    form: Object,
    users: Object,
    courses: Object,
});

const assignCourses = ref(false);

const closeCourseAssignment = () => {
    assignCourses.value = false;
    form.courses = [];
};

const updateCoursesList = () => {
    assignCourses.value = false;
};

const emits = defineEmits(['submit'])
</script>

<template>
    <form @submit.prevent="emits('submit')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <InputLabel
                    for="name"
                    value="Program Name"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.name"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <div>
                <InputLabel
                    for="language"
                    value="Program Language"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <TextInput
                    id="language"
                    type="text"
                    v-model="form.language"
                    required
                    autocomplete="language"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.language"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <div>
                <InputLabel
                    for="description"
                    value="Description"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <TextInput
                    id="description"
                    type="text"
                    v-model="form.description"
                    autocomplete="description"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.description"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Duration -->
            <div>
                <InputLabel
                    for="duration"
                    value="Duration(In Years)"
                    class="block mb-1 text-gray-200"
                />
                <TextInput
                    id="duration"
                    type="number"
                    v-model="form.duration"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:ring focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-100 transition"
                />
                <InputError
                    :message="form.errors.duration"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <!-- Program Director Dropdown -->
            <div>
                <InputLabel
                    for="user_id"
                    value="Select Program Director"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />

                <Select
                    id="cousesList"
                    v-model="form.user_id"
                    :options="users"
                    option-value="id"
                    option-label="name"
                    appendTo="self"
                    checkmark
                    filter
                    placeholder="Select Program Director"
                    :maxSelectevdLabels="3"
                    class="w-full"
                />
                <InputError
                    :message="form.errors.user_id"
                    class="mt-2 text-sm text-red-500"
                />
            </div>

            <div>
                <InputLabel
                    for="user_id"
                    value="Assign Common Courses to All Tracks"
                    class="block mb-1 text-gray-800 dark:text-gray-200"
                />
                <span
                    type="button"
                    @click="assignCourses = true"
                    class="inline-flex cursor-pointer justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Assign Common Courses (Optional)
                </span>
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

    <Modal
        :show="assignCourses"
        @close="assignCourses = !assignCourses"
        :maxWidth="'6xl'"
        class="p-24 h-full"
    >
        <div class="w-full px-16 py-8">
            <h1 class="text-lg mb-5">
                Pick Common Courses You Would like To Assign To This Program
            </h1>

            <Listbox
                id="cousesList"
                v-model="form.courses"
                :options="courses"
                optionLabel="name"
                option-value="id"
                appendTo="self"
                filter
                checkmark
                multiple
                list-style="max-height: 500px"
                placeholder="Select Courses"
                :maxSelectedLabels="3"
                class="w-full"
            />

            <InputError
                :message="form.errors.programs"
                class="mt-2 text-sm text-red-500"
            />
            <div class="flex justify-end mt-4">
                <button
                    @click="updateCoursesList"
                    :disabled="form.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition mr-5"
                >
                    {{ form.processing ? "Assigning..." : "Assign" }}
                </button>

                <button
                    @click="closeCourseAssignment"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition"
                >
                    Close
                </button>
            </div>
        </div>
    </Modal>
</template>