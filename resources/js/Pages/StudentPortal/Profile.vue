<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {
    UserIcon,
    PhoneIcon,
    AcademicCapIcon,
    IdentificationIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Panel: Avatar Showcase -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm flex flex-col items-center text-center space-y-5">
                        <div class="relative group">
                            <!-- Glow Ring backdrop -->
                            <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-purple-600 rounded-full blur opacity-40 group-hover:opacity-75 transition duration-500"></div>
                            <img
                                :src="
                                    student.avatar ||
                                    'https://ui-avatars.com/api/?background=4f46e5&color=ffffff&size=128&name=' + encodeURIComponent(student.firstName + ' ' + student.lastName)
                                "
                                alt="Profile Avatar"
                                class="relative w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow-xl object-cover"
                            />
                        </div>
                        
                        <div>
                            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                                {{ student.firstName }} {{ student.middleName }}
                            </h1>
                            <p class="text-xs font-mono text-gray-400 mt-1 uppercase tracking-wider">
                                Student ID: {{ student.idNo || 'N/A' }}
                            </p>
                        </div>

                        <div class="w-full pt-4 border-t border-gray-100 dark:border-gray-700/60 space-y-2">
                            <div class="bg-indigo-50/40 dark:bg-indigo-950/20 px-4 py-3 rounded-2xl border border-indigo-100/50 dark:border-indigo-900/30">
                                <span class="text-[10px] font-semibold text-indigo-500 uppercase tracking-wider block">Active Program</span>
                                <span class="text-sm font-black text-gray-850 dark:text-gray-200 mt-0.5 block">{{ student.program?.name || 'N/A' }}</span>
                            </div>
                            <div class="bg-purple-50/40 dark:bg-purple-950/20 px-4 py-3 rounded-2xl border border-purple-100/50 dark:border-purple-900/30">
                                <span class="text-[10px] font-semibold text-purple-500 uppercase tracking-wider block">Major / Track</span>
                                <span class="text-sm font-black text-gray-850 dark:text-gray-250 mt-0.5 block">{{ student.track?.name || 'General' }}</span>
                            </div>
                        </div>

                        <a
                            :href="route('profile.edit')"
                            class="w-full bg-gradient-to-r from-indigo-650 to-purple-650 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 rounded-2xl shadow-md hover:shadow-lg transition duration-200 text-center text-sm block"
                        >
                            Edit Profile Settings
                        </a>
                    </div>
                </div>

                <!-- Right Panel: Detailed Info Split -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information Card -->
                    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm space-y-5">
                        <h2 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                            <UserIcon class="w-5 h-5 text-indigo-500" />
                            Personal Details
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Full Name</span>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1">
                                    {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Gender</span>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1 capitalize">
                                    {{ student.sex || 'Not Specified' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Date of Birth</span>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1">
                                    {{ student.dateOfBirth || 'Not Provided' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Marital Status</span>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1 capitalize">
                                    {{ student.maritalStatus || 'Not Disclosed' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info Card -->
                    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm space-y-5">
                        <h2 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                            <PhoneIcon class="w-5 h-5 text-purple-500" />
                            Contact Information
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Email Address</span>
                                <p class="text-sm font-bold text-gray-850 dark:text-gray-200 mt-1 break-all">
                                    {{ student.user?.email || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Mobile Phone</span>
                                <p class="text-sm font-bold text-gray-850 dark:text-gray-200 mt-1">
                                    {{ student.mobilePhone || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Office Phone</span>
                                <p class="text-sm font-bold text-gray-850 dark:text-gray-200 mt-1">
                                    {{ student.officePhone || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Physical Address</span>
                                <p class="text-sm font-bold text-gray-850 dark:text-gray-200 mt-1">
                                    {{ student.address || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Registration Info Card -->
                    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md p-6 rounded-3xl border border-white/20 dark:border-gray-700/50 shadow-sm space-y-5">
                        <h2 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                            <AcademicCapIcon class="w-5 h-5 text-indigo-500" />
                            Academic Placement Details
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">UUID System Key</span>
                                <p class="text-sm font-mono text-gray-850 dark:text-gray-200 mt-1">
                                    {{ student.user?.user_uuid || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Active Section</span>
                                <p class="text-sm font-bold text-gray-850 dark:text-gray-200 mt-1">
                                    {{ student.section?.name || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Program Language</span>
                                <p class="text-sm font-bold text-gray-850 dark:text-gray-200 mt-1">
                                    {{ student.program?.language || 'English' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Registration Year</span>
                                <p class="text-sm font-bold text-gray-850 dark:text-gray-200 mt-1">
                                    {{ student.section?.year?.name || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
