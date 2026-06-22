<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import {
    UserCircleIcon,
    PhoneIcon,
    EnvelopeIcon,
    MapPinIcon,
    AcademicCapIcon,
    IdentificationIcon,
    PencilSquareIcon,
    ChevronDownIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    student: { type: Object, required: true },
});

const openSection = ref("basic");

const toggle = (key) => {
    openSection.value = openSection.value === key ? null : key;
};

const sections = [
    {
        key: "basic",
        label: "Basic Information",
        icon: UserCircleIcon,
        color: "text-indigo-500",
        bg: "bg-indigo-50 dark:bg-indigo-900/20",
        fields: () => [
            { label: "Full Name", value: `${props.student.firstName} ${props.student.middleName} ${props.student.lastName}` },
            { label: "Gender", value: props.student.sex },
            { label: "Date of Birth", value: props.student.dateOfBirth },
            { label: "Nationality", value: props.student.nationality },
            { label: "Marital Status", value: props.student.maritalStatus },
        ],
    },
    {
        key: "contact",
        label: "Contact",
        icon: PhoneIcon,
        color: "text-sky-500",
        bg: "bg-sky-50 dark:bg-sky-900/20",
        fields: () => [
            { label: "Email", value: props.student.user?.email },
            { label: "Mobile", value: props.student.mobilePhone },
            { label: "Office Phone", value: props.student.officePhone },
            { label: "Address", value: props.student.address },
        ],
    },
    {
        key: "academic",
        label: "Academic",
        icon: AcademicCapIcon,
        color: "text-emerald-500",
        bg: "bg-emerald-50 dark:bg-emerald-900/20",
        fields: () => [
            { label: "Student ID", value: props.student.user?.user_uuid ?? props.student.idNo },
            { label: "Program", value: props.student.program?.name },
            { label: "Language", value: props.student.program?.language },
            { label: "Section", value: props.student.section?.name },
            { label: "Year", value: props.student.section?.year?.name },
            { label: "Study Mode", value: props.student.studyMode?.name },
        ],
    },
];

const initials = (student) => {
    const f = student.firstName?.[0] ?? "";
    const l = student.lastName?.[0] ?? "";
    return (f + l).toUpperCase() || "ST";
};
</script>

<template>
    <AppLayout>
        <div class="pb-24 md:pb-8 max-w-2xl mx-auto px-4 md:px-6 pt-4 space-y-5">

            <!-- Hero card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Cover gradient -->
                <div class="h-24 bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-500 relative">
                    <!-- Edit button -->
                    <Link
                        :href="route('profile.edit')"
                        class="absolute top-3 right-3 flex items-center gap-1.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl px-3 py-1.5 text-xs font-semibold transition"
                    >
                        <PencilSquareIcon class="w-3.5 h-3.5" />
                        Edit Profile
                    </Link>
                </div>

                <!-- Avatar + name -->
                <div class="px-5 pb-5">
                    <div class="-mt-10 mb-3">
                        <div
                            v-if="student.avatar"
                            class="w-20 h-20 rounded-2xl border-4 border-white dark:border-gray-800 overflow-hidden shadow-md"
                        >
                            <img :src="student.avatar" alt="Profile" class="w-full h-full object-cover" />
                        </div>
                        <div
                            v-else
                            class="w-20 h-20 rounded-2xl border-4 border-white dark:border-gray-800 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md"
                        >
                            <span class="text-white font-bold text-xl">{{ initials(student) }}</span>
                        </div>
                    </div>

                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ student.program?.name ?? '' }}</p>

                    <!-- Badges row -->
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span v-if="student.idNo" class="flex items-center gap-1.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full px-3 py-1">
                            <IdentificationIcon class="w-3.5 h-3.5" /> {{ student.idNo }}
                        </span>
                        <span v-if="student.section?.year?.name" class="flex items-center gap-1.5 text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-full px-3 py-1">
                            <AcademicCapIcon class="w-3.5 h-3.5" /> {{ student.section.year.name }}
                        </span>
                        <span v-if="student.user?.email" class="flex items-center gap-1.5 text-xs bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 rounded-full px-3 py-1">
                            <EnvelopeIcon class="w-3.5 h-3.5" /> {{ student.user.email }}
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
                    <!-- Section header -->
                    <button
                        @click="toggle(section.key)"
                        class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-750/30 transition"
                    >
                        <div class="flex items-center gap-3">
                            <div :class="['w-8 h-8 rounded-xl flex items-center justify-center shrink-0', section.bg]">
                                <component :is="section.icon" :class="['w-4 h-4', section.color]" />
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ section.label }}</span>
                        </div>
                        <ChevronDownIcon
                            :class="['w-4 h-4 text-gray-400 transition-transform duration-200', openSection === section.key ? 'rotate-180' : '']"
                        />
                    </button>

                    <!-- Section content -->
                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 -translate-y-2"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="openSection === section.key" class="border-t border-gray-100 dark:border-gray-700 px-5 py-4 bg-gray-50/30 dark:bg-gray-900/10">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div
                                    v-for="field in section.fields().filter(f => f.value)"
                                    :key="field.label"
                                    class="flex flex-col p-3 bg-white dark:bg-gray-850 rounded-xl border border-gray-100 dark:border-gray-750 shadow-sm hover:shadow-md transition duration-300"
                                >
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ field.label }}</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white mt-1 break-words">{{ field.value }}</span>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
