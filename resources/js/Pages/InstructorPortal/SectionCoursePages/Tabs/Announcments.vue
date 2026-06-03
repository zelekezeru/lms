<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { 
  MegaphoneIcon, 
  TrashIcon, 
  PlusIcon,
  XMarkIcon,
  CalendarDaysIcon,
  UserIcon
} from "@heroicons/vue/24/solid";

const props = defineProps({
  courseOffering: {
    type: Object,
    required: true,
  },
  course: {
    type: Object,
    required: true,
  },
  instructor: {
    type: Object,
    required: true,
  },
});

const announcements = ref([]);
const showForm = ref(false);
const title = ref("");
const content = ref("");

// Fetch announcements from localStorage
const loadAnnouncements = () => {
  const key = `announcements_${props.courseOffering.id}`;
  const stored = localStorage.getItem(key);
  if (stored) {
    try {
      announcements.value = JSON.parse(stored);
    } catch (e) {
      announcements.value = [];
    }
  } else {
    announcements.value = [];
  }
};

// Create a new announcement
const submitAnnouncement = () => {
  if (!title.value.trim() || !content.value.trim()) {
    Swal.fire("Validation Error", "Please fill in both the title and the content.", "warning");
    return;
  }

  const newAnn = {
    id: Date.now().toString(),
    title: title.value.trim(),
    content: content.value.trim(),
    createdAt: new Date().toISOString(),
    authorName: props.instructor?.user?.name || "Instructor",
  };

  announcements.value.unshift(newAnn);
  
  const key = `announcements_${props.courseOffering.id}`;
  localStorage.setItem(key, JSON.stringify(announcements.value));

  // Reset form
  title.value = "";
  content.value = "";
  showForm.value = false;

  Swal.fire({
    title: "Success!",
    text: "Announcement posted successfully.",
    icon: "success",
    timer: 1500,
    showConfirmButton: false,
  });
};

// Delete an announcement
const deleteAnnouncement = (id) => {
  Swal.fire({
    title: "Are you sure?",
    text: "You will not be able to recover this announcement!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      announcements.value = announcements.value.filter((ann) => ann.id !== id);
      const key = `announcements_${props.courseOffering.id}`;
      localStorage.setItem(key, JSON.stringify(announcements.value));

      Swal.fire({
        title: "Deleted!",
        text: "Your announcement has been deleted.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false,
      });
    }
  });
};

// Format date helper
const formatDate = (isoString) => {
  const date = new Date(isoString);
  return date.toLocaleString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  });
};

onMounted(() => {
  loadAnnouncements();
});
</script>

<template>
  <div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border dark:border-gray-700">
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
          <MegaphoneIcon class="w-6 h-6" />
        </div>
        <div>
          <h2 class="text-lg font-bold text-gray-900 dark:text-white">Course Announcements</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400">Share notices, materials, and reminders with the class.</p>
        </div>
      </div>

      <button
        v-if="!showForm"
        @click="showForm = true"
        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition space-x-1"
      >
        <PlusIcon class="w-5 h-5" />
        <span>Post Notice</span>
      </button>
    </div>

    <!-- Announcement Form Panel -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="transform scale-95 opacity-0 -translate-y-4"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 -translate-y-4"
    >
      <div 
        v-if="showForm" 
        class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md border-2 border-blue-500 dark:border-blue-600 space-y-4"
      >
        <div class="flex justify-between items-center border-b dark:border-gray-700 pb-3">
          <h3 class="text-md font-bold text-gray-900 dark:text-white flex items-center">
            <MegaphoneIcon class="w-5 h-5 mr-2 text-blue-500" />
            New Announcement
          </h3>
          <button @click="showForm = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Title</label>
            <input
              v-model="title"
              type="text"
              placeholder="e.g. Midterm exam scheduling updates"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Content</label>
            <textarea
              v-model="content"
              rows="5"
              placeholder="Write your announcement details here..."
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
            ></textarea>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-2">
          <button
            @click="showForm = false"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition"
          >
            Cancel
          </button>
          <button
            @click="submitAnnouncement"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition"
          >
            Post Notice
          </button>
        </div>
      </div>
    </Transition>

    <!-- Announcements Timeline/List -->
    <div v-if="announcements.length > 0" class="space-y-4">
      <div 
        v-for="ann in announcements" 
        :key="ann.id"
        class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border dark:border-gray-700 hover:shadow-md transition duration-200 space-y-3 relative group"
      >
        <!-- Delete Button (Only visible on hover / active) -->
        <button
          @click="deleteAnnouncement(ann.id)"
          class="absolute top-4 right-4 p-1.5 rounded-lg text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 transition lg:opacity-0 lg:group-hover:opacity-100"
          title="Delete Announcement"
        >
          <TrashIcon class="w-5 h-5" />
        </button>

        <h3 class="text-lg font-bold text-gray-900 dark:text-white pr-8">{{ ann.title }}</h3>

        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ ann.content }}</p>

        <!-- Meta info bar -->
        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400 pt-2 border-t dark:border-gray-700">
          <div class="flex items-center space-x-1">
            <CalendarDaysIcon class="w-4 h-4 text-gray-400" />
            <span>Posted on {{ formatDate(ann.createdAt) }}</span>
          </div>
          <div class="flex items-center space-x-1">
            <UserIcon class="w-4 h-4 text-gray-400" />
            <span>Author: {{ ann.authorName }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 shadow-sm">
      <MegaphoneIcon class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" />
      <h3 class="text-md font-bold text-gray-900 dark:text-white">No Announcements Yet</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
        Share important updates, deadlines, or learning materials with your students.
      </p>
      <button
        @click="showForm = true"
        class="mt-4 inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition space-x-1"
      >
        <PlusIcon class="w-5 h-5" />
        <span>Create Announcement</span>
      </button>
    </div>
  </div>
</template>
