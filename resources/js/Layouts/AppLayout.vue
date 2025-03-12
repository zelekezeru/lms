<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Link } from "@inertiajs/vue3";
import { useDark } from "@vueuse/core";
import {
  HomeIcon,
  TableCellsIcon,
  ChartBarIcon,
  DocumentTextIcon,
  DocumentIcon,
  FolderIcon,
  Cog6ToothIcon,
  Bars3Icon,
  BuildingStorefrontIcon,
  XMarkIcon,
  MoonIcon,
  SunIcon,
  ArrowLeftIcon,
  ArrowRightIcon,
  ChevronUpIcon,
  ChevronDownIcon,
  PlusIcon,
  ClipboardDocumentListIcon,
  UsersIcon,
} from "@heroicons/vue/24/outline";

// Sidebar state
const isMobile = ref(window.innerWidth < 768);
const sidebarVisible = ref(!isMobile.value);
const sidebarHovered = ref(false);
const openMenus = ref({
  userPages: false,
  departmentsMenu: false,
  programsMenu: false,
  employeesMenu: false,
  studentsMenu: false,
  coursesMenu: false,
  teachersMenu: false,
  attendanceMenu: false,
  examsMenu: false,
  permissionsMenu: false,
});

// Dark mode handling
const isDarkMode = useDark();

const toggleTheme = () => {
  isDarkMode.value = !isDarkMode.value;
  if (isDarkMode.value) {
    document.documentElement.classList.add("dark");
  } else {
    document.documentElement.classList.remove("dark");
  }
};

// Navigation functions
const goBack = () => {
  window.history.length > 1
    ? window.history.back()
    : (window.location.href = "/dashboard");
};

const goForward = () => {
  window.history.forward();
};

// Detect screen size changes
const updateScreenSize = () => {
  isMobile.value = window.innerWidth < 768;
  sidebarVisible.value = !isMobile.value;
};

// Add event listeners
onMounted(() => {
  updateScreenSize();
  window.addEventListener("resize", updateScreenSize);
});

const handleAsideHover = (e) => {
  if (sidebarVisible.value) return;
  sidebarHovered.value = e.type === "mouseenter";
};

onUnmounted(() => {
  window.removeEventListener("resize", updateScreenSize);
});

// Custom Transition Hooks for Smooth Height Animation
const beforeEnter = (el) => {
  el.style.height = "0";
  el.style.opacity = "0";
};

const enter = (el) => {
  el.style.transition = "all 0.1s ease";
  el.style.height = el.scrollHeight + "px";
  el.style.opacity = "1";
};

const afterEnter = (el) => {
  el.style.height = "auto";
};

const beforeLeave = (el) => {
  el.style.height = el.scrollHeight + "px";
  el.style.opacity = "1";
};

const leave = (el) => {
  el.style.transition = "all 0.1s ease";
  // Force reflow to ensure the transition occurs
  void el.offsetHeight;
  el.style.height = "0";
  el.style.opacity = "0";
};

const afterLeave = (el) => {
  el.style.height = "";
};
</script>

