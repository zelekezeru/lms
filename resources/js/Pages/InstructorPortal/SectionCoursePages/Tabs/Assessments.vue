<script setup>
import ShowResults from "@/Pages/InstructorPortal/Components/ShowResults.vue";

const props = defineProps({
    section: {
        type: Object,
        required: true,
    },
    course: {
        type: Object,
        required: true,
    },
    semester: {
        type: Object,
        required: true,
    },
    instructor: {
        type: Object,
        required: true,
    },
    weights: {
        type: Object,
        required: true,
    },
    students: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <ShowResults
        :section="section"
        :course="course"
        :semester="semester"
        :instructor="instructor"
        :weights="weights"
        :studentsList="students"
    />
</template>
