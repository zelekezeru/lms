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
                <div class="user">                    
                    <div class="info">
                        <a class="collapsed" data-toggle="collapse" href="#profileMenu">
                            <span>
                                Admin
                                <span class="user-level">Administrator</span>
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
                        <h4 class="text-section">College Management</h4>
                    </li>
                    
                    <!-- Students -->
                    <li class="nav-item">
                        <a href="#" @click.prevent="toggleDropdown('students')">
                            <i class="fas fa-users"></i>
                            <p>Students</p>
                            <span class="caret"></span>
                        </a>
                        <div v-if="showingDropdowns.students" class="submenu">
                            <DropdownLink>Add Student</DropdownLink>
                            <DropdownLink>Manage Students</DropdownLink>
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
                            <DropdownLink>Add Course</DropdownLink>
                            <DropdownLink>Manage Courses</DropdownLink>
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
                            <DropdownLink>Add Teacher</DropdownLink>
                            <DropdownLink>Manage Teachers</DropdownLink>
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
                            <DropdownLink>Take Attendance</DropdownLink>
                            <DropdownLink>View Attendance</DropdownLink>
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
                            <DropdownLink>Schedule Exam</DropdownLink>
                            <DropdownLink>Manage Exams</DropdownLink>
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