<template>
  <div class="flex h-screen bg-gray-100 dark:bg-gray-900 relative transition-colors duration-900">
    <!-- Sidebar -->
    <aside
      :class="{
        '-translate-x-full': !sidebarVisible && isMobile,
        'translate-x-0': sidebarVisible && isMobile,
        'w-64 fixed top-0 bottom-0': sidebarVisible || sidebarHovered || isMobile,
        'w-20 fixed top-0 bottom-0': !sidebarVisible && !isMobile,
        'fixed inset-y-0 left-0 transform z-50 transition-all duration-300 ease-in-out': isMobile,
      }"
      @mouseenter="handleAsideHover"
      @mouseleave="handleAsideHover"
      class="transition-width duration-300 ease-in-out flex flex-col overflow-y-auto text-sm bg-gray-800 dark:bg-gray-900 text-gray-300"
    >
      <div class="h-[70px] flex" :class="{'justify-between': isMobile, 'justify-center': !isMobile}">
        <div class="flex gap-4 items-center justify-center h-full">
          <img src="/img/logo.png" class="w-[48px] rounded-full" alt="Logo" />
          <transition name="fade">
            <h1
              v-if="sidebarVisible || sidebarHovered"
              class="text-xl font-bold tracking-wide truncate"
              style="font-family: transity"
            >
              SITS
            </h1>
          </transition>
        </div>
        <button v-if="isMobile" @click="sidebarVisible = false">
          <XMarkIcon class="w-8 h-8 text-gray-200 hover:text-red-500 transition" />
        </button>
      </div>

      <nav class="pt-1 text-sm font-medium">
  <Link
    href="/dashboard"
    class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-700"
  >
    <HomeIcon class="w-8 h-8 p-1 rounded-full" />
    <transition name="fade">
      <span
        v-if="sidebarVisible || sidebarHovered"
        class="transition-all duration-300 truncate"
      >
        Dashboard
      </span>
    </transition>
  </Link>

  <div>
    <h2 class="font-bold px-4 text-sm">Menu</h2>

    <!-- Students Navigation Item -->
    <button
      @click="openMenus.studentsMenu = !openMenus.studentsMenu"
      :class="{
        'border-l-4 border-blue-700 box-border': openMenus.studentsMenu
      }"
      class="w-full flex items-center justify-between px-4 py-1 hover:bg-gray-700"
    >
      <div class="flex items-center space-x-3">
        <UsersIcon class="w-7 p-1" />
        <transition name="fade">
          <span v-if="sidebarVisible || sidebarHovered" class="text-sm">
            Students
          </span>
        </transition>
      </div>
      <component
        :is="openMenus.studentsMenu ? ChevronUpIcon : ChevronDownIcon"
        class="w-5 h-5 text-gray-200"
      />
    </button>
    <transition
      @before-enter="beforeEnter"
      @enter="enter"
      @after-enter="afterEnter"
      @before-leave="beforeLeave"
      @leave="leave"
      @after-leave="afterLeave"
    >
      <div
        v-if="openMenus.studentsMenu && (sidebarVisible || sidebarHovered)"
        class="space-y-2 rounded-md p-2"
      >
        <Link
          :href="route('students.create')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Add Student</span>
        </Link>
        <Link
          :href="route('students.index')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <ClipboardDocumentListIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Manage Students</span>
        </Link>
      </div>
    </transition>

    <!-- Courses Navigation Item -->
    <button
      @click="openMenus.coursesMenu = !openMenus.coursesMenu"
      :class="{
        'border-l-4 border-blue-700 box-border': openMenus.coursesMenu
      }"
      class="w-full flex items-center justify-between px-4 py-1 hover:bg-gray-700"
    >
      <div class="flex items-center space-x-3">
        <TableCellsIcon class="w-7 p-1" />
        <transition name="fade">
          <span
            v-if="sidebarVisible || sidebarHovered"
            class="text-sm truncate"
          >
            Courses
          </span>
        </transition>
      </div>
      <component
        :is="openMenus.coursesMenu ? ChevronUpIcon : ChevronDownIcon"
        class="w-5 h-5 text-gray-200"
      />
    </button>
    <transition
      @before-enter="beforeEnter"
      @enter="enter"
      @after-enter="afterEnter"
      @before-leave="beforeLeave"
      @leave="leave"
      @after-leave="afterLeave"
    >
      <div
        v-if="openMenus.coursesMenu && (sidebarVisible || sidebarHovered)"
        class="space-y-2 rounded-md p-2"
      >
        <Link
          :href="route('courses.create')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Add Course</span>
        </Link>
        <Link
          :href="route('courses.index')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <ClipboardDocumentListIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Manage Courses</span>
        </Link>
      </div>
    </transition>

    <!-- Teachers Navigation Item -->
    <button
      @click="openMenus.teachersMenu = !openMenus.teachersMenu"
      :class="{
        'border-l-4 border-blue-700 box-border': openMenus.teachersMenu
      }"
      class="w-full flex items-center justify-between px-4 py-1 hover:bg-gray-700"
    >
      <div class="flex items-center space-x-3">
        <BuildingStorefrontIcon class="w-7 p-1" />
        <transition name="fade">
          <span
            v-if="sidebarVisible || sidebarHovered"
            class="text-sm truncate"
          >
            Teachers
          </span>
        </transition>
      </div>
      <component
        :is="openMenus.teachersMenu ? ChevronUpIcon : ChevronDownIcon"
        class="w-5 h-5 text-gray-200"
      />
    </button>
    <transition
      @before-enter="beforeEnter"
      @enter="enter"
      @after-enter="afterEnter"
      @before-leave="beforeLeave"
      @leave="leave"
      @after-leave="afterLeave"
    >
      <div
        v-if="openMenus.teachersMenu && (sidebarVisible || sidebarHovered)"
        class="space-y-2 rounded-md p-2"
      >
        <Link
          :href="route('teachers.create')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Add Teacher</span>
        </Link>
        <Link
          :href="route('teachers.index')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <ClipboardDocumentListIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Manage Teachers</span>
        </Link>
      </div>
    </transition>

    <!-- Attendance Navigation Item -->
    <button
      @click="openMenus.attendanceMenu = !openMenus.attendanceMenu"
      :class="{
        'border-l-4 border-blue-700 box-border': openMenus.attendanceMenu
      }"
      class="w-full flex items-center justify-between px-4 py-1 hover:bg-gray-700"
    >
      <div class="flex items-center space-x-3">
        <ChartBarIcon class="w-7 p-1" />
        <transition name="fade">
          <span
            v-if="sidebarVisible || sidebarHovered"
            class="text-sm truncate"
          >
            Attendance
          </span>
        </transition>
      </div>
      <component
        :is="openMenus.attendanceMenu ? ChevronUpIcon : ChevronDownIcon"
        class="w-5 h-5 text-gray-200"
      />
    </button>
    <transition
      @before-enter="beforeEnter"
      @enter="enter"
      @after-enter="afterEnter"
      @before-leave="beforeLeave"
      @leave="leave"
      @after-leave="afterLeave"
    >
      <div
        v-if="openMenus.attendanceMenu && (sidebarVisible || sidebarHovered)"
        class="space-y-2 rounded-md p-2"
      >
        <Link
          :href="route('attendance.create')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Take Attendance</span>
        </Link>
        <Link
          :href="route('attendance.index')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <ClipboardDocumentListIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">View Attendance</span>
        </Link>
      </div>
    </transition>

    <!-- Exams Navigation Item -->
    <button
      @click="openMenus.examsMenu = !openMenus.examsMenu"
      :class="{
        'border-l-4 border-blue-700 box-border': openMenus.examsMenu
      }"
      class="w-full flex items-center justify-between px-4 py-1 hover:bg-gray-700"
    >
      <div class="flex items-center space-x-3">
        <DocumentTextIcon class="w-7 p-1" />
        <transition name="fade">
          <span
            v-if="sidebarVisible || sidebarHovered"
            class="text-sm truncate"
          >
            Exams
          </span>
        </transition>
      </div>
      <component
        :is="openMenus.examsMenu ? ChevronUpIcon : ChevronDownIcon"
        class="w-5 h-5 text-gray-200"
      />
    </button>
    <transition
      @before-enter="beforeEnter"
      @enter="enter"
      @after-enter="afterEnter"
      @before-leave="beforeLeave"
      @leave="leave"
      @after-leave="afterLeave"
    >
      <div
        v-if="openMenus.examsMenu && (sidebarVisible || sidebarHovered)"
        class="space-y-2 rounded-md p-2"
      >
        <Link
          :href="route('exams.create')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Schedule Exam</span>
        </Link>
        <Link
          :href="route('exams.index')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <ClipboardDocumentListIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Manage Exams</span>
        </Link>
      </div>
    </transition>

    <!-- Permissions Navigation Item -->
    <button
      @click="openMenus.permissionsMenu = !openMenus.permissionsMenu"
      :class="{
        'border-l-4 border-blue-700 box-border': openMenus.permissionsMenu
      }"
      class="w-full flex items-center justify-between px-4 py-1 hover:bg-gray-700"
    >
      <div class="flex items-center space-x-3">
        <Cog6ToothIcon class="w-7 p-1" />
        <transition name="fade">
          <span
            v-if="sidebarVisible || sidebarHovered"
            class="text-sm truncate"
          >
            Permissions
          </span>
        </transition>
      </div>
      <component
        :is="openMenus.permissionsMenu ? ChevronUpIcon : ChevronDownIcon"
        class="w-5 h-5 text-gray-200"
      />
    </button>
    <transition
      @before-enter="beforeEnter"
      @enter="enter"
      @after-enter="afterEnter"
      @before-leave="beforeLeave"
      @leave="leave"
      @after-leave="afterLeave"
    >
      <div
        v-if="openMenus.permissionsMenu && (sidebarVisible || sidebarHovered)"
        class="space-y-2 rounded-md p-2"
      >
        <Link
          :href="route('roles.index')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <ClipboardDocumentListIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Manage Roles</span>
        </Link>
        <Link
          :href="route('permissions.index')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Manage Permissions</span>
        </Link>
      </div>
    </transition>

    <!-- Departments Navigation Item -->
    <button
      @click="openMenus.departmentsMenu = !openMenus.departmentsMenu"
      :class="{
        'border-l-4 border-blue-700 box-border': openMenus.departmentsMenu
      }"
      class="w-full flex items-center justify-between px-4 py-1 hover:bg-gray-700"
    >
      <div class="flex items-center space-x-3">
        <DocumentIcon class="w-7 p-1" />
        <transition name="fade">
          <span
            v-if="sidebarVisible || sidebarHovered"
            class="text-sm truncate"
          >
            Departments
          </span>
        </transition>
      </div>
      <component
        :is="openMenus.departmentsMenu ? ChevronUpIcon : ChevronDownIcon"
        class="w-5 h-5 text-gray-200"
      />
    </button>
    <transition
      @before-enter="beforeEnter"
      @enter="enter"
      @after-enter="afterEnter"
      @before-leave="beforeLeave"
      @leave="leave"
      @after-leave="afterLeave"
    >
      <div
        v-if="openMenus.departmentsMenu && (sidebarVisible || sidebarHovered)"
        class="space-y-2 rounded-md p-2"
      >
        <Link
          :href="route('departments.index')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <ClipboardDocumentListIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Manage Departments</span>
        </Link>
        <Link
          :href="route('departments.create')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Add Departments</span>
        </Link>
      </div>
    </transition>

    <!-- Programs Navigation Item -->
    <button
      @click="openMenus.programsMenu = !openMenus.programsMenu"
      :class="{
        'border-l-4 border-blue-700 box-border': openMenus.programsMenu
      }"
      class="w-full flex items-center justify-between px-4 py-1 hover:bg-gray-700"
    >
      <div class="flex items-center space-x-3">
        <DocumentIcon class="w-7 p-1" />
        <transition name="fade">
          <span
            v-if="sidebarVisible || sidebarHovered"
            class="text-sm truncate"
          >
            Programs
          </span>
        </transition>
      </div>
      <component
        :is="openMenus.programsMenu ? ChevronUpIcon : ChevronDownIcon"
        class="w-5 h-5 text-gray-200"
      />
    </button>
    <transition
      @before-enter="beforeEnter"
      @enter="enter"
      @after-enter="afterEnter"
      @before-leave="beforeLeave"
      @leave="leave"
      @after-leave="afterLeave"
    >
      <div
        v-if="openMenus.programsMenu && (sidebarVisible || sidebarHovered)"
        class="space-y-2 rounded-md p-2"
      >
        <Link
          :href="route('programs.index')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <ClipboardDocumentListIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Manage Programs</span>
        </Link>
        <Link
          :href="route('programs.create')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Add Programs</span>
        </Link>
      </div>
    </transition>

    <!-- Employees Navigation Item -->
    <button
      @click="openMenus.employeesMenu = !openMenus.employeesMenu"
      :class="{
        'border-l-4 border-blue-700 box-border': openMenus.employeesMenu
      }"
      class="w-full flex items-center justify-between px-4 py-1 hover:bg-gray-700"
    >
      <div class="flex items-center space-x-3">
        <DocumentIcon class="w-7 p-1" />
        <transition name="fade">
          <span
            v-if="sidebarVisible || sidebarHovered"
            class="text-sm truncate"
          >
            Employees
          </span>
        </transition>
      </div>
      <component
        :is="openMenus.employeesMenu ? ChevronUpIcon : ChevronDownIcon"
        class="w-5 h-5 text-gray-200"
      />
    </button>
    <transition
      @before-enter="beforeEnter"
      @enter="enter"
      @after-enter="afterEnter"
      @before-leave="beforeLeave"
      @leave="leave"
      @after-leave="afterLeave"
    >
      <div
        v-if="openMenus.employeesMenu && (sidebarVisible || sidebarHovered)"
        class="space-y-2 rounded-md p-2"
      >
        <Link
          :href="route('employees.index')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <ClipboardDocumentListIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Manage Employees</span>
        </Link>
        <Link
          :href="route('employees.create')"
          class="flex items-center px-4 py-2 hover:bg-gray-700 rounded"
        >
          <PlusIcon class="w-4 h-5 mr-2 text-gray-200" />
          <span class="text-sm">Add Employees</span>
        </Link>
      </div>
    </transition>

    <!-- Profile Navigation Item -->
    <Link
      :href="route('profile.edit')"
      class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-700"
    >
      <UsersIcon class="w-8 h-8 text-gray-200 p-1 rounded-full" />
      <transition name="fade">
        <span
          v-if="sidebarVisible || sidebarHovered"
          class="transition-all duration-300 truncate text-sm"
        >
          My Profile
        </span>
      </transition>
    </Link>
  </div>
