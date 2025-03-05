<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import DropdownLink from "@/Components/DropdownLink.vue";

const showingDropdowns = ref({
    students: false,
    courses: false,
    teachers: false,
    attendance: false,
    exams: false,
});

const toggleDropdown = (key) => {
    showingDropdowns.value[key] = !showingDropdowns.value[key];
};
</script>

<template>
    <div class="sidebar" data-background-color="dark">
        <div class="sidebar-wrapper">
            <div class="sidebar-content">
                <div class="user justify-center">
                    <img src="/img/logo.png" alt="Logo" class="user-logo" />
                    <div class="info">
                        <a class="collapsed" data-toggle="collapse" href="#profileMenu">
                            <span>     
                                <slot />
                            </span>
                        </a>
                    </div>
                </div>
                <ul class="nav">
                    <li class="nav-item active">
                        <Link :href="route('dashboard')">
                            <i class="fas fa-home"></i>
                            <p>Dashboard</p>
                        </Link>
                    </li>
                    <li class="nav-section">
                        <h4 class="text-section">Institution Management</h4>
                    </li>
                    
                    <!-- Students -->
                    <li class="nav-item">
                        <a href="#" @click.prevent="toggleDropdown('students')">
                            <i class="fas fa-users"></i>
                            <p>Students</p>
                        </a>
                        <div v-if="showingDropdowns.students" class="submenu">
                            <DropdownLink :href="route('students.create')" class="submenu-item">Add Student</DropdownLink>
                            <DropdownLink :href="route('students.index')" class="submenu-item">Manage Students</DropdownLink>
                        </div>
                    </li>
                    
                    <!-- Courses -->
                    <li class="nav-item">
                        <a href="#" @click.prevent="toggleDropdown('courses')">
                            <i class="fas fa-book"></i>
                            <p>Courses</p>
                            <span class="caret"></span>
                        </a>
                        <div v-if="showingDropdowns.courses" class="submenu">
                            <DropdownLink class="submenu-item">Add Course</DropdownLink>
                            <DropdownLink class="submenu-item">Manage Courses</DropdownLink>
                        </div>
                    </li>
                    
                    <!-- Teachers -->
                    <li class="nav-item">
                        <a href="#" @click.prevent="toggleDropdown('teachers')">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <p>Teachers</p>
                            <span class="caret"></span>
                        </a>
                        <div v-if="showingDropdowns.teachers" class="submenu">
                            <DropdownLink class="submenu-item">Add Teacher</DropdownLink>
                            <DropdownLink class="submenu-item">Manage Teachers</DropdownLink>
                        </div>
                    </li>
                    
                    <!-- Attendance -->
                    <li class="nav-item">
                        <a href="#" @click.prevent="toggleDropdown('attendance')">
                            <i class="fas fa-calendar-check"></i>
                            <p>Attendance</p>
                            <span class="caret"></span>
                        </a>
                        <div v-if="showingDropdowns.attendance" class="submenu">
                            <DropdownLink class="submenu-item">Take Attendance</DropdownLink>
                            <DropdownLink class="submenu-item">View Attendance</DropdownLink>
                        </div>
                    </li>
                    
                    <!-- Exams -->
                    <li class="nav-item">
                        <a href="#" @click.prevent="toggleDropdown('exams')">
                            <i class="fas fa-clipboard-list"></i>
                            <p>Exams</p>
                            <span class="caret"></span>
                        </a>
                        <div v-if="showingDropdowns.exams" class="submenu">
                            <DropdownLink class="submenu-item">Schedule Exam</DropdownLink>
                            <DropdownLink class="submenu-item">Manage Exams</DropdownLink>
                        </div>
                    </li>
                    
                    <li class="nav-item">
                        <Link :href="route('profile.edit')">
                            <i class="fas fa-user"></i>
                            <p>My Profile</p>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<style scoped>
.sidebar {
    background: #27293d;
    color: #fff;
    height: 100vh;
    width: 250px; /* Adjust width if needed */
    position: fixed;
    overflow: hidden; /* Prevents the whole sidebar from scrolling */
    display: flex;
    flex-direction: column;
}

.sidebar-wrapper {
    flex-grow: 1;
    padding: 15px;
    scrollbar-width: none; /* Hide scrollbar in Firefox */
}

/* Hide scrollbar in Chrome, Edge, Safari */
.sidebar-wrapper::-webkit-scrollbar {
    display: none;
}


/* Hides scrollbar in Chrome, Safari, Edge */
.sidebar::-webkit-scrollbar {
    display: none;
}
/* Ensure navigation items fit well */
.nav {
    list-style: none;
    padding: 0;
}

.nav-item {
    transition: background 0.3s;
}

.nav-item.active,
.nav-item:hover {
    background: #1f2235;
}

.nav-link {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #fff;
    font-size: 14px;
    gap: 10px;
}

.nav-link i {
    font-size: 16px;
}

.submenu {
    padding-left: 20px;
    background: #1f2235;
    display: flex;
    flex-direction: column;
}

.submenu-item {
    color: #ccc;
    text-decoration: none;
    transition: color 0.3s;
}

.submenu-item:hover {
    color: #f0f0f0;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}

/* User Info */
.user-logo {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-bottom: 10px;
}

.user {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    justify-content: center;
}

</style>
