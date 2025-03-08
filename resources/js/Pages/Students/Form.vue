<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({ form: Object });
const emit = defineEmits(['submit']);
</script>

<template>
    <form @submit.prevent="emit('submit')" class="bg-white p-4 shadow rounded">
        <div class="flex mb-4">
            <div class="w-1/2 pr-2">
                <label class="block text-gray-700">Student Name</label>
                <input v-model="form.student_name" class="w-full border rounded p-2" placeholder="Full Name" />
            </div>
            <div class="w-1/2 pl-2">
                <label class="block text-gray-700">Father's Name</label>
                <input v-model="form.father_name" class="w-full border rounded p-2" placeholder="Father's Name" />
            </div>
        </div>

        <div class="flex mb-4">
            <div class="w-1/2 pr-2">
                <label class="block text-gray-700">Grandfather's Name</label>
                <input v-model="form.grand_father_name" class="w-full border rounded p-2" placeholder="Grandfather's Name" />
            </div>
            <div class="w-1/2 pl-2">
                <label class="block text-gray-700">Student ID</label>
                <input v-model="form.student_id" class="w-full border rounded p-2" placeholder="Unique ID" />
            </div>
        </div>

        <div class="flex mb-4">
            <div class="w-1/2 pr-2">
                <label class="block text-gray-700">Mobile Phone</label>
                <input v-model="form.mobile_phone" class="w-full border rounded p-2" placeholder="Mobile Phone Number" />
            </div>
            <div class="w-1/2 pl-2">
                <label class="block text-gray-700">Office Phone</label>
                <input v-model="form.office_phone" class="w-full border rounded p-2" placeholder="Office Phone Number" />
            </div>
        </div>

        <div class="flex mb-4">
            <div class="w-1/2 pr-2">
                <label class="block text-gray-700">Email</label>
                <input v-model="form.email" class="w-full border rounded p-2" placeholder="Email address" />
            </div>
            <div class="w-1/2 pl-2">
                <label class="block text-gray-700">Date of Birth</label>
                <input v-model="form.date_of_birth" type="date" class="w-full border rounded p-2" />
            </div>
        </div>

        <div class="flex mb-4">
            <div class="w-1/2 pr-2">
                <label class="block text-gray-700">Marital Status</label>
                <select v-model="form.marital_status" class="w-full border rounded p-2">
                    <option value="Choose" disabled selected>Select Marital Status</option>
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="divorced">Divorced</option>
                    <option value="widowed">Widowed</option>
                </select>
            </div>
            <div class="w-1/2 pl-2">
                <label class="block text-gray-700">Sex</label>
                <select v-model="form.sex" class="w-full border rounded p-2">
                    <option value="" disabled>Select Sex</option>
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>
        </div>

        <div class="flex mb-4">
            <div class="w-1/2 pr-2">
                <label class="block text-gray-700">Academic Year</label>
                <input v-model="form.academic_year" class="w-full border rounded p-2" placeholder="e.g., 2024" />
            </div>
            <div class="w-1/2 pl-2">
                <label class="block text-gray-700">Program</label>
                <input v-model="form.program" class="w-full border rounded p-2" placeholder="e.g., Degree" />
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Year of Study</label>
            <input v-model="form.year_of_study" class="w-full border rounded p-2" placeholder="e.g., First" />
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Pastor's Name</label>
            <input v-model="form.pastor_name" class="w-full border rounded p-2" placeholder="Pastor's Full Name" />
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Pastor's Phone</label>
            <input v-model="form.pastor_phone" class="w-full border rounded p-2" placeholder="Pastor Phone Number" />
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Address</label>
            <input v-model="form.address_1" class="w-full border rounded p-2" placeholder="Primary Address" />
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Position/Denomination</label>
            <input v-model="form.position_denomination" class="w-full border rounded p-2" placeholder="e.g., Title/Denomination" />
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Office Use Notes</label>
            <textarea v-model="form.office_use_notes" class="w-full border rounded p-2" placeholder="Internal Notes"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</template>