</nav>

    </aside>

    <!-- Mobile Sidebar Overlay -->
    <div
      v-if="isMobile && sidebarVisible"
      class="fixed inset-0 bg-black bg-opacity-50 z-40"
      @click="sidebarVisible = false"
    ></div>

    <!-- Main Content -->
    <div
      class="flex-1 flex flex-col transition-margin duration-300 overflow-x-hidden"
      :class="{
        'ml-64': !isMobile && sidebarVisible,
        'ml-20': !isMobile && !sidebarVisible,
      }"
    >
      <!-- Navbar -->
      <nav
        class="border-b border-gray-300 dark:border-gray-700 px-4 py-4 flex justify-between items-center bg-gray-800 text-gray-200"
      >
        <div class="flex items-center gap-3">
          <!-- Back Button -->
          <button
            @click="goBack"
            class="p-2 hover:bg-gray-700 rounded-md"
          >
            <ArrowLeftIcon class="w-6 h-6 text-gray-200" />
          </button>

          <!-- Forward Button -->
          <button
            @click="goForward"
            class="p-2 hover:bg-gray-700 rounded-md"
          >
            <ArrowRightIcon class="w-6 h-6 text-gray-200" />
          </button>

          <!-- Sidebar Toggler -->
          <button @click="sidebarVisible = !sidebarVisible">
            <Bars3Icon class="w-6 h-6 text-gray-200" />
          </button>
        </div>

        <!-- Other Navbar Content -->
        <div class="flex items-center gap-4 ml-auto">
          <!-- User Info -->
          <div class="text-sm font-medium text-gray-200 flex items-center gap-2">
            <img src="images/mlane.jpg" alt="Avatar" class="w-10 h-10 rounded-full" />
            <div>
              <div class="text-base">{{ $page.props.auth.user.name }}</div>
              <div class="text-xs">{{ $page.props.auth.user.email }}</div>
            </div>
          </div>
          <!-- Theme Toggle Button -->
          <button @click="toggleTheme" class="p-2">
            <MoonIcon v-if="!isDarkMode" class="w-6 h-6 text-gray-200" />
            <SunIcon v-else class="w-6 h-6 text-gray-200" />
          </button>
        </div>
      </nav>

      <!-- Page Content -->
      <main class="flex-1 p-6 bg-gray-100 dark:bg-gray-900">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.1s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.fade-enter-to,
.fade-leave-from {
  opacity: 1;
}
</style>
