<script setup>
import AdminSidebar from "./AdminSidebar.vue";
import FinanceSidebar from "./FinanceSidebar.vue";
import InstructorSidebar from "./InstructorSidebar.vue";
import RegistrarSidebar from "./RegistrarSidebar.vue";
import StudentSidebar from "./StudentSidebar.vue";

const props = defineProps({
    activeRole: {
        type: String,
        required: true,
    },
    isOpen: Boolean,
    isMobile: Boolean,
});

let sidebarComponent = null;

switch (props.activeRole) {
    case "SUPER-ADMIN":
        sidebarComponent = AdminSidebar;
        break;
    case "ADMIN":
        sidebarComponent = AdminSidebar;
        break;
    case "INSTRUCTOR":
        sidebarComponent = InstructorSidebar;
        break;
    case "REGISTRAR":
        sidebarComponent = RegistrarSidebar;
        break;
    case "FINANCE-ADMIN":
        sidebarComponent = FinanceSidebar;
        break;
    case "FINANCE-USER":
        sidebarComponent = FinanceSidebar;
        break;
    case "STUDENT":
        sidebarComponent = StudentSidebar;
        break;

    default:
        console.warn("No layout set for role: " + props.activeRole);
        break;
}
</script>

<template>
    <component :is="sidebarComponent" :is-open="isOpen" :is-mobile="isMobile" />
</template>
