<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { watch } from "vue";

const props = defineProps({
    semester: Object,
    semesters: Object,
});

// Initialize form with inertia useForm for posting
const form = useForm({
    approval: false,
    new_semester_id: null,
    new_semester_start_date: "",
    new_semester_end_date: "",
    errors: {},
});

// When component loads, set the form to current active semester info by default
if (props.semester) {
    form.new_semester_id = props.semester.id;
    form.new_semester_start_date = props.semester.start_date || "";
    form.new_semester_end_date = props.semester.end_date || "";
}

// Watch selected semester id changes and update start/end dates accordingly
watch(
    () => form.new_semester_id,
    (newId) => {
        if (newId === props.semester.id) {
            // Selected is the current active semester
            form.new_semester_start_date = props.semester.start_date || "";
            form.new_semester_end_date = props.semester.end_date || "";
        } else {
            // Find selected semester from semesters array
            const selectedSemester = props.semesters.find(
                (s) => s.id === newId
            );
            if (selectedSemester) {
                form.new_semester_start_date =
                    selectedSemester.start_date || "";
                form.new_semester_end_date = selectedSemester.end_date || "";
            } else {
                form.new_semester_start_date = "";
                form.new_semester_end_date = "";
            }
        }
    }
);

const submit = () => {
    form.post(route("semesters.close"), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout>
        <h2 class="text-2xl font-semibold mb-1 text-gray-900 dark:text-white">
            {{ $t("semester.close_active_title") }}
        </h2>

        <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">
            {{ $t("semester.confirm_close_and_activate") }}
        </p>

        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow space-y-6">
            <!-- Current Semester Info -->
            <div>
                <h3
                    class="text-lg font-semibold text-gray-800 dark:text-white mb-1"
                >
                    {{ $t("semester.current_active") }}
                </h3>
                <p class="text-green-600 dark:text-green-300 text-base">
                    🟢 {{ semester.name }}
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Select New Semester -->
                <div>
                    <label
                        class="block mb-2 font-medium text-gray-900 dark:text-gray-200"
                    >
                        {{ $t("semester.select_next_opening") }}
                    </label>
                    <select
                        v-model="form.new_semester_id"
                        class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                        <!-- Disabled Current Active Semester -->
                        <option
                            :value="semester.id"
                            disabled
                            class="bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                            🟢 {{ $t("semester.current_active_option") }} :
                            {{ semester.name }}
                        </option>

                        <!-- Loop Through Inactive Semesters -->
                        <option
                            v-for="sem in semesters"
                            :key="sem.id"
                            :value="sem.id"
                            class="bg-white dark:bg-gray-700"
                        >
                            🔴 {{ sem.name }} - {{ $t("semester.semester") }}
                        </option>
                    </select>
                </div>

                <!-- Set New Semester Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-900 dark:text-gray-200"
                        >
                            {{ $t("semester.start_date") }}
                        </label>
                        <input
                            v-model="form.new_semester_start_date"
                            type="date"
                            class="w-full p-2 border rounded dark:bg-gray-700"
                        />
                        <p
                            class="text-sm text-red-500"
                            v-if="form.errors.new_semester_start_date"
                        >
                            {{ form.errors.new_semester_start_date }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-900 dark:text-gray-200"
                        >
                            {{ $t("semester.end_date") }}
                        </label>
                        <input
                            v-model="form.new_semester_end_date"
                            type="date"
                            class="w-full p-2 border rounded dark:bg-gray-700"
                        />
                        <p
                            class="text-sm text-red-500"
                            v-if="form.errors.new_semester_end_date"
                        >
                            {{ form.errors.new_semester_end_date }}
                        </p>
                    </div>
                </div>

                <!-- Confirmation Checkbox -->
                <div>
                    <label
                        class="inline-flex items-center text-gray-900 dark:text-gray-200"
                    >
                        <input
                            type="checkbox"
                            v-model="form.approval"
                            class="mr-2"
                        />
                        {{ $t("semester.confirm_close_and_activate") }}
                    </label>
                    <p class="text-sm text-red-500" v-if="form.errors.approval">
                        {{ form.errors.approval }}
                    </p>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded"
                    :disabled="form.processing"
                >
                    {{ $t("semester.initialize_close_semester") }}
                </button>
            </form>
        </div>
    </AppLayout>
</template>
