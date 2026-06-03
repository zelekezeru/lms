<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import {
    UserCircleIcon,
    PhoneIcon,
    EnvelopeIcon,
    AcademicCapIcon,
    IdentificationIcon,
    PencilSquareIcon,
    ChevronDownIcon,
    BookOpenIcon,
    Square2StackIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    instructor: { type: Object, required: true },
    student: { type: Object, required: false },
});

// Resolve the actual subject — instructor profile shows instructor, but
// this component was also used as a student profile page.
const subject = props.instructor ?? props.student;
const isInstructor = !!props.instructor;

const openSection = ref("basic");
const toggle = (key) => { openSection.value = openSection.value === key ? null : key; };

const sections = [
    {
        key: "basic",
        label: "Basic Information",
        icon: UserCircleIcon,
        color: "text-violet-500",
        bg: "bg-violet-50 dark:bg-violet-900/20",
        fields: () => [
            { label: "Full Name", value: subject.user?.name ?? `${subject.firstName ?? ''} ${subject.middleName ?? ''} ${subject.lastName ?? ''}`.trim() },
            { label: "Gender", value: subject.sex },
            { label: "Date of Birth", value: subject.dateOfBirth },
            { label: "Nationality", value: subject.nationality },
            { label: "Marital Status", value: subject.maritalStatus },
        ],
    },
    {
        key: "contact",
        label: "Contact",
        icon: PhoneIcon,
        color: "text-sky-500",
        bg: "bg-sky-50 dark:bg-sky-900/20",
        fields: () => [
            { label: "Email", value: subject.user?.email },
            { label: "Mobile", value: subject.mobilePhone },
            { label: "Office Phone", value: subject.officePhone },
            { label: "Address", value: subject.address },
        ],
    },
    {
        key: "academic",
        label: isInstructor ? "Teaching Info" : "Academic Info",
        icon: AcademicCapIcon,
        color: "text-emerald-500",
        bg: "bg-emerald-50 dark:bg-emerald-900/20",
        fields: () => isInstructor
            ? [
                { label: "Instructor ID", value: subject.user?.user_uuid },
                { label: "Courses", value: (subject.courses ?? []).map(c => c.name).join(", ") || null },
                { label: "Sections", value: (subject.sections ?? []).map(s => s.name).join(", ") || null },
              ]
            : [
                { label: "Student ID", value: subject.user?.user_uuid ?? subject.idNo },
                { label: "Program", value: subject.program?.name },
                { label: "Section", value: subject.section?.name },
                { label: "Year", value: subject.section?.year?.name },
              ],
    },
];

const initials = () => {
    const name = subject.user?.name ?? `${subject.firstName ?? ''} ${subject.lastName ?? ''}`.trim();
    const parts = name.split(" ");
    return ((parts[0]?.[0] ?? "") + (parts[1]?.[0] ?? "")).toUpperCase() || "IN";
};
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-2xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Hero card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="h-24 bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-600 relative">
                    <Link
                        :href="route('profile.edit')"
                        class="absolute top-3 right-3 flex items-center gap-1.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl px-3 py-1.5 text-xs font-semibold transition"
                    >
                        <PencilSquareIcon class="w-3.5 h-3.5" /> Edit Profile
                    </Link>
                </div>

                <div class="px-5 pb-5">
                    <div class="-mt-10 mb-3">
                        <div
                            class="w-20 h-20 rounded-2xl border-4 border-white dark:border-gray-800 bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-md"
                        >
                            <span class="text-white font-bold text-xl">{{ initials() }}</span>
                        </div>
                    </div>

                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ subject.user?.name ?? `${subject.firstName} ${subject.middleName} ${subject.lastName}` }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ isInstructor ? 'Instructor' : subject.program?.name }}
                    </p>

                    <div class="flex flex-wrap gap-2 mt-3">
                        <span v-if="subject.user?.email" class="flex items-center gap-1.5 text-xs bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 rounded-full px-3 py-1">
                            <EnvelopeIcon class="w-3.5 h-3.5" /> {{ subject.user.email }}
                        </span>
                        <span v-if="isInstructor && (subject.courses ?? []).length" class="flex items-center gap-1.5 text-xs bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 rounded-full px-3 py-1">
                            <BookOpenIcon class="w-3.5 h-3.5" /> {{ (subject.courses ?? []).length }} courses
                        </span>
                        <span v-if="isInstructor && (subject.sections ?? []).length" class="flex items-center gap-1.5 text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-full px-3 py-1">
                            <Square2StackIcon class="w-3.5 h-3.5" /> {{ (subject.sections ?? []).length }} sections
                        </span>
                    </div>
                </div>
            </div>

            <!-- Accordion sections -->
            <div class="space-y-3">
                <div
                    v-for="section in sections"
                    :key="section.key"
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden"
                >
                    <button
                        @click="toggle(section.key)"
                        class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                    >
                        <div class="flex items-center gap-3">
                            <div :class="['w-8 h-8 rounded-xl flex items-center justify-center shrink-0', section.bg]">
                                <component :is="section.icon" :class="['w-4 h-4', section.color]" />
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ section.label }}</span>
                        </div>
                        <ChevronDownIcon :class="['w-4 h-4 text-gray-400 transition-transform duration-200', openSection === section.key ? 'rotate-180' : '']" />
                    </button>

                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 -translate-y-2"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="openSection === section.key" class="border-t border-gray-100 dark:border-gray-700 px-5 py-4">
                            <dl class="space-y-3">
                                <div
                                    v-for="field in section.fields().filter(f => f.value)"
                                    :key="field.label"
                                    class="flex items-start justify-between gap-4"
                                >
                                    <dt class="text-xs text-gray-500 dark:text-gray-400 shrink-0 w-28">{{ field.label }}</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-white text-right">{{ field.value }}</dd>
                                </div>
                            </dl>
                        </div>
                    </transition>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
