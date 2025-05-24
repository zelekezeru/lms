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
import * as XLSX from 'xlsx'
import { saveAs } from 'file-saver'
import { computed } from 'vue'

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

// Export to Excel
const exportToExcel = () => {
  if (!students?.data?.length) return

  const exportData = students.data.map((student, index) => ({
    'No.': index + 1 + ((students.meta.current_page - 1) * students.meta.per_page),
    'Full Name': `${student.firstName} ${student.middleName} ${student.lastName}`,
    'ID Number': student.idNo,
    'Phone': student.mobilePhone,
  }))

  const worksheet = XLSX.utils.json_to_sheet(exportData)
  const workbook = XLSX.utils.book_new()

  XLSX.utils.book_append_sheet(workbook, worksheet, 'Students')

  const excelBuffer = XLSX.write(workbook, {
    bookType: 'xlsx',
    type: 'array',
  })

  const blob = new Blob([excelBuffer], {
    type:
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8',
  })

  saveAs(blob, `Students_List_${new Date().toISOString().slice(0, 10)}.xlsx`)
}


</script>

<template>
    <!-- Export list to Excel -->
     <a :href="route('students.export')" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded">
        Export All to Excel
    </a>

    <div class="overflow-x-auto shadow-md sm:rounded-lg mt-3">
        <!-- Export list to Excel -->

        <Table>
            <TableHeader>
                <tr>
                    <Thead>No.</Thead>
                    <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'firstName'">Student Name</Thead>
                    <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'idNo'">ID Number</Thead>
                    <Thead :sortable="true" :sort-info="sortInfo" :sortColumn="'mobilePhone'">Phone</Thead>
                    <Thead>Actions</Thead>
                </tr>
            </TableHeader>
            <tbody>
                <TableZebraRows
                    v-for="(student, index) in students.data"
                    :key="student.id"
                >
                    <td class="px-6 py-4">
                        {{ index + 1 + ((students.meta.current_page - 1) * students.meta.per_page) }}
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <Link :href="route('students.show', { student: student.id })">
                            {{ student.firstName }} {{ student.middleName }} {{ student.lastName }}
                        </Link>
                    </th>
                    <td class="px-6 py-4">{{ student.idNo }}</td>
                    <td class="px-6 py-4">{{ student.mobilePhone }}</td>
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