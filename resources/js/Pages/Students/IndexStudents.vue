<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    students: Array,
});

const remove = (student) => {
    router.delete(route('students.destroy', student.id));
};

onMounted(() => {
    console.log(props.students.length);
});
</script>

<template>
    <Head title="Students" />

    <AppLayout>
        <template #header>
            <div class="pt-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Students List
                </h2>
            </div>
        </template>
        
        <div class="flex flex-wrap">

            <a :href="route('students.create')" class="absolute px-4 py-2 text-white rounded-lg bg-slate-900 top-4 right-32">Create Student</a>
            <div class="flex justify-center py-40 overflow-hidden bg-white shadow-sm sm:rounded-lg space-x-11">
                <ul class="flex-1 px-8 list-none" v-if="props.students.length">
                    <li v-for="student in props.students" :key="student.id" class="border-b-2">
                        <div class="grid grid-cols-5">
                            <p class="col-span-3 text-lg">{{ student.name }}</p>
                            <div class="grid grid-cols-2">
                                <a class="px-4 text-center text-white bg-slate-900" :href="route('students.edit', student.id)">Edit</a>
                                <button @click="remove(student)" class="px-4 text-center text-white bg-red-600">Delete</button>
                            </div>
                        </div>
                    </li>
                </ul>
                <p v-else>No students yet</p>
            </div>
        </div>
                                
    </AppLayout>
</template>