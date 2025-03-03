<script>
import { Link } from '@inertiajs/vue3'; // Import the Link component
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

export default {
    components: {
        Link, // Register the Link component
        Dropdown,
        DropdownLink,
    },
    methods: {
        // Add any methods you need here
    }
}
</script>

<template>
    <!-- Sidebar -->
    <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
                <Link :href="route('dashboard')" class="logo">
                    <img src="/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                </Link>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar">
                        <i class="gg-menu-right"></i>
                    </button>
                    <button class="btn btn-toggle sidenav-toggler">
                        <i class="gg-menu-left"></i>
                    </button>
                </div>
                <button class="topbar-toggler more">
                    <i class="gg-more-vertical-alt"></i>
                </button>
            </div>
            <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
            <div class="sidebar-content">
                <ul class="nav nav-secondary">
                    <!-- Dashboard Link -->
                    <li class="nav-item active">
                        <Link :href="route('dashboard')">
                            <i class="fas fa-home"></i>
                            <p>Dashboard</p>
                        </Link>
                    </li>

                    <!-- Section Title -->
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">College Management</h4>
                    </li>

                    <!-- Students Management -->
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#students">
                            <i class="fas fa-users"></i>
                            <p>Students</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="students">
                            <ul class="nav nav-collapse">
                                <li>
                                    <Link >
                                        Add Student
                                    </Link>
                                </li>
                                <li>
                                    <Link >
                                        Manage Students
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Courses Management -->
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#courses">
                            <i class="fas fa-book"></i>
                            <p>Courses</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="courses">
                            <ul class="nav nav-collapse">
                                <li>
                                    <Link >
                                        Add Course
                                    </Link>
                                </li>
                                <li>
                                    <Link >
                                        Manage Courses
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Teachers Management -->
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#teachers">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <p>Teachers</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="teachers">
                            <ul class="nav nav-collapse">
                                <li>
                                    <Link >
                                        Add Teacher
                                    </Link>
                                </li>
                                <li>
                                    <Link >
                                        Manage Teachers
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Attendance Management -->
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#attendance">
                            <i class="fas fa-calendar-check"></i>
                            <p>Attendance</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="attendance">
                            <ul class="nav nav-collapse">
                                <li>
                                    <Link >
                                        Take Attendance
                                    </Link>
                                </li>
                                <li>
                                    <Link >
                                        View Attendance
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Exams Management -->
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#exams">
                            <i class="fas fa-clipboard-list"></i>
                            <p>Exams</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="exams">
                            <ul class="nav nav-collapse">
                                <li>
                                    <Link >
                                        Schedule Exam
                                    </Link>
                                </li>
                                <li>
                                    <Link >
                                        Manage Exams
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Profile Link -->
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
    <!-- End Sidebar -->
</template>
