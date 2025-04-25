<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";
import { EyeIcon, TrashIcon } from "@heroicons/vue/24/solid";
import { PencilSquareIcon } from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableZebraRows from "@/Components/TableZebraRows.vue";
import Thead from "@/Components/Thead.vue";

defineProps({
    students: {
        type: Object,
        required: true,
    },
    sortInfo: {
        type: Object,
        required: false,
    },
    deleteStudent: {
        type: Function,
        required: true,
    },
    search: {
        type: String,
        required: false,
    },
});
</script>

<template>
    <div class="overflow-x-auto shadow-md sm:rounded-lg mt-3">
        <Table>
            <TableHeader>
                <tr>
                    <Thead>No.</Thead>
                    <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'first_name'">Student Name</Thead>
                    <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'id_no'">ID Number</Thead>
                    <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'mobile_phone'">Phone</Thead>
                    <Thead>Actions</Thead>
                </tr>
            </TableHeader>
            <tbody>
                <TableZebraRows
                    v-for="(student, index) in students.data"
                    :key="student.id"
                >
                    <td class="px-6 py-4">{{ index + 1 + (students.meta.current_page - 1) * students.meta.per_page }}</td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <Link :href="route('students.show', { student: student.id })">
                            {{ student.first_name }} {{ student.middle_name }} {{ student.last_name }}
                        </Link>
                    </th>
                    <td class="px-6 py-4">{{ student.id_no }}</td>
                    <td class="px-6 py-4">{{ student.mobile_phone }}</td>
                    <td class="px-6 py-4 flex space-x-6">
                        <div v-if="userCan('view-students')">
                            <Link :href="route('students.show', { student: student.id })" class="text-blue-500 hover:text-blue-700">
                                <EyeIcon class="w-5 h-5" />
                            </Link>
                        </div>
                        <div v-if="userCan('update-students')">
                            <Link :href="route('students.edit', { student: student.id })" class="text-green-500 hover:text-green-700">
                                <PencilSquareIcon class="w-5 h-5" />
                            </Link>
                        </div>
                        <div v-if="userCan('delete-students')">
                            <button @click="deletestudent(student.id)" class="text-red-500 hover:text-red-700">
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </td>
                </TableZebraRows>
            </tbody>
        </Table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-3 flex justify-center space-x-6">
        <Link
            v-for="link in students.meta.links"
            :key="link.label"
            :href="link.url ? `${link.url}&search=${search}` : '#'"
            class="p-2 px-4 text-sm font-medium rounded-lg transition-colors"
            :class="{
                'text-gray-700 dark:text-gray-400': true,
                'cursor-not-allowed opacity-50': !link.url,
                '!bg-gray-100 !dark:bg-gray-800': link.active,
            }"
            v-html="link.label"
        />
    </div>
</template>