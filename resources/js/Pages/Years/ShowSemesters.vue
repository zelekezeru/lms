<script setup>
import { defineProps, ref } from "vue";
import Swal from "sweetalert2";
import { usePage, Link } from "@inertiajs/vue3";
import "sweetalert2/dist/sweetalert2.min.css";
import {
    ArrowPathIcon,
    TrashIcon,
    EyeIcon,
    PlusIcon,
    PencilSquareIcon,
    PencilIcon,
    CalendarDaysIcon,
    CheckBadgeIcon,
    ClipboardDocumentCheckIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/solid";

const year = usePage().props.year;

const showForm = ref(false);
const isEditing = ref(false);
const selectedSemester = ref(null);

// Form data
const semesterForm = ref({
    name: "",
    year_id: year.id,
    status: "inactive",
    is_approved: false,
    is_completed: false,
    start_date: "",
    end_date: "",
});

// Toggle create form
const toggleForm = () => {
    showForm.value = !showForm.value;
    isEditing.value = false;
    resetForm();
};

// Reset form
const resetForm = () => {
    semesterForm.value = {
        name: "",
        status: "inactive",
        year_id: year.id,
        is_approved: false,
        is_completed: false,
        start_date: "",
        end_date: "",
    };
    selectedSemester.value = null;
};

// Submit semester (create or update)
const submitSemester = () => {
    const action = isEditing.value
        ? router.put(
              route("semesters.update", selectedSemester.value.id),
              semesterForm.value,
              {
                  onSuccess: () => {
                      Swal.fire(
                          "Updated!",
                          "Semester has been updated.",
                          "success"
                      );
                      resetForm();
                      showForm.value = false;
                  },
              }
          )
        : router.post(route("semesters.store"), semesterForm.value, {
              onSuccess: () => {
                  Swal.fire("Added!", "Semester has been added.", "success");
                  resetForm();
                  showForm.value = false;
              },
          });
};

// Edit semester
const editSemester = (semester) => {
    semesterForm.value = { ...semester };
    selectedSemester.value = semester;
    isEditing.value = true;
    showForm.value = true;
};
</script>
<template>
    <!-- <div v-if="showForm" class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded shadow">
        <form @submit.prevent="submitSemester">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ $t('semester.name') }}</label>
                    <input
                        v-model="semesterForm.name"
                        type="text"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ $t('semester.start_date') }}</label>
                    <input
                        type="date"
                        v-model="semesterForm.start_date"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ $t('semester.end_date') }}</label>
                    <input
                        type="date"
                        v-model="semesterForm.end_date"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-center space-x-2">
                    <input
                        type="checkbox"
                        v-model="semesterForm.is_approved"
                        class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:bg-gray-800 dark:checked:bg-blue-600"
                    />
                    <label class="text-sm text-gray-700 dark:text-gray-200">{{ $t('semester.approved') }}</label>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button
                    type="submit"
                    class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800"
                >
                    {{ isEditing ? $t('semester.update', 'Update Semester') : $t('semester.create', 'Create Semester') }}
                </button>
            </div>
        </form>
    </div> -->

    <!-- Semester List -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
            v-for="semester in year.semesters"
            :key="semester.id"
            class="rounded-xl p-5 shadow-md border dark:border-gray-700 bg-white dark:bg-[#1E293B]"
        >
            <div
                class="flex items-center mb-3 text-indigo-600 dark:text-indigo-400"
            >
                <CalendarDaysIcon class="w-6 h-6 mr-2" />
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                    <Link
                        :href="
                            route('semesters.show', { semester: semester.id })
                        "
                        class="hover:underline"
                    >
                        {{ semester.name || $t("semester.no_name", "No Name") }}
                    </Link>
                </h2>
            </div>
            <div class="text-sm space-y-1 text-gray-600 dark:text-gray-300">
                <p>
                    <CheckBadgeIcon
                        class="w-4 h-4 inline-block mr-1 text-green-500"
                    />
                    <span>{{ $t("common.status") }}: </span>
                    <span
                        :class="
                            semester.status === 'Active'
                                ? 'bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900 px-2 py-0.5 rounded-full font-semibold'
                                : 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 px-2 py-0.5 rounded-full font-semibold'
                        "
                    >
                        {{
                            $t(
                                "common." + semester.status.toLowerCase(),
                                semester.status
                            )
                        }}
                    </span>
                </p>
                <p>
                    <ClipboardDocumentCheckIcon
                        class="w-4 h-4 inline-block mr-1 text-yellow-500"
                    />
                    <span>{{ $t("semester.approved") }}:</span>
                    <span
                        :class="
                            semester.is_approved
                                ? 'text-green-600 font-semibold'
                                : 'text-red-600 font-semibold'
                        "
                    >
                        {{
                            semester.is_approved
                                ? $t("common.yes")
                                : $t("common.no")
                        }}
                    </span>
                </p>
                <p>
                    <CheckCircleIcon
                        class="w-4 h-4 inline-block mr-1 text-purple-500"
                    />
                    <span>{{ $t("semester.completed", "Completed") }}:</span>
                    <span
                        :class="
                            semester.is_completed
                                ? 'text-green-600 font-semibold'
                                : 'text-red-600 font-semibold'
                        "
                    >
                        {{
                            semester.is_completed
                                ? $t("common.yes")
                                : $t("common.no")
                        }}
                    </span>
                </p>
                <p>
                    <CalendarDaysIcon
                        class="w-4 h-4 inline-block mr-1 text-blue-500"
                    />
                    <span>{{ $t("semester.start_date") }}:</span>
                    {{ semester.start_date }}
                </p>
                <p>
                    <CalendarDaysIcon
                        class="w-4 h-4 inline-block mr-1 text-blue-500"
                    />
                    <span>{{ $t("semester.end_date") }}:</span>
                    {{ semester.end_date }}
                </p>
            </div>
            <div class="mt-4 text-right">
                <button
                    @click="editSemester(semester)"
                    class="text-blue-600 hover:underline text-sm flex items-center space-x-1"
                >
                    <PencilSquareIcon class="w-4 h-4" />
                    <span>{{ $t("common.edit") }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
